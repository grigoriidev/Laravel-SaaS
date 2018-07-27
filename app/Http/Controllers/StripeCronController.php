<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Stripe;
use App\CurrentUser;
use App\StripeWorkflow;
use App\StripeWorkflowData;
use App\Visma;
use Auth;
use App\Models\Token;
use \Datetime;
use App\Models\User;
use App\StripeVismaSchedule;
use Storage;
use PDF;
class StripeCronController extends Controller
{
    public function __construct()
    {
        session_start();
        set_time_limit(0);
        ini_set('xdebug.var_display_max_depth', 20000);
        ini_set('xdebug.var_display_max_children', 10000000);
        ini_set('xdebug.var_display_max_data', 10000000);
    }

    public function index() {

    	$user_array = User::all();

        $interval_array = [60*5, 60*15, 3600*1, 3600*24, 3600*24*7];

    	foreach ($user_array as $user) {
    		if($user["id"] != 1) {

                $schedule_array = StripeVismaSchedule::where('user_id',$user["id"])->first(); 

                $time_interval = $interval_array[ $schedule_array["time_interval"] ];

                $start_date = strtotime($schedule_array["start_date"]);

                $current_time = strtotime(date("Y-m-d H:i:s"),time());

                $last_time = strtotime($schedule_array["last_run_date"]);

    			// $schedule = $this->Schedule($user["id"]);

                if($current_time >= $start_date) {

                    /*if($last_time == '-0001-11-30 00:00:00') {

                        #for the first running
                        $schedule = $this->Schedule($user["id"]);

                        StripeVismaSchedule::where('user_id',$user["id"])->update(array('last_run_date' => date("Y-m-d H:i:s")));

                    } else {*/

                        if($current_time >= $last_time+$time_interval ) {

                            $schedule = $this->Schedule($user["id"]);
                            if ($schedule == 'notupdate') {
                                continue;
                            }
                            StripeVismaSchedule::where('user_id',$user["id"])->update(array('last_run_date' => date("Y-m-d H:i:s")));
                        }
                   /*}*/

                }
    		}

    	}
    	return redirect('manageapp');
    }

