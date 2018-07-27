<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Woocommerce;
use App\CurrentUser;
use App\Workflow;
use App\WorkflowData;
use App\Visma;
use Auth;
use App\Models\Token;
use \Datetime;
use App\Models\User;
use App\WoovismaSchedule;
use Storage;

class CronController extends Controller
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

                $schedule_array = WoovismaSchedule::where('user_id',$user["id"])->first(); 

                $time_interval = $interval_array[ $schedule_array["time_interval"] ];

                $start_date = strtotime($schedule_array["start_date"]);

                $current_time = strtotime(date("Y-m-d H:i:s"),time());

                $last_time = strtotime($schedule_array["last_run_date"]);

                if($current_time >= $start_date) {

                    /*if($last_time == '-0001-11-30 00:00:00') {

                        #for the first running
                        $schedule = $this->Schedule($user["id"]);

                        WoovismaSchedule::where('user_id',$user["id"])->update(array('last_run_date' => date("Y-m-d H:i:s")));

                    }*/ /*else {*/

                        if($current_time >= $last_time+$time_interval ) {

                            $schedule = $this->Schedule($user["id"]);
                            if ($schedule == 'notupdate') {
                                continue;
                            }
                            WoovismaSchedule::where('user_id',$user["id"])->update(array('last_run_date' => date("Y-m-d H:i:s")));
                        } 
                    /*}*/

                }
    			// $schedule = $this->Schedule($user["id"]);
    		}

    	}
    	return redirect('manageapp');
    }

	public function Schedule($id) {

		$array = Woocommerce::where('user_id', $id)->first();
    
        $woo_shop_info = unserialize($array['credential']);

        $customer_key    = $woo_shop_info['customerKey'];
        $customer_secret = $woo_shop_info['customerSecret'];
        $site_url        = $woo_shop_info['siteUrl'];
        $ch = curl_init();
        
        /*orders*/
        curl_setopt($ch, CURLOPT_URL, $site_url."/wp-json/wc/v2/orders?per_page=100&page=1");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_USERPWD, $customer_key . ":" . $customer_secret);
        $result1 = curl_exec($ch);

        curl_setopt($ch, CURLOPT_URL, $site_url."/wp-json/wc/v2/orders?per_page=100&page=2");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_USERPWD, $customer_key . ":" . $customer_secret);
        $result2 = curl_exec($ch);

        curl_setopt($ch, CURLOPT_URL, $site_url."/wp-json/wc/v2/orders?per_page=100&page=3");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_USERPWD, $customer_key . ":" . $customer_secret);
        $result3 = curl_exec($ch);

        curl_setopt($ch, CURLOPT_URL, $site_url."/wp-json/wc/v2/orders?per_page=100&page=4");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_USERPWD, $customer_key . ":" . $customer_secret);
        $result4 = curl_exec($ch);

        /*shipping_methods ,  taxes/classes, payment_gateways*/
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        
        $woo_orders = json_encode(array_merge(json_decode($result1), json_decode($result2), json_decode($result3), json_decode($result4)));

        // $woo_orders_array = json_decode($woo_orders);
        // $trans_number_array = [];
        // foreach ($woo_orders_array as $item) {
            
        //     array_push($trans_number_array, $item->number);
        // }
        // var_dump($trans_number_array);exit;
        /*check if data has been updated*/
        // $new_data = json_decode($result1)[0]->id;
        // $old_data = json_decode((Woocommerce::where('user_id',$id)->first())['woocommerce_data'])[0]->id;
        // if($new_data == $old_data) {
            
        //     return 'notupdate';
        // }

        /*woocommerce order information*/

        /*save woocommerce order data*/
        $woocommerce_table_info = Woocommerce::all();
        if(count(json_decode($woocommerce_table_info)) == 0) {
                    
            DB::table('woocommerce')->insert([
                'user_id'           => $id,
                'woocommerce_data'  => $woo_orders,
                
            ]);

        } else {
            if(Woocommerce::where('user_id',$id)->first()) {

                Woocommerce::where('user_id',$id)->update(array('woocommerce_data' => $woo_orders));

            } else {

                DB::table('woocommerce')->insert([
                    'user_id'           => $id,
                    'woocommerce_data'  => $woo_orders,

                ]);
            }
        }
        /*saved into woocommerce table*/

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

