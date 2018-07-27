<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Input;
use App\Models\Token;
use App\Visma;
use App\CurrentUser;
use App\Woocommerce;
use App\WorkflowData;
use App\Http\Controller\VismaDataController;
use Illuminate\Support\Facades\DB;
use Storage;
use App\WoovismaSchedule;
class WooVismaController extends Controller
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
        
        $status_array = $_SESSION["status"];
        $payment_gateway_array = $_SESSION["payment"];
        $product_array = $_SESSION["product"];
        $sales_array = $_SESSION["sales"];
        $shipping_goods_array = $_SESSION["shipping_goods"];
        $shipping_vat_array = $_SESSION["shipping_vat"];

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
            $fields = $this->sortWoocommerceData();
            if($fields != null) {

                $result_array = [];

                $voucher_array = [];
                foreach ($fields as $field) {
                    if($field['ShippingTotal'] != '0') {

                        $item = $this->getVoucherShipping($field);

                    } else {
                        $item = $this->getVoucher($field);
                    }

                    $item = (is_array($item)) ? http_build_query($item) : $item; 

                    curl_setopt($ch, CURLOPT_URL, env('VISMA_API_ENDPOINT')."/v2/vouchers");
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $item);

                    $result = curl_exec($ch);
                    $dt = date('Y-m-d H:i:s');
                    Storage::append((string)$current_user["id"].'.txt', $dt."[WooCommerce orderID ".$field["order_id"]."] successfully transferred to Visma"."\n");
                    array_push($voucher_array, json_decode($result));
                }

                Visma::where('user_id',$current_user['id'])->update(array('visma_data' => serialize($voucher_array)));

                if (curl_errno($ch)) {
                    echo 'Error:' . curl_error($ch);
                    Storage::append((string)$current_user["id"].'.txt', $dt."[WooCommerce orderID ".$field["order_id"]."] failed transfer to Visma-["."\t".curl_error($ch)."\t"."]"."\n");
                }
                curl_close ($ch);
                
            }
            /*end insert*/
        }

       return redirect('manageapp')->with('result', 'Profile updated!');
      
    }
