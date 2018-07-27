<?php

namespace App\Http\Controllers;
use App\Apps;
use Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Models\Token;
use \Datetime;
use App\Visma;
use App\Woocommerce;
use App\Workflow;
use App\StripeWorkflow;
use App\WoovismaSchedule;
use App\StripeVismaSchedule;
use Storage;

class ManageAppController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show user's apps.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        
        $user = Auth::user();
        $user_id = $user->id;
        if(!Storage::exists((string)$user_id.'.txt')) {
            Storage::put((string)$user_id.'.txt', '');
        }
        $visma_vatcode_data = null;
        $visma_accounts = null;

        $visma_vatcode_array = null;
        $visma_accounts_array = null;

        $payment_gateway = [];
        $tax_class = [];
        $shipping_method = [];

        /*workflow*/
        $status = null;
        $payment = null;
        $product = null;
        $sales = null;
        $shipping_goods = null;
        $shipping_vat = null;

        $gross_amount = null;
        $charges = null;
        $net_amount = null;

        $woovisma_schedule_time_interval = null;
        $woovisma_schedule_start_date = null;
        $woovisma_schedule_last_time = null;

        $stripevisma_schedule_time_interval = null;
        $stripevisma_schedule_start_date = null;
        $stripevisma_schedule_last_time = null;

        if(count(json_decode(Apps::all())) == 0) {
            $assigned_app_visma  = null; 
            $assigned_app_woocommerce = null;
            $assigned_app_stripe = null;
        } else {

            /*have to do work here today 2018-5-18*/
                $assigned_app_all = Apps::where('user_id',$user['id'])->first();
             
                if(!empty($assigned_app_all['visma'])) {
                    $assigned_app_visma = json_decode($assigned_app_all['visma']);
                    $visma_all_data = Visma::where('user_id', $user['id'])->first();
                    $visma_vatcode_data = (json_decode($visma_all_data['vatcode']))->Data;

                    $visma_accounts = (unserialize($visma_all_data['accounts']));

                    $visma_vatcode_array = [];
                    foreach ($visma_vatcode_data as $visma_vatcode_data_item) {
                        array_push($visma_vatcode_array, $visma_vatcode_data_item->Description.'('.$visma_vatcode_data_item->VatRate.')');
                    }
                    
                    $visma_accounts_array = [];
                    $visma_accounts_array_db = [];
                    foreach ($visma_accounts as $item) {
                        foreach ($item as $data) {

                            if($data->IsActive == true) {
                                array_push($visma_accounts_array_db, $data);
                                array_push($visma_accounts_array, $data->Number.' - '.$data->Name);
                            }
                        }
                    }
                    Visma::where('user_id', $user['id'])->update(['accounts_active' => serialize($visma_accounts_array_db)]);
                }
                if(  !$assigned_app_all['visma']) {

                    $assigned_app_visma = null;
                    $visma_vatcode_data = null;
                    $visma_account_data = null;
                    $visma_vatcode_array = null;
                    $visma_accounts_array = null;
                }
                if($assigned_app_all['woocommerce']) {
                    $assigned_app_woocommerce = json_decode($assigned_app_all['woocommerce']);

                }
                if( !$assigned_app_all['woocommerce']) {
                    $assigned_app_woocommerce = null;
                }

                if($assigned_app_all['stripe']) {
                    $assigned_app_stripe = json_decode($assigned_app_all['stripe']);
                }

                if( !$assigned_app_all['stripe']) {
                    $assigned_app_stripe = null;
                }

                if(Woocommerce::where('user_id',$user_id)->first()) {
                    $woocommerce_settings = Woocommerce::where('user_id', $user_id)->first();
                    $payment_gateway = unserialize($woocommerce_settings['payment_gateway']);
                    $tax_class = unserialize($woocommerce_settings['tax_class']);
                    $shipping_method = unserialize($woocommerce_settings['shipping_method']);

                } else {
                    $payment_gateway = [];
                    $tax_class = [];
                    $shipping_method = [];
                }

                if(Workflow::where('user_id',$user_id)->first()) {

                    $workflow = Workflow::where('user_id',$user_id)->first();
                    /*workflow*/
                    $status = unserialize($workflow['status']);
                    $payment = unserialize($workflow['payment']);
                    $product = unserialize($workflow['product']);
                    $sales =unserialize($workflow['sales']);
                    $shipping_goods = unserialize($workflow['shipping_goods']);
                    $shipping_vat = unserialize($workflow['shipping_vat']);
                }

                if(StripeWorkflow::where('user_id',$user_id)->first()) {

                    $workflow = StripeWorkflow::where('user_id',$user_id)->first();
                    /*workflow*/
                    $gross_amount = ($workflow['gross_amount']);
                    $charges = ($workflow['charges']);
                    $net_amount = ($workflow['net_amount']);
                    
                }

                if(WoovismaSchedule::where('user_id', $user_id)->first()) {
                    $schedule = WoovismaSchedule::where('user_id', $user_id)->first();
                    $woovisma_schedule_time_interval = $schedule['time_interval'];
                    $woovisma_schedule_start_date = $schedule['start_date'];
                    $woovisma_schedule_last_time = $schedule['last_run_date'];
                }

                if(StripeVismaSchedule::where('user_id', $user_id)->first()) {
                    $schedule = StripeVismaSchedule::where('user_id', $user_id)->first();
                    $stripevisma_schedule_time_interval = $schedule['time_interval'];
                    $stripevisma_schedule_start_date = $schedule['start_date'];
                    $stripevisma_schedule_last_time = $schedule['last_run_date'];
                }
        }
        
        $REDIRECT_URI = "https://epti.eratio.com/api/eaccounting";
        $STATE = md5(time());
        $visma_authorize_url = env('VISMA_AUTH_ENDPOINT') . "/connect/authorize?client_id=". env('VISMA_CLIENT_ID') ."&redirect_uri=". $REDIRECT_URI ."&scope=". env('VISMA_SCOPES') ."&state=". $STATE ."&response_type=code&prompt=login";

        return view('pages.manageapp.index',[
            'assigned_app_visma' => $assigned_app_visma,'assigned_app_woocommerce' => $assigned_app_woocommerce, 'assigned_app_stripe' => $assigned_app_stripe, 'current_user' => $user, 'vatcodes' => $visma_vatcode_data, 'vatcodearray' => $visma_vatcode_array, 'accounts' => $visma_accounts_array, 'payment_gateway' => $payment_gateway, 'tax_class' => $tax_class, 'shipping_method' => $shipping_method, 'authUrl' => $visma_authorize_url,'status' => $status, 'payment' => $payment, 'product' => $product, 'sales' => $sales, 'shipping_goods' => $shipping_goods, 'shipping_vat' => $shipping_vat, 'gross_amount' => $gross_amount, 'charges' => $charges, 'net_amount' => $net_amount, 'woo_schedule_interval' => 
                $woovisma_schedule_time_interval, 'woo_schedule_start' => $woovisma_schedule_start_date, 'woo_schedule_last' => 
                $woovisma_schedule_last_time,'stripe_schedule_interval' => $stripevisma_schedule_time_interval, 
                'stripe_schedule_start' => $stripevisma_schedule_start_date, 'stripe_schedule_last' => $stripevisma_schedule_last_time]);
    }

    /**
     * Add new item interface
     */
    public function newApp()
    {
        
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
            // var_dump($current_time - $created_time);

            if($array->updated_at == '-0001-11-30 00:00:00') {
        
                if( $current_time-$created_time <= 3600) {

                    $visma_authorize_url = url('visma/refresh_token');

                } else {

                    $visma_authorize_url = url('visma/refresh_token');
                }
            } else {
                if( $current_time-$updated_time <= 3600 ) {

                    $visma_authorize_url = url('visma/refresh_token');
                } else {
                    $visma_authorize_url = url('visma/refresh_token');
                }   
            }
                
        }
        
        else {
            $REDIRECT_URI = "https://epti.eratio.com/api/eaccounting";
            $STATE = md5(time());
            $visma_authorize_url = env('VISMA_AUTH_ENDPOINT') . "/connect/authorize?client_id=". env('VISMA_CLIENT_ID') ."&redirect_uri=". $REDIRECT_URI ."&scope=". env('VISMA_SCOPES') ."&state=". $STATE ."&response_type=code&prompt=login";
        }
       
        return view('pages.manageapp.new', ['visma_authorize_url' => $visma_authorize_url, 'woocommerce_authorize_url' => url('woocommerce/authorize'), 'stripe_authorize_url' => url('stripe/authorize')]);
    }

}
            