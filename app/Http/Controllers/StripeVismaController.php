<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use Auth;
use Illuminate\Support\Facades\Input;
use App\Models\Token;
use App\Visma;
use App\Stripe;
use App\StripeWorkflowData;
use App\StripeWorkflow;
use App\Http\Controller\VismaDataController;
use Illuminate\Support\Facades\DB;
use Storage;
use PDF;

class StripeVismaController extends Controller
{
    public function __construct()
    {
        session_start();
        set_time_limit(0);
        ini_set('xdebug.var_display_max_depth', 20000);
        ini_set('xdebug.var_display_max_children', 10000000);
        ini_set('xdebug.var_display_max_data', 10000000);
    }

    public function authAccessToken() {
        
    	$gross_amount_account = $_SESSION["gross_amount_account"];
    	$charges_account = $_SESSION["charges_account"];
    	$net_amount_account = $_SESSION["net_amount_account"];

        $current_user = $_SESSION["user"];
        
        $array = json_decode(Token::where('user_id',$current_user['id'])->orderBy('id','desc')->first());
        $access_token = $array->access_token;

        /*authorize with access token*/
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, env('VISMA_API_ENDPOINT')."/v2/companysettings");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

        $headers = array();
        $headers[] = "Authorization: Bearer ".$access_token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $result = curl_exec($ch);

        if($result != null) {
            /*insert order info*/
            $fields = $this->getPayoutData();

            if($fields != null) {

                
                $i = 0;
                foreach ($fields as $field) {
                	$fee = 0;
	                $net = 0;
	                $gross = 0;
                	foreach ($field as $item) {
                		
                		$fee = $fee + ($item->fee)*0.01;
                		$net = $net + ($item->net)*0.01;

                	}

                	$gross = $fee + $net;
                	
                	/*create a new attachment*/
                    $data = array("ContentType" => 'application/pdf', "FileName" => 'paymentreport.pdf', "Data" => $this->generatePDF($field));
                    $data = (is_array($data)) ? http_build_query($data) : $data; 
             		
                    curl_setopt($ch, CURLOPT_URL, env('VISMA_API_ENDPOINT')."/v2/attachments");
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

					$result = curl_exec($ch);
			
					$result = json_decode($result);
                    $report_id = $result->Id;
					/*end create a new attachment*/

                	$item = $this->postToVisma($gross, $fee, $net, $result->Id);
                    $item = (is_array($item)) ? http_build_query($item) : $item; 

                    curl_setopt($ch, CURLOPT_URL, env('VISMA_API_ENDPOINT')."/v2/vouchers");
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $item);

                    $result = curl_exec($ch);
                    $dt = date('Y-m-d H:i:s');
                    Storage::append((string)$current_user["id"].'.txt', $dt."[Stripe payment #".$report_id."] successfully transferred to Visma"."\n");
                }
              
                if (curl_errno($ch)) {
                    echo 'Error:' . curl_error($ch);
                    Storage::append((string)$current_user["id"].'.txt', "error transferring payment report number "."\t".curl_error($ch)."\t"."]".$dt."\n");
                }
                curl_close ($ch);
                
            }
            /*end insert*/
        }

       return redirect('manageapp')->with('result', 'Profile updated!');
      
    }