/*Refresh token********************************************************************************************************/

    public function authRefreshToken() {

        $status_array = $_SESSION["status"];
        $payment_gateway_array = $_SESSION["payment"];
        $product_array = $_SESSION["product"];
        $sales_array = $_SESSION["sales"];
        $shipping_goods_array = $_SESSION["shipping_goods"];
        $shipping_vat_array = $_SESSION["shipping_vat"];

        $ch = curl_init();
     
        $user_id = $_SESSION["user"]["id"];
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
            $fields = $this->sortWoocommerceData();
            if($fields != null) {

                $voucher_array = [];
                foreach ($fields as $field) {
                    if($field['ShippingTotal'] != '0') {

                        $item = $this->getVoucherShipping($field);

                    } else {

                        $item = $this->getVoucher($field);
                    }

                    $item = (is_array($item)) ? http_build_query($item) : $item; 

                    curl_setopt($ch, CURLOPT_URL, env('VISMA_API_ENDPOINT')."/v2/vouchers");
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $item);
                    $dt = date('Y-m-d H:i:s');
                    $result = curl_exec($ch);
                   
                    Storage::append((string)$user_id.'.txt', $dt."[WooCommerce orderID ".$field["order_id"]."] successfully transferred to Visma"."\n");
                    array_push($voucher_array, json_decode($result));

                }

                Visma::where('user_id',$user_id)->update(array('visma_data' => serialize($voucher_array)));

	            if (curl_errno($ch)) {
	                echo 'Error:' . curl_error($ch);
                    Storage::append((string)$user_id.'.txt', $dt."[WooCommerce orderID ".$field["order_id"]."] failed transfer to Visma-["."\t".curl_error($ch)."\t"."]"."\n");
	            }
	            curl_close ($ch);
	            
            }
            /*end insert*/
        }
        return redirect('manageapp')->with('result', 'Profile updated!');
    }

    public function sortWoocommerceData() {

        $user = $_SESSION["user"];
        $woocommerce_data = Woocommerce::where("user_id", $user["id"])->first();
        $woocommerce_payment_gateway = unserialize($woocommerce_data["payment_gateway"]);
        $woocommerce_tax_class = unserialize($woocommerce_data["tax_class"]);
        $woocommerce_shipping_method = unserialize($woocommerce_data["shipping_method"]);
        
        $status_array = $_SESSION["status"];
        $payment_gateway_array = $_SESSION["payment"];
        $product_array = $_SESSION["product"];
        $sales_array = $_SESSION["sales"];
        $shipping_goods_array = $_SESSION["shipping_goods"];
        $shipping_vat_array = $_SESSION["shipping_vat"];
        
        $woocommerce_data_raw = Woocommerce::all();
        
        $user_id = $_SESSION["user"]["id"];

        if(count(json_decode($woocommerce_data_raw)) > 0) {

            if(Woocommerce::where('user_id', $user_id)->first()) {

                $woocommerce_data_item_array = json_decode(Woocommerce::where('user_id', $user_id)->first());
                $woocommerce_data = json_decode($woocommerce_data_item_array->woocommerce_data);
                $sort_array = [];

                foreach($woocommerce_data as $data) {

                    if (in_array($data->status, $status_array)) {    

                        if(($data->billing)->company !== '') {

                        $private_person = 'false';

                        } else {

                            $private_person = 'true';

                        }

                        if($data->prices_include_tax == true) {

                            $EuThirdParty = 'true';

                        } else {

                            $EuThirdParty = 'false';
                        }
                        
                        $date_string = $data->date_modified;
                        $new = date('Y-m-d',strtotime($date_string));
                        
                        array_push($sort_array,[
                        "Amount"=>$data->total, "CustomerId"=>'9458b498-77da-4c7e-b459-e6d86639e16b', "CurrencyCode"=>$data->currency, "VatAmount"=>$data->total_tax,"ShippingTotal"=>$data->shipping_total,"ShippingTax"=>$data->shipping_tax, "RoundingsAmount"=>(($data->line_items)[0])->total, "InvoiceCity"=>($data->billing)->city,"InvoiceCountryCode"=>($data->billing)->country,"InvoicePostalCode"=>($data->billing)->postcode,"InvoiceCustomerName"=>($data->billing)->first_name.($data->billing)->last_name,"EuThirdParty"=>$EuThirdParty, "CustomerIsPrivatePerson"=>$private_person, "OrderDate"=>$new, "Status"=>1, "RotReducedInvoicingType"=>1, "ReverseChargeOnConstructionServices"=>'true','payment_method'=>$data->payment_method,
                            "tax_id"=>$data->tax_lines[0]->rate_id, "shipping_id"=>$data->shipping_lines[0]->instance_id,
                            "order_id"=>$data->id,
                        ]);    
                    }

                }
               
                return $sort_array;

            } else {

                return null;
            }
        }
    }
    /*with shipping*/
    public function getVoucherShipping($data) {

        $user = $_SESSION["user"];
        /*selected by user*/
        $payment_gateway_array = $_SESSION["payment"];
        $product_array = $_SESSION["product"];
        $sales_array = $_SESSION["sales"];
        $shipping_goods_array = $_SESSION["shipping_goods"];
        $shipping_vat_array = $_SESSION["shipping_vat"];

        /*from woocommerce data*/
        $woocommerce_data = Woocommerce::where("user_id", $user["id"])->first();
        $woocommerce_payment_gateway = unserialize($woocommerce_data["payment_gateway"]);
        $woocommerce_tax_class = unserialize($woocommerce_data["tax_class"]);
        $woocommerce_shipping_method = unserialize($woocommerce_data["shipping_method"]);
       
        foreach ($woocommerce_payment_gateway as $key => $item) {
            if($item->id == $data->payment_method) {
                $debit = $payment_gateway_array[$key];
            }
        }

        $product = $product_array[($data["tax_id"])-1];
        $sales   = $sales_array[($data["tax_id"])-1];
        $shipping_goods = $shipping_goods_array[($data["shipping_id"])-1];
        $shipping_vat = $shipping_vat_array[($data["shipping_id"])-1];
       
        $item = array("VoucherDate" => $data['OrderDate'], "VoucherText" => "EPTI Voucher - WooCommerce order #".$data['order_id'], "Rows" => array(
                    
           [ "AccountNumber" => $debit->Number, "AccountDescription" => $debit->Name, "DebitAmount" => $data['Amount'],'CostCenterItemId1' => null,'CostCenterItemId2' => null,'CostCenterItemId3' => null,
               "TransactionText" => $debit->TypeDescription, "VatCodeId" => "eaf3eadf-33cc-4579-ab71-e96258afc1d3",'VatCodeAndPercent' => '09 (25%)', "Quantity" => null, "Weight" => null, "DeliveryDate" => null,'HarvestYear' => null,'ProjectId' => null],/*debit*/

           [ "AccountNumber" => $product->Number, "AccountDescription" =>$product->Name , "CreditAmount" => $data['Amount']-$data['VatAmount']-$data['ShippingTotal'],'CostCenterItemId1' => null,'CostCenterItemId2' => null,'CostCenterItemId3' => null,
            "TransactionText" => $product->TypeDescription, "VatCodeId" => "eaf3eadf-33cc-4579-ab71-e96258afc1d3",'VatCodeAndPercent' => '03 (25%)', "Quantity" => null, "Weight" => null, "DeliveryDate" => null,'HarvestYear' => null,'ProjectId' => null],/*product*/

           [ "AccountNumber" => $sales->Number, "AccountDescription" =>$sales->Name , "CreditAmount" => $data['VatAmount'],
                'CostCenterItemId1' => null,'CostCenterItemId2' => null,'CostCenterItemId3' => null,
            "TransactionText" => $sales->TypeDescription, "VatCodeId" => "eaf3eadf-33cc-4579-ab71-e96258afc1d3",'VatCodeAndPercent' => '09 (25%)', "Quantity" => null, "Weight" => null, "DeliveryDate" => null,'HarvestYear' => null,'ProjectId' => null],/*sales*/

            [ "AccountNumber" => $shipping_goods->Number, "AccountDescription" =>$shipping_goods->Name , "CreditAmount" => $data['ShippingTotal']-$data['ShippingTax'],
                'CostCenterItemId1' => null,'CostCenterItemId2' => null,'CostCenterItemId3' => null,
            "TransactionText" => $shipping_goods->TypeDescription, "VatCodeId" => "eaf3eadf-33cc-4579-ab71-e96258afc1d3",'VatCodeAndPercent' => '09 (25%)', "Quantity" => null, "Weight" => null, "DeliveryDate" => null,'HarvestYear' => null,'ProjectId' => null],/*shipping product*/

            [ "AccountNumber" => $shipping_vat->Number, "AccountDescription" =>$shipping_vat->Name , "CreditAmount" => $data['ShippingTax'],
                'CostCenterItemId1' => null,'CostCenterItemId2' => null,'CostCenterItemId3' => null,
            "TransactionText" => $shipping_vat->TypeDescription, "VatCodeId" => "eaf3eadf-33cc-4579-ab71-e96258afc1d3",'VatCodeAndPercent' => '09 (25%)', "Quantity" => null, "Weight" => null, "DeliveryDate" => null,'HarvestYear' => null,'ProjectId' => null]/*shipping sales*/
    
        ),"Attachments.DocumentType" => 0,  "VoucherType" => 3);

        return $item;
    }   

    /*with out shipping*/
    public function getVoucher($data) {
        
        $user = $_SESSION["user"];
        /*selected by user*/
        $payment_gateway_array = $_SESSION["payment"];
        $product_array = $_SESSION["product"];
        $sales_array = $_SESSION["sales"];
        $shipping_goods_array = $_SESSION["shipping_goods"];
        $shipping_vat_array = $_SESSION["shipping_vat"];

        /*from woocommerce data*/
        $woocommerce_data = Woocommerce::where("user_id", $user["id"])->first();
        $woocommerce_payment_gateway = unserialize($woocommerce_data["payment_gateway"]);
        $woocommerce_tax_class = unserialize($woocommerce_data["tax_class"]);
        $woocommerce_shipping_method = unserialize($woocommerce_data["shipping_method"]);
       
        foreach ($woocommerce_payment_gateway as $key => $item) {
            if($item->id == $data["payment_method"]) {
                $debit = $payment_gateway_array[$key];
            }
        }

        $product = $product_array[($data["tax_id"])-1];
        $sales   = $sales_array[($data["tax_id"])-1];
       
        $item = array("VoucherDate" => $data['OrderDate'], "VoucherText" => "EPTI Voucher - WooCommerce order #".$data['order_id'], "Rows" => array(
                    
           [ "AccountNumber" => $debit->Number, "AccountDescription" => $debit->Name, "DebitAmount" => $data['Amount'],'CostCenterItemId1' => null,'CostCenterItemId2' => null,'CostCenterItemId3' => null,
               "TransactionText" => $debit->TypeDescription, "VatCodeId" => "eaf3eadf-33cc-4579-ab71-e96258afc1d3",'VatCodeAndPercent' => '09 (25%)', "Quantity" => null, "Weight" => null, "DeliveryDate" => null,'HarvestYear' => null,'ProjectId' => null],/*debit*/

           [ "AccountNumber" => $product->Number, "AccountDescription" =>$product->Name , "CreditAmount" => $data['Amount']-$data['VatAmount'],
            'CostCenterItemId1' => null,'CostCenterItemId2' => null,'CostCenterItemId3' => null,
            "TransactionText" => $product->TypeDescription, "VatCodeId" => "eaf3eadf-33cc-4579-ab71-e96258afc1d3",'VatCodeAndPercent' => '03 (25%)', "Quantity" => null, "Weight" => null, "DeliveryDate" => null,'HarvestYear' => null,'ProjectId' => null],/*product*/

           [ "AccountNumber" => $sales->Number, "AccountDescription" =>$sales->Name , "CreditAmount" => $data['VatAmount'],
                'CostCenterItemId1' => null,'CostCenterItemId2' => null,'CostCenterItemId3' => null,
            "TransactionText" => $sales->TypeDescription, "VatCodeId" => "eaf3eadf-33cc-4579-ab71-e96258afc1d3",'VatCodeAndPercent' => '09 (25%)', "Quantity" => null, "Weight" => null, "DeliveryDate" => null,'HarvestYear' => null,'ProjectId' => null],/*sales*/
    
        ),"Attachments.DocumentType" => 0,  "VoucherType" => 3);

        return $item;
    }  

    public function showLog() {
        $user_id = $_SESSION["user"]["id"];
        if(Storage::exists((string)$user_id.".txt")) {

            $logs = Storage::get((string)$user_id.".txt");
     
            $logs_array = explode("\n", $logs);
            $logs_array = array_reverse($logs_array);
        } else {
            
            $logs_array = null;
        }
        
        return view('pages.manageapp.log',["log_array"=>$logs_array]);
    } 
}