    public function Schedule($id) {

    	$array = Stripe::where('user_id', $id)->first();

    	$stripe_info = unserialize($array['credential']);
    	$public_key = $stripe_info['publickey'];
    	$secret_key = $stripe_info['secretKey'];

    	$ch = curl_init();
    	$stripe_payment_array = [];
    	$stripe_payment_detail_array = [];
    	$x = 0;
    	$end_point = "";
		// curl_setopt($ch, CURLOPT_URL, "https://api.stripe.com/v1/transfers/po_1CZqEvGhLUbHoLlq44sK7qCt/transactions?limit=50");
		while ($x < 10) {

			if ($x == 0) {
				curl_setopt($ch, CURLOPT_URL, "https://api.stripe.com/v1/payouts?limit=100");

			} else {

				curl_setopt($ch, CURLOPT_URL, "https://api.stripe.com/v1/payouts?limit=100&starting_after=".$end_point);
			}

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
			curl_setopt($ch, CURLOPT_USERPWD, $secret_key . ":" . "");

			$result = curl_exec($ch);

			$data = json_decode($result)->data;

			$stripe_payment_array = array_merge($stripe_payment_array, $data);

			if (count($data) < 100) {
				break;
			}

			$end_point = (end($data))->id;

			$x++;
		}
/*live mode all items*/
        $trans_date_array = [];
		foreach($stripe_payment_array as $data) {

			curl_setopt($ch, CURLOPT_URL, "https://api.stripe.com/v1/transfers/" . $data->id . "/transactions?limit=100");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
			curl_setopt($ch, CURLOPT_USERPWD, $secret_key . ":" . "");

			$result_detail = curl_exec($ch);
			$data_detail = json_decode($result_detail)->data;

            $trans_date = $data_detail[0]->created;
            foreach ($data_detail as $value) {
                
                if($trans_date < $value->created) {

                    $trans_date = $value->created;
                }

            }
            
            array_push($trans_date_array, $trans_date);

			array_push($stripe_payment_detail_array, $data_detail);
		}
/*end*/

/*test mode only one item*/
        // curl_setopt($ch, CURLOPT_URL, "https://api.stripe.com/v1/transfers/" . $stripe_payment_array[1]->id . "/transactions?limit=100");
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        // curl_setopt($ch, CURLOPT_USERPWD, $secret_key . ":" . "");

        // $result_detail = curl_exec($ch);
        // $data_detail = json_decode($result_detail)->data;
        
        // array_push($stripe_payment_detail_array, $data_detail);

        // // var_dump(($stripe_payment_detail_array)[0][0]->id);
        // // var_dump(unserialize((Stripe::where('user_id', $user_id)->first())['stripe_data'])[0][0]->id);exit;
    
        // $new = ($stripe_payment_detail_array)[0][0]->id;
        // $old = unserialize((Stripe::where('user_id', $id)->first())['stripe_data'])[0][0]->id;

        // if ($new == $old) {

        //     return 'notupdate';
        // }
/*end*/      
    
		$stripe_table_info = Stripe::all();
        if(count(json_decode($stripe_table_info)) == 0) {
                    
            DB::table('stripe')->insert([
                'user_id'     		=> $id,
                'stripe_data'  		=> serialize($stripe_payment_detail_array),
                'transport_data'    => serialize($stripe_payment_array),
                'transport_number'  => null,
                'transfer_date'     => serialize($trans_date_array),
            ]);

        } else {

            if(Stripe::where('user_id',$id)->first()) {

                Stripe::where('user_id',$id)->update(array('stripe_data' => serialize($stripe_payment_detail_array), 'transport_data' => serialize($stripe_payment_array), 'transfer_date' => serialize($trans_date_array),));

            } else {

                DB::table('stripe')->insert([
                    'user_id'     		=> $id,
                    'stripe_data'  		=> serialize($stripe_payment_detail_array),
                    'transport_data'    => serialize($stripe_payment_array),
                    'transport_number'  => null,
                    'transfer_date'     => serialize($trans_date_array),
                ]);
            }
        }

        /*redirect assignapp page*/

        $array = [];
        if(Token::where('user_id',$id)->orderBy('id','desc')->first()) {

            $array = json_decode(Token::where('user_id', $id)->orderBy('id', 'desc')->first());
            /*date_default_timezone_set("America/Los_Angeles");*/
            /*on the server*/
            // date_default_timezone_set("Europe/Oslo");
            $current_time = strtotime(date("Y-m-d H:i:s"),time());

            $created_time = strtotime($array->created_at);
            $updated_time = strtotime($array->updated_at);

            if($array->updated_at == '-0001-11-30 00:00:00') {
        
                if( $current_time-$created_time <= 3600) {

                	$auth = $this->authAccessToken($id);

                } else {

                    $auth = $this->authRefreshToken($id);
                }
            } else {

                if( $current_time-$updated_time <= 3600 ) {

                    $auth = $this->authAccessToken($id);

                } else {

                    $auth = $this->authRefreshToken($id);
                }   
            }
                
        }

    }