/*accessToken***********************************************************************************************************************************************/
    public function authAccessToken($id) {
       
        $workflow_data_array = WorkflowData::where('user_id', $id)->first();

        $status_array = unserialize($workflow_data_array["status"]);
        $payment_gateway_array = unserialize($workflow_data_array["payment"]);
        $product_array = unserialize($workflow_data_array["product"]);
        $sales_array = unserialize($workflow_data_array["sales"]);
        $shipping_goods_array = unserialize($workflow_data_array["shipping_goods"]);
        $shipping_vat_array = unserialize($workflow_data_array["shipping_vat"]);
        
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
            $fields = $this->sortWoocommerceData($id);

            if($fields != null) {

                $result_array = [];

                $voucher_array = [];
                foreach ($fields as $field) {
                    if($field['ShippingTotal'] != '0') {

                        $item = $this->getVoucherShipping($field, $id);

                    } else {
                        $item = $this->getVoucher($field, $id);
                    }
                    /*echo('++++++++++++++++++++++++++++++++++++++++++++++++access');
                    var_dump($item);exit;*/
                    $item = (is_array($item)) ? http_build_query($item) : $item; 

                    curl_setopt($ch, CURLOPT_URL, env('VISMA_API_ENDPOINT')."/v2/vouchers");
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $item);

                    $result = curl_exec($ch);
                	$dt = date('Y-m-d H:i:s');
                    Storage::append((string)$id.'.txt', $dt."[WooCommerce orderID ".$field["order_id"]."] successfully transferred to Visma"."\n");
                    array_push($voucher_array, json_decode($result));
                }

                Visma::where('user_id',$id)->update(array('visma_data' => serialize($voucher_array)));

                if (curl_errno($ch)) {
                    echo 'Error:' . curl_error($ch);
                    Storage::append((string)$id.'.txt', $dt."[WooCommerce orderID ".$field["order_id"]."] failed transfer to Visma-["."\t".curl_error($ch)."\t"."]"."\n");
                }
                curl_close ($ch);
                
            }
            /*end insert*/
        }

       // return redirect('manageapp')->with('result', 'Profile updated!');
       return 'success';
      
    }
