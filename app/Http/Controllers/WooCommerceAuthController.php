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
use App\WoovismaSchedule;
use Auth;
use App\Models\Token;
use \Datetime;

class WooCommerceAuthController extends Controller
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
    	
    	return view('pages.manageapp.woocommerce_auth');
    }

    public function getData() {

        $current_user = $_SESSION["user"];
    	/*woocommerce order information*/
    	$woo_shop_info = array(
			'customerKey' 	 => Input::get('customer-key'),
			'customerSecret' => Input::get('customer-secret'),
			'siteUrl'        => Input::get('site-url')
		);

    	$customer_key    = $woo_shop_info['customerKey'];
		$customer_secret = $woo_shop_info['customerSecret'];
		$site_url 		 = $woo_shop_info['siteUrl'];

        $shipping_method = [];

		$ch = curl_init();

		/*orders*/
		curl_setopt($ch, CURLOPT_URL, $site_url."/wp-json/wc/v2/orders?per_page=1&page=1");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_USERPWD, $customer_key . ":" . $customer_secret);
        $result1 = curl_exec($ch);
        
        curl_setopt($ch, CURLOPT_URL, $site_url."/wp-json/wc/v2/orders?per_page=1&page=2");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_USERPWD, $customer_key . ":" . $customer_secret);
        $result2 = curl_exec($ch);

        /*get activated vat rates*/
        curl_setopt($ch, CURLOPT_URL, $site_url."/wp-json/wc/v2/taxes");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_USERPWD, $customer_key . ":" . $customer_secret);
        $tax_class = json_decode(curl_exec($ch));

        foreach ($tax_class as $item) {
            if($item->shipping == true) {
                array_push($shipping_method, $item);
            }
        }

        /*get activated payment methods*/
        curl_setopt($ch, CURLOPT_URL, $site_url."/wp-json/wc/v2/payment_gateways");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_USERPWD, $customer_key . ":" . $customer_secret);
        $payment_gateways = json_decode(curl_exec($ch));

        $payment_gateway = [];
        
        foreach ($payment_gateways as $item) {
            if ($item->enabled) {
                array_push($payment_gateway, $item);
            }
        }
        
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        
        $woo_orders = json_encode(array_merge(json_decode($result1), json_decode($result2)));
        
		/*woocommerce order information*/
        // var_dump(json_decode($woo_orders));exit;
		
        $current_user_id = $_SESSION["user"]["id"];

        /*save woocommerce order data*/
        $woocommerce_table_info = Woocommerce::all();
        if(count(json_decode($woocommerce_table_info)) == 0) {
                    
            DB::table('woocommerce')->insert([
                'user_id'     		=> $current_user_id,
                'woocommerce_data'  => $woo_orders,
                'credential'        => serialize($woo_shop_info),
                'payment_gateway'   => serialize($payment_gateway),
                'tax_class'         => serialize($tax_class),
                'shipping_method'   => serialize($shipping_method),
            ]);

        } else {
            if(Woocommerce::where('user_id',$current_user_id)->first()) {

                Woocommerce::where('user_id',$current_user_id)->update(array('woocommerce_data' => $woo_orders, 'credential' => serialize($woo_shop_info),'payment_gateway' => serialize($payment_gateway),'tax_class' => serialize($tax_class),'shipping_method' => serialize($shipping_method),));

            } else {

                DB::table('woocommerce')->insert([
                    'user_id'     		=> $current_user_id,
                    'woocommerce_data'  => $woo_orders,
                    'credential'        => serialize($woo_shop_info),
                    'payment_gateway'   => serialize($payment_gateway),
                    'tax_class'         => serialize($tax_class),
                    'shipping_method'   => serialize($shipping_methods),
                ]);
            }
        }
        /*saved into woocommerce table*/
        
        /*redirect assignapp page*/
        return redirect('manageapp/assigned/woocommerce');
    }
    public function woocommerceData(Request $request) {
        /*accounts information*/
        $all_select_info = [];
        $all_select_info = $request->all();
        /*remove unused element from the array*/
        array_shift($all_select_info);/*remove first element*/
        array_pop($all_select_info);/*remove last element*/
        array_pop($all_select_info);
       
        $status_array = [];
        $status_array1 = [];
        $payment_gateway_array = [];
        $product_array = [];
        $sales_array = [];
        $shipping_goods = [];
        $shipping_vat = [];

        $workflow_status = $all_select_info['status'];
        $workflow_payment = [];
        $workflow_product = [];
        $workflow_sales = [];
        $workflow_shipping_goods = [];
        $workflow_shipping_vat = [];

        $user = $_SESSION['user'];
        $visma_all_data = Visma::where('user_id', $user['id'])->first();

        $status_data = ['completed', 'on-hold', 'processing','failed', 'refunded', 'pending payment', 'cancelled'];
        $visma_accounts = (unserialize($visma_all_data['accounts_active']));
       
        
        foreach ($all_select_info as $key => $item) {
            if($key == 'status') {
                $status_array1 = $item;
                if(count($status_array1) > 0) {

                    foreach ($status_array1 as $data) {
                        array_push($status_array,$status_data[$data]);
                    }
                }
            }
            if(strpos($key,'payment') !== false) {
                array_push($payment_gateway_array, $visma_accounts[$item]);
                array_push($workflow_payment, $item);
            }
            if(strpos($key,'product') !== false) {
                array_push($product_array, $visma_accounts[$item]);
                array_push($workflow_product, $item);
            }
            if(strpos($key,'sales') !== false) {
                array_push($sales_array, $visma_accounts[$item]);
                array_push($workflow_sales, $item);
            }
            if(strpos($key,'shippinggoods') !== false) {
                array_push($shipping_goods, $visma_accounts[$item]);
                array_push($workflow_shipping_goods, $item);
            }
            if(strpos($key,'shippingvat') !== false) {
                array_push($shipping_vat, $visma_accounts[$item]);
                array_push($workflow_shipping_vat, $item);
            }
        }

        /*woocommerce orders info*/
        $array = Woocommerce::where('user_id', $user['id'])->first();
        $woo_shop_info = unserialize($array['credential']);

        $customer_key    = $woo_shop_info['customerKey'];
        $customer_secret = $woo_shop_info['customerSecret'];
        $site_url        = $woo_shop_info['siteUrl'];
        $ch = curl_init();
        
        /*orders*/
        curl_setopt($ch, CURLOPT_URL, $site_url."/wp-json/wc/v2/orders?per_page=1&page=1");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_USERPWD, $customer_key . ":" . $customer_secret);
        $result1 = curl_exec($ch);

        curl_setopt($ch, CURLOPT_URL, $site_url."/wp-json/wc/v2/orders?per_page=1&page=2");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_USERPWD, $customer_key . ":" . $customer_secret);
        $result2 = curl_exec($ch);

        /*shipping_methods ,  taxes/classes, payment_gateways*/
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        
        $woo_orders = json_encode(array_merge(json_decode($result1), json_decode($result2)));
        
        /*woocommerce order information*/
     
        $current_user_id = $_SESSION["user"]["id"];

        /*save woocommerce order data*/
        $woocommerce_table_info = Woocommerce::all();
        if(count(json_decode($woocommerce_table_info)) == 0) {
                    
            DB::table('woocommerce')->insert([
                'user_id'           => $current_user_id,
                'woocommerce_data'  => $woo_orders,
            ]);

        } else {
            if(Woocommerce::where('user_id',$current_user_id)->first()) {

                Woocommerce::where('user_id',$current_user_id)->update(array('woocommerce_data' => $woo_orders));

            } else {

                DB::table('woocommerce')->insert([
                    'user_id'           => $current_user_id,
                    'woocommerce_data'  => $woo_orders,
                ]);
            }
        }
        /*saved into woocommerce table*/

        /*redirect assignapp page*/
        $user = Auth::user();
        $user_id = $user->id;
        $array = [];
        if(Token::where('user_id', $user_id)->first()) {

            $array = json_decode(Token::where('user_id', $user_id)->first());
            // date_default_timezone_set("America/Los_Angeles");
            /*on the server*/
            // date_default_timezone_set("Europe/Oslo");
            $current_time = strtotime(date("Y-m-d H:i:s"),time());

            $created_time = strtotime($array->created_at);
            $updated_time = strtotime($array->updated_at);

            if($array->updated_at == '-0001-11-30 00:00:00') {
        
                if( $current_time-$created_time <= 3600) {

                    $visma_authorize_url = url('woovisma/access_token');

                } else {

                    $visma_authorize_url = url('woovisma/refresh_token');
                }
            } else {
                if( $current_time-$updated_time <= 3600 ) {

                    $visma_authorize_url = url('woovisma/access_token');
                } else {
                    $visma_authorize_url = url('woovisma/refresh_token');
                }   
            }
                
        }

        $_SESSION["status"] = $status_array;
        $_SESSION["payment"] = $payment_gateway_array;
        $_SESSION["product"] = $product_array;
        $_SESSION["sales"] = $sales_array;
        $_SESSION["shipping_goods"] = $shipping_goods;
        $_SESSION["shipping_vat"] = $shipping_vat;

        if( !WorkflowData::where('user_id',$current_user_id)->first()) {
            DB::table('workflow_datas')->insert([
                'user_id' => $current_user_id,
                'status' => serialize($status_array),
                'payment' => serialize($payment_gateway_array),
                'product' => serialize($product_array),
                'sales' => serialize($sales_array),
                'shipping_goods' => serialize($shipping_goods),
                'shipping_vat' => serialize($shipping_vat),
            ]);
        } else {
            WorkflowData::where('user_id',$current_user_id)->update(array(
                'status' => serialize($status_array),
                'payment' => serialize($payment_gateway_array),
                'product' => serialize($product_array),
                'sales' => serialize($sales_array),
                'shipping_goods' => serialize($shipping_goods),
                'shipping_vat' => serialize($shipping_vat),
            ));
        }


        if( !Workflow::where('user_id',$current_user_id)->first()) {
            DB::table('workflow')->insert([
                'user_id' => $current_user_id,
                'status' => serialize($workflow_status),
                'payment' => serialize($workflow_payment),
                'product' => serialize($workflow_product),
                'sales' => serialize($workflow_sales),
                'shipping_goods' => serialize($workflow_shipping_goods),
                'shipping_vat' => serialize($workflow_shipping_vat),
            ]);
        } else {
            Workflow::where('user_id',$current_user_id)->update(array(
                'status' => serialize($workflow_status),
                'payment' => serialize($workflow_payment),
                'product' => serialize($workflow_product),
                'sales' => serialize($workflow_sales),
                'shipping_goods' => serialize($workflow_shipping_goods),
                'shipping_vat' => serialize($workflow_shipping_vat),
            ));
        }

        return redirect($visma_authorize_url);
    }

    public function saveScheduleData(Request $request) {
        
        $all_select_info = $request->all();
        array_shift($all_select_info);/*remove first element*/
        $time_interval = $all_select_info['time_interval'];
        $start_date = $all_select_info['start_date'];
        // $time_interval_array = ['5 minutes', '15 minutes', '1 hour','a day', 'a week'];
        // $time_interval = $time_interval_array[$time_interval];
        /*save in database*/
        $current_user_id = $_SESSION["user"]["id"];
        $woovisma_schedule_table_info = WoovismaSchedule::all();
        if(count(json_decode($woovisma_schedule_table_info)) == 0) {
                    
            DB::table('woovisma_schedule')->insert([
                'user_id'           => $current_user_id,
                'time_interval'     => $time_interval,
                'start_date'        => $start_date,
                'last_run_date'     => null,
                'schedule_on'       => 1,
            ]);

        } else {
            if(WoovismaSchedule::where('user_id',$current_user_id)->first()) {

                WoovismaSchedule::where('user_id',$current_user_id)->update(array('time_interval' => $time_interval, 
                    'start_date' => $start_date, 'schedule_on' => 1,));

            } else {

                DB::table('woovisma_schedule')->insert([
                'user_id'           => $current_user_id,
                'time_interval'     => $time_interval,
                'start_date'        => $start_date,
                'last_run_date'     => null,
                'schedule_on'       => 1,
            ]);
            }
        }
        // var_dump($all_select_info);exit;
        return redirect('manageapp');
    }
}


/*my team says you should

create a file named .env on root folder

that states

NODE_ENV=development

*/