        public function authAccessToken($id) {
        
        $array = json_decode(Token::where('user_id',$id)->orderBy('id','desc')->first());
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
            $fields = $this->getPayoutData($id);

            if($fields != null) {

                $schedule_start_date = strtotime((StripeVismaSchedule::where('user_id',$id)->first())["start_date"]);
                $transport_data = unserialize((Stripe::where('user_id', $id)->first())['transport_data']);
                $transfer_date = unserialize((Stripe::where('user_id', $id)->first())['transfer_date']);
                $i = 0;
                $transport_number_array = [];

                if((Stripe::where('user_id', $id)->first())['transport_number'] == null) {

                    foreach ($fields as $field) {
                        array_push($transport_number_array, ($transport_data[$i])->id);
                        if((($transfer_date[$i])) > $schedule_start_date) {

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

                            $item = $this->postToVisma($id, $gross, $fee, $net, $result->Id,date('Y-m-d',($transfer_date[$i]) ));
                            $item = (is_array($item)) ? http_build_query($item) : $item; 

                            curl_setopt($ch, CURLOPT_URL, env('VISMA_API_ENDPOINT')."/v2/vouchers");
                            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $item);

                            $result = curl_exec($ch);
                            $dt = date('Y-m-d H:i:s');
                            Storage::append((string)$id.'.txt', $dt."[Stripe payment #".$report_id."] successfully transferred to Visma"."\n");
                        }
                        
                        $i = $i + 1;
                    }

                    Stripe::where('user_id', $id)->update(array('transport_number' => serialize($transport_number_array)));
                } else {

                    foreach ($fields as $field) {
                        array_push($transport_number_array, ($transport_data[$i])->id);
                        if((($transfer_date[$i])) >= $schedule_start_date) {

                            if( !in_array(($transport_data[$i])->id, unserialize((Stripe::where('user_id', $id)->first())['transport_number']))) {

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

                                $item = $this->postToVisma($id, $gross, $fee, $net, $result->Id,date('Y-m-d',($transfer_date[$i]) ));
                                $item = (is_array($item)) ? http_build_query($item) : $item; 

                                curl_setopt($ch, CURLOPT_URL, env('VISMA_API_ENDPOINT')."/v2/vouchers");
                                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, $item);

                                $result = curl_exec($ch);
                                $dt = date('Y-m-d H:i:s');
                                Storage::append((string)$id.'.txt', $dt."[Stripe payment #".$report_id."] successfully transferred to Visma"."\n");
                            }
                        }
                        $i= $i+1;
                    }
                    Stripe::where('user_id', $id)->update(array('transport_number' => serialize($transport_number_array)));
                }
                
              
                if (curl_errno($ch)) {
                    echo 'Error:' . curl_error($ch);
                    Storage::append((string)$id.'.txt', "error transferring payment report number "."\t".curl_error($ch)."\t"."]".$dt."\n");
                }
                curl_close ($ch);
                
            }
            /*end insert*/
        }

       return 'success';
    }

    public function authRefreshToken($id) {
    	
        $ch = curl_init();

        $tokenArr = json_decode(Token::where('user_id',$id)->orderBy('id','desc')->first());
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
        
        Token::where('user_id',$id)->delete();
        DB::table('token_info')->insert([
            'user_id'  => $id,
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
            $fields = $this->getPayoutData($id);

            if($fields != null) {

                $schedule_start_date = strtotime((StripeVismaSchedule::where('user_id',$id)->first())["start_date"]);
                $transport_data = unserialize((Stripe::where('user_id', $id)->first())['transport_data']);
                $transfer_date = unserialize((Stripe::where('user_id', $id)->first())['transfer_date']);
                $i = 0;
                $transport_number_array = [];

                if((Stripe::where('user_id', $id)->first())['transport_number'] == null) {

                    foreach ($fields as $field) {
                        array_push($transport_number_array, ($transport_data[$i])->id);
                        if((($transfer_date[$i])) > $schedule_start_date) {

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

                            $item = $this->postToVisma($id, $gross, $fee, $net, $result->Id,date('Y-m-d',($transfer_date[$i]) ));
                            $item = (is_array($item)) ? http_build_query($item) : $item; 

                            curl_setopt($ch, CURLOPT_URL, env('VISMA_API_ENDPOINT')."/v2/vouchers");
                            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $item);

                            $result = curl_exec($ch);
                            $dt = date('Y-m-d H:i:s');
                            Storage::append((string)$id.'.txt', $dt."[Stripe payment #".$report_id."] successfully transferred to Visma"."\n");
                        }
                        
                        $i = $i+1;
                    }

                    Stripe::where('user_id', $id)->update(array('transport_number' => serialize($transport_number_array)));

                } else {

                    foreach ($fields as $field) {

                        array_push($transport_number_array, ($transport_data[$i])->id);
                        
                        if((($transfer_date[$i])) > $schedule_start_date) {

                            if( !in_array(($transport_data[$i])->id, unserialize((Stripe::where('user_id', $id)->first())['transport_number']))) {

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

                                $item = $this->postToVisma($id, $gross, $fee, $net, $result->Id,date('Y-m-d',($transfer_date[$i]) ));
                                $item = (is_array($item)) ? http_build_query($item) : $item; 

                                curl_setopt($ch, CURLOPT_URL, env('VISMA_API_ENDPOINT')."/v2/vouchers");
                                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, $item);

                                $result = curl_exec($ch);
                                $dt = date('Y-m-d H:i:s');
                                Storage::append((string)$id.'.txt', $dt."[Stripe payment #".$report_id."] successfully transferred to Visma"."\n");
                            }
                        }
                        $i = $i+1;
                    }
                    Stripe::where('user_id', $id)->update(array('transport_number' => serialize($transport_number_array)));
                }
                
              
                if (curl_errno($ch)) {
                    echo 'Error:' . curl_error($ch);
                    Storage::append((string)$id.'.txt', "error transferring payment report number "."\t".curl_error($ch)."\t"."]".$dt."\n");
                }
                curl_close ($ch);
                
            }
            /*end insert*/
        }
        return 'success';
    }

    public function postToVisma($id, $gross, $fee, $net, $attachmentId, $transportDate) {

    	

        $gross_amount_account = unserialize((StripeWorkflowData::where('user_id', $id)->first())['gross_amount']);
    	$charges_account = unserialize((StripeWorkflowData::where('user_id', $id)->first())['charges']);
    	$net_amount_account = unserialize((StripeWorkflowData::where('user_id', $id)->first())['net_amount']);
        $date_string = $gross_amount_account->ModifiedUtc;
        $new = date('Y-m-d',strtotime($date_string));

    	$item = array("VoucherDate" => $transportDate, "VoucherText" => "STRIPE TRANSFER", "Rows" => array(
                    
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

    public function getPayoutData($id) {

      
        $payout_data = unserialize((Stripe::where("user_id", $id)->first())["stripe_data"]);
      
        return $payout_data;
    }

public function generatePDF($fields) {
        $details = ['title' => 'test'];
        // $pdf = PDF::loadView('textDoc');
        $total_amount = 0;
        $total_fee = 0;
        $total_net = 0;
        $no = 1;
        $out = '<h3>Transactions</h3>
        <div style="text-align: center;">
            <table><tbody>
                <tr>
                    <td style="color:red;font-size:18px;text-align:center;border-top:1px solid black;border-left:1px solid black;width:20px;">no</td>

                    <td style="color:red;font-size:18px;text-align:center;border-top:1px solid black;border-left:1px solid black;">Type</td>
                    
                    <td style="color:red;font-size:18px;text-align:center;border-top:1px solid black;border-left:1px solid black;">Gross</td>
                    <td style="color:red;font-size:18px;text-align:center;border-top:1px solid black;border-left:1px solid black;">Fee</td>
                    <td style="color:red;font-size:18px;text-align:center;border-top:1px solid black;border-left:1px solid black;">Total</td>
                    <td style="color:red;font-size:18px;text-align:center;border-top:1px solid black;border-left:1px solid black;">Details</td>
                    <td style="color:red;font-size:18px;text-align:center;border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;">Date</td>
                </tr>';
        foreach ($fields as $field) {
            $item = '<tr>
                        <td style="font-size:15px;text-align:center;border-top:1px solid black;border-left:1px solid black;color: gray;">'.$no.'</td>
                        <td style="font-size:15px;border-top:1px solid black;border-left:1px solid black;">Payment</td>
                        <td style="font-size:15px;border-top:1px solid black;border-left:1px solid black;text-align:right;">'.strtoupper($field->currency).' '.($field->amount)*0.01.'</td>
                        <td style="font-size:15px;border-top:1px solid black;border-left:1px solid black;text-align:right;">'.strtoupper($field->currency).' '.($field->fee)*0.01.'</td>
                        <td style="font-size:15px;border-top:1px solid black;border-left:1px solid black;text-align:right;"> '.strtoupper($field->currency).' '.($field->net)*0.01.'</td>
                        <td style="font-size:15px;border-top:1px solid black;border-left:1px solid black;">'.$field->customer_details.'</td>
                        <td style="font-size:15px;border-top:1px solid black;border-left:1px solid black;border-right:1px solid black;">'.date("d", $field->created).'.'.date('m', strtotime(date("M", $field->created))).'.'.date("Y", $field->created).'</td>
                    </tr>' ;
            $out = $out.$item;
            $total_amount = $total_amount + ($field->amount)*0.01;
            $total_fee = $total_fee + ($field->fee)*0.01;
            $total_net = $total_net + ($field->net)*0.01;
            $no = $no + 1;
        }
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