/*Refresh token********************************************************************************************************/

    public function authRefreshToken($id) {
    	
        $workflow_data_array = WorkflowData::where('user_id', $id)->first();

        $status_array = unserialize($workflow_data_array["status"]);
        $payment_gateway_array = unserialize($workflow_data_array["payment"]);
        $product_array = unserialize($workflow_data_array["product"]);
        $sales_array = unserialize($workflow_data_array["sales"]);
        $shipping_goods_array = unserialize($workflow_data_array["shipping_goods"]);
        $shipping_vat_array = unserialize($workflow_data_array["shipping_vat"]);

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

        Token::where('user_id',$id)->update(array('refresh_token'=>$new_refresh_token, 'access_token'=>$new_access_token));
        /*Token::where('user_id',$id)->delete();
        DB::table('token_info')->insert([

            'user_id'  => $id,
            'refresh_token' => $new_refresh_token,
            'access_token' => $new_access_token,
        ]);   */
        /*end of response*/

        /*authorization with new access_token*/
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
            $fields = $this->sortWoocommerceData($id);
            if($fields != null) {

                $voucher_array = [];
                foreach ($fields as $field) {

                    if($field['ShippingTotal'] != '0') {

                        $item = $this->getVoucherShipping($field, $id);

                    } else {

                        $item = $this->getVoucher($field, $id);
                    }
                    /*echo('===============================================refresh');
                    var_dump($item);exit;*/
                    $item = (is_array($item)) ? http_build_query($item) : $item; 

                    curl_setopt($ch, CURLOPT_URL, env('VISMA_API_ENDPOINT')."/v2/vouchers");
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $item);

                    $result = curl_exec($ch);
                	$dt = date('Y-m-d H:i:s');
                    Storage::append((string)$id.'.txt', $dt."[WooCommerce orderID ".$field["order_id"]."] successfully transferred to Visma"."\n");
                    array_push($voucher_array, json_decode($result));

                }

                Visma::where('user_id',$id)->update(array('visma_data' => serialize($voucher_array)));

	            if (curl_errno($ch)) {
	                echo 'Error:' . curl_error($ch);
	                Storage::append((string)$id.'.txt', $dt."[WooCommerce orderID ".$field["order_id"]."] failed transfer to Visma-["."\t".curl_error($ch)."\t"."]"."\n");
	            }
	            curl_close ($ch);
	            
            }
            /*end insert*/
        }
        return 'success';
    }

    public function sortWoocommerceData($id) {

        $woocommerce_data = Woocommerce::where("user_id", $id)->first();
        $woocommerce_payment_gateway = unserialize($woocommerce_data["payment_gateway"]);
        $woocommerce_tax_class = unserialize($woocommerce_data["tax_class"]);
        $woocommerce_shipping_method = unserialize($woocommerce_data["shipping_method"]);

        $workflow_data_array = WorkflowData::where('user_id', $id)->first();
        
        $status_array = unserialize($workflow_data_array["status"]);
        $payment_gateway_array = unserialize($workflow_data_array["payment"]);
        $product_array = unserialize($workflow_data_array["product"]);
        $sales_array = unserialize($workflow_data_array["sales"]);
        $shipping_goods_array = unserialize($workflow_data_array["shipping_goods"]);
        $shipping_vat_array = unserialize($workflow_data_array["shipping_vat"]);
        
        $woocommerce_data_raw = Woocommerce::all();

        if(count(json_decode($woocommerce_data_raw)) > 0) {

            if(Woocommerce::where('user_id', $id)->first()) {

                $woocommerce_data_item_array = json_decode(Woocommerce::where('user_id', $id)->first());
                $woocommerce_data = json_decode($woocommerce_data_item_array->woocommerce_data);
                $sort_array = [];
                $schedule_start_date = strtotime((WoovismaSchedule::where('user_id',$id)->first())["start_date"]);
                $order_array = [];

                foreach($woocommerce_data as $data) {

                    array_push($order_array, $data->id);

                    if (in_array($data->status, $status_array)) {    

                        $trans_date = strtotime($data->date_modified);

                        if ((Woocommerce::where('user_id', $id)->first())['order_number'] == null) {

                            if($trans_date >= $schedule_start_date) {

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
                        } else {

                            if($trans_date >= $schedule_start_date) {

                                if ( !in_array($data->id, unserialize((Woocommerce::where('user_id', $id)->first())['order_number']))) {
 
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
                        }

                    }

                }
                Woocommerce::where('user_id', $id)->update(array('order_number' => serialize($order_array)));
                return $sort_array;

            } else {

                return null;
            }
        }
    }
    /*with shipping*/
    public function getVoucherShipping($data, $id) {

        /*selected by user*/
        $workflow_data_array = WorkflowData::where('user_id', $id)->first();

        $status_array = unserialize($workflow_data_array["status"]);
        $payment_gateway_array = unserialize($workflow_data_array["payment"]);
        $product_array = unserialize($workflow_data_array["product"]);
        $sales_array = unserialize($workflow_data_array["sales"]);
        $shipping_goods_array = unserialize($workflow_data_array["shipping_goods"]);
        $shipping_vat_array = unserialize($workflow_data_array["shipping_vat"]);


        /*from woocommerce data*/
        $woocommerce_data = Woocommerce::where("user_id", $id)->first();
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
    public function getVoucher($data, $id) {
        
        /*selected by user*/
        $workflow_data_array = WorkflowData::where('user_id', $id)->first();

        $status_array = unserialize($workflow_data_array["status"]);
        $payment_gateway_array = unserialize($workflow_data_array["payment"]);
        $product_array = unserialize($workflow_data_array["product"]);
        $sales_array = unserialize($workflow_data_array["sales"]);
        $shipping_goods_array = unserialize($workflow_data_array["shipping_goods"]);
        $shipping_vat_array = unserialize($workflow_data_array["shipping_vat"]);


        /*from woocommerce data*/
        $woocommerce_data = Woocommerce::where("user_id", $id)->first();
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

}