/*Refresh token********************************************************************************************************/

    public function authRefreshToken() {

        $gross_amount_account = $_SESSION["gross_amount_account"];
    	$charges_account = $_SESSION["charges_account"];
    	$net_amount_account = $_SESSION["net_amount_account"];

    	$user_id = $_SESSION["user"]["id"];

        $ch = curl_init();

        $tokenArr = json_decode(Token::where('user_id',$user_id)->orderBy('id','desc')->first());
        $refresh_token = $tokenArr->refresh_token; 
        
        $REDIRECT_URI = "https://epti.eratio.com/api/eaccounting/refresh";
        curl_setopt($ch, CURLOPT_URL, env('VISMA_AUTH_ENDPOINT')."/connect/token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "refresh_token=".$refresh_token."&grant_type=refresh_token&redirect_uri=".$REDIRECT_URI);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_USERPWD, env('VISMA_CLIENT_ID') . ":" . env('VISMA_CLIENT_SECRET'));

        $headers = array();
        $headers[] = "Content-Type: application/x-www-form-urlencoded";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {

            echo 'Error:' . curl_error($ch);
        }
        curl_close ($ch);
        /*response data*/
        $response_data = json_decode($result);
        $new_id_token = $response_data->id_token;
        $new_access_token = $response_data->access_token;
        $new_refresh_token = $response_data->refresh_token;

        // Token::where('user_id',$user_id)->update(array('refresh_token'=>$new_refresh_token, 'access_token'=>$new_access_token));
        
        Token::where('user_id',$user_id)->delete();
        DB::table('token_info')->insert([
            'user_id'  => $user_id,
            'refresh_token' => $new_refresh_token,
            'access_token' => $new_access_token,
        ]);  

        $access_token = $new_access_token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, env('VISMA_API_ENDPOINT')."/v2/companysettings");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

        $headers = array();
        $headers[] = "Authorization: Bearer ".$access_token;
        $link[] = "Link";
        array_push($headers, "Link");
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $result = curl_exec($ch);
        if($result != null) {

            /*insert order info*/
            $fields = $this->getPayoutData();

            if($fields != null) {

            	$i = 0;
                foreach ($fields as $field) {

                	$fee   = 0;
	                $net   = 0;
	                $gross = 0;
                	foreach ($field as $item) {
                		
                		$fee = $fee + ($item->fee)*0.01;
                		$net = $net + ($item->net)*0.01;
                	}

                	$gross = $fee + $net;
                	
                    /*create a new attachment*/
                    $data = array("ContentType" => 'application/pdf', "FileName" => 'paymentreport.pdf', "Data" => $this->generatePDF($field));
                    $data = (is_array($data)) ? http_build_query($data) : $data; 
             		
                    curl_setopt($ch, CURLOPT_URL, env('VISMA_API_ENDPOINT')."/v2/attachments");
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

					$result = curl_exec($ch);
			
					$result = json_decode($result);
                    $report_id = $result->Id;
                    // var_dump($result);exit;
					/*end create a new attachment*/

					$item = $this->postToVisma($gross, $fee, $net, $result->Id);
                    $item = (is_array($item)) ? http_build_query($item) : $item; 

                    curl_setopt($ch, CURLOPT_URL, env('VISMA_API_ENDPOINT')."/v2/vouchers");
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $item);
                    $dt = date('Y-m-d H:i:s');
                    $result = curl_exec($ch);
                
                    Storage::append((string)$user_id.'.txt', $dt."[Stripe payment #".$report_id."] successfully transferred to Visma"."\n");
                }
            
	            if (curl_errno($ch)) {
	                echo 'Error:' . curl_error($ch);
                     Storage::append((string)$user_id.'.txt', "error transferring payment report number "."\t".curl_error($ch)."\t"."]".$dt."\n");
	            }
	            curl_close ($ch);
	            
            }
            /*end insert*/
        }
        return redirect('manageapp')->with('result', 'Profile updated!');
    }

    public function postToVisma($gross, $fee, $net, $attachmentId) {

    	$user_id = $_SESSION["user"]["id"];

        $gross_amount_account = unserialize((StripeWorkflowData::where('user_id', $user_id)->first())['gross_amount']);
    	$charges_account = unserialize((StripeWorkflowData::where('user_id', $user_id)->first())['charges']);
    	$net_amount_account = unserialize((StripeWorkflowData::where('user_id', $user_id)->first())['net_amount']);
        $date_string = $gross_amount_account->ModifiedUtc;
        $new = date('Y-m-d',strtotime($date_string));
    
    	$item = array("VoucherDate" => $new, "VoucherText" => "STRIPE TRANSFER", "Rows" => array(
                    
           [ "AccountNumber" => $gross_amount_account->Number, "AccountDescription" => $gross_amount_account->Name, "CreditAmount" => $gross,
           		'CostCenterItemId1' => null,'CostCenterItemId2' => null,'CostCenterItemId3' => null,
               "TransactionText" => $gross_amount_account->TypeDescription, "VatCodeId" => "eaf3eadf-33cc-4579-ab71-e96258afc1d3",'VatCodeAndPercent' => '09 (25%)', "Quantity" => null, "Weight" => null, "DeliveryDate" => null,'HarvestYear' => null,'ProjectId' => null],

           [ "AccountNumber" => $charges_account->Number, "AccountDescription" =>$charges_account->Name , "DebitAmount" => $fee,'CostCenterItemId1' => null,'CostCenterItemId2' => null,'CostCenterItemId3' => null,
            "TransactionText" => $charges_account->TypeDescription, "VatCodeId" => "eaf3eadf-33cc-4579-ab71-e96258afc1d3",'VatCodeAndPercent' => '03 (25%)', "Quantity" => null, "Weight" => null, "DeliveryDate" => null,'HarvestYear' => null,'ProjectId' => null],

           [ "AccountNumber" => $net_amount_account->Number, "AccountDescription" =>$net_amount_account->Name , "DebitAmount" => $net,
                'CostCenterItemId1' => null,'CostCenterItemId2' => null,'CostCenterItemId3' => null,
            "TransactionText" => $net_amount_account->TypeDescription, "VatCodeId" => "eaf3eadf-33cc-4579-ab71-e96258afc1d3",'VatCodeAndPercent' => '09 (25%)', "Quantity" => null, "Weight" => null, "DeliveryDate" => null,'HarvestYear' => null,'ProjectId' => null],
        ), "Attachments.DocumentType" => 2,"Attachments.AttachmentIds" => $attachmentId,
        	 "VoucherType" => 3);

        return $item;
    }
		
    public function getPayoutData() {

        $user = $_SESSION["user"];
        $user_id = $user["id"];
        $payout_data = unserialize((Stripe::where("user_id", $user_id)->first())["stripe_data"]);
      
        return $payout_data;
    }


    public function showLog() {

        $user_id = $_SESSION["user"]["id"];
        if(Storage::exists((string)$user_id.".txt")) {

            $logs = Storage::get((string)$user_id.".txt");
     
            $logs_array = explode("\n", $logs);
        } else {
            
            $logs_array = null;
        }
        
        return view('pages.manageapp.log',["log_array"=>$logs_array]);
    } 

    public function generatePDF($fields) {
    	$details = ['title' => 'test'];
    	// $pdf = PDF::loadView('textDoc');
        $total_amount = 0;
        $total_fee = 0;
        $total_net = 0;
        $no = 1;

        foreach ($fields as $key => $item) {
            $created[$key] = $item->created;
        }

        array_multisort($created, SORT_ASC, $fields);
        // var_dump($fields);exit;

    	$out = '<h3>Transactions</h3>
    	<div style="text-align: center;">
			<table><tbody>
				<tr>
                    <td style="color:red;font-size:18px;text-align:center;border-top:1px solid black;border-left:1px solid black;width:20px;">no</td>

					<td style="color:red;font-size:18px;text-align:center;border-top:1px solid black;border-left:1px solid black;">Type</td>
                    
					<td style="color:red;font-size:18px;text-align:center;border-top:1px solid black;border-left:1px solid black;">Gross</td>
					<td style="color:red;font-size:18px;text-align:center;border-top:1px solid black;border-left:1px solid black;">Fee</td>
					<td style="color:red;font-size:18px;text-align:center;border-top:1px solid black;border-left:1px solid black;">Total</td>
					<td style="color:red;font-size:18px;text-align:center;border-top:1px solid black;border-left:1px solid black;">Buyer</td>
					<td style="color:red;font-size:18px;text-align:center;border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;">Date</td>
				</tr>';
		foreach ($fields as $field) {
			$item = '<tr>
                        <td style="font-size:15px;text-align:center;border-top:1px solid black;border-left:1px solid black;color: gray;">'.$no.'</td>
						<td style="font-size:15px;border-top:1px solid black;border-left:1px solid black;">Payment</td>
						<td style="font-size:15px;border-top:1px solid black;border-left:1px solid black;text-align:right;">'.strtoupper($field->currency).' '.number_format(($field->amount)*0.01, 2, ',', '').'</td>
						<td style="font-size:15px;border-top:1px solid black;border-left:1px solid black;text-align:right;">'.strtoupper($field->currency).' '.number_format(($field->fee)*0.01, 2, ',', '').'</td>
						<td style="font-size:15px;border-top:1px solid black;border-left:1px solid black;text-align:right;"> '.strtoupper($field->currency).' '.number_format(($field->net)*0.01, 2, ',', '').'</td>
						<td style="font-size:15px;border-top:1px solid black;border-left:1px solid black;">'.$field->customer_details.'</td>
						<td style="font-size:15px;border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;">'.date("d", $field->created).'.'.date('m', strtotime(date("M", $field->created))).'.'.date("Y", $field->created).'</td>
					</tr>' ;
			$out = $out.$item;
            $total_amount = $total_amount + ($field->amount)*0.01;
            
            $total_fee = $total_fee + ($field->fee)*0.01;
            
            $total_net = $total_net + ($field->net)*0.01;
            
            $no = $no + 1;
		}
        $total_amount = number_format($total_amount, 2, ',', '');
        $total_fee = number_format($total_fee, 2, ',', '');
        $total_net = number_format($total_net, 2, ',', '');
        $sum = '<tr>
                    <td style="font-size:15px;text-align:center;border-top:1px solid black;border-left:1px solid black;border-bottom:1px solid black;color: gray;">'.($no+1).'</td>
                    <td style="font-size:15px;border-top:1px solid black;border-bottom:1px solid black;border-left:1px solid black;">SUM</td>
                    <td style="font-size:15px;border-top:1px solid black;border-bottom:1px solid black;border-left:1px solid black;text-align:right;">'.strtoupper($field->currency).' '.$total_amount.'</td>
                    <td style="font-size:15px;border-top:1px solid black;border-bottom:1px solid black;border-left:1px solid black;text-align:right;">'.strtoupper($field->currency).' '.$total_fee.'</td>
                    <td style="font-size:15px;border-top:1px solid black;border-bottom:1px solid black;border-left:1px solid black;text-align:right;"> '.strtoupper($field->currency).' '.$total_net.'</td>
                    <td style="font-size:15px;border-top:1px solid black;border-bottom:1px solid black;border-left:1px solid black;"></td>
                    <td style="font-size:15px;border-top:1px solid black;border-bottom:1px solid black;border-left:1px solid black;border-right:1px solid black;"></td>
                </tr>';

		$out = $out.$sum.'</tbody></table></div>';

    	$pdf = PDF::loadHTML($out);

    	// var_dump(base64_encode($pdf->output('mypdf.pdf')));exit;
    	return base64_encode($pdf->output('mypdf.pdf'));
    }
}
