<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Support\Facades\Input;
use App\Models\Token;
use App\Visma;
use App\CurrentUser;
use App\Woocommerce;
use App\Stripe;
use App\StripeWorkflow;
use App\StripeWorkflowData;
use App\StripeVismaSchedule;
use App\Http\Controller\VismaDataController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StripeAuthController extends Controller
{
    public function __construct()
    {
        session_start();
        set_time_limit(0);
        ini_set('xdebug.var_display_max_depth', 20000);
        ini_set('xdebug.var_display_max_children', 10000000);
        ini_set('xdebug.var_display_max_data', 10000000);
    }

    public function toAuthPage() {

    	return view('pages.manageapp.stripe_auth');
    }
    public function index() {

    	$current_user = $_SESSION["user"];
    	$current_user_id = $current_user["id"];
    	/*woocommerce order information*/
    	$stripe_info = array(
			'publickey' 	 => Input::get('public-key'),
			'secretKey' => Input::get('secret-key'),
		);

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
  //       $trans_date_array = [];
		// foreach($stripe_payment_array as $data) {

		// 	curl_setopt($ch, CURLOPT_URL, "https://api.stripe.com/v1/transfers/" . $data->id . "/transactions?limit=100");
  //           curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// 	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		// 	curl_setopt($ch, CURLOPT_USERPWD, $secret_key . ":" . "");

		// 	$result_detail = curl_exec($ch);
		// 	$data_detail = json_decode($result_detail)->data;
  //           $trans_date = $data_detail[0]->created;
  //          foreach ($data_detail as $value) {
                
  //               if($trans_date<$value->created) {

  //                   $trans_date = $value->created;
  //               }

  //           }
  //           // var_dump(date('Y-m-d', $trans_date));
  //           array_push($trans_date_array, date('Y-m-d', $trans_date));
            
		// 	array_push($stripe_payment_detail_array, $data_detail);
		// } var_dump($trans_date_array); exit;
/*end*/

/*test mode only one item*/
        $trans_date_array = [];
        curl_setopt($ch, CURLOPT_URL, "https://api.stripe.com/v1/transfers/" . $stripe_payment_array[0]->id . "/transactions?limit=100");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_USERPWD, $secret_key . ":" . "");

        $result_detail = curl_exec($ch);

        $data_detail = json_decode($result_detail)->data;

        $trans_date = $data_detail[0]->created;
           foreach ($data_detail as $value) {
                
                if($trans_date<$value->created) {

                    $trans_date = $value->created;
                }

            }
            // var_dump(date('Y-m-d', $trans_date));
        array_push($trans_date_array, $trans_date);
     
        array_push($stripe_payment_detail_array, $data_detail);
        
/*end*/      
    
		$stripe_table_info = Stripe::all();
        if(count(json_decode($stripe_table_info)) == 0) {
                    
            DB::table('stripe')->insert([
                'user_id'     		=> $current_user_id,
                'stripe_data'  		=> serialize($stripe_payment_detail_array),
                'credential'        => serialize($stripe_info),
                'transfer_date'     => serialize($trans_date_array),
            ]);

        } else {
            if(Stripe::where('user_id',$current_user_id)->first()) {

                Stripe::where('user_id',$current_user_id)->update(array('stripe_data' => serialize($stripe_payment_detail_array), 'credential' => serialize($stripe_info),'transfer_date' => serialize($trans_date_array),));

            } else {

                DB::table('stripe')->insert([
                    'user_id'     		=> $current_user_id,
                    'stripe_data'  		=> serialize($stripe_payment_detail_array),
                    'credential'        => serialize($stripe_info),
                    'transfer_date'     => serialize($trans_date_array),
                ]);
            }
        }
        
        return redirect('manageapp/assigned/stripe');
		// if (curl_errno($ch)) {
		//     echo 'Error:' . curl_error($ch);
		// }
		// curl_close ($ch);
		// // var_dump(json_decode($result));exit;
		// $fee = $net = $gross = 0;
		// $data = json_decode($result)->data;
		// foreach ($data as $item) {
		// 	$fee = $fee + ($item->fee)*0.01;
		// 	$net = $net + ($item->net)*0.01;
		// }
		// var_dump('fee='.$fee);
		// var_dump('net='.$net);
		// var_dump('gross='.($fee+$net));exit;
		
	}

	public function stripeData(Request $request) {

		$all_select_info = [];
        $all_select_info = $request->all();
        array_shift($all_select_info);/*remove first element*/
        array_pop($all_select_info);/*remove last element*/
        array_pop($all_select_info);
   
   		$user = $_SESSION["user"];
   		$user_id = $user["id"];
        $stripe_credential = unserialize((Stripe::where('user_id', $user_id)->first())['credential']);
        
        $public_key = $stripe_credential['publickey'];
        $secret_key = $stripe_credential['secretKey'];

        /*get stripe data from api*/
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
        // foreach($stripe_payment_array as $data) {

        //  curl_setopt($ch, CURLOPT_URL, "https://api.stripe.com/v1/transfers/" . $data->id . "/transactions?limit=100");
        //  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        //  curl_setopt($ch, CURLOPT_USERPWD, $secret_key . ":" . "");

        //  $result_detail = curl_exec($ch);
        //  $data_detail = json_decode($result_detail)->data;
            
        //  array_push($stripe_payment_detail_array, $data_detail);
        // }
/*end*/

/*test mode only one item*/
        curl_setopt($ch, CURLOPT_URL, "https://api.stripe.com/v1/transfers/" . $stripe_payment_array[0]->id . "/transactions?limit=100");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_USERPWD, $secret_key . ":" . "");

        $result_detail = curl_exec($ch);
        $data_detail = json_decode($result_detail)->data;
        
        array_push($stripe_payment_detail_array, $data_detail);
       
/*end*/      
    
        $stripe_table_info = Stripe::all();
        if(count(json_decode($stripe_table_info)) == 0) {
                    
            DB::table('stripe')->insert([
                'user_id'           => $user_id,
                'stripe_data'       => serialize($stripe_payment_detail_array),
                'credential'        => serialize($stripe_info),
            ]);

        } else {
            if(Stripe::where('user_id',$user_id)->first()) {

                Stripe::where('user_id',$user_id)->update(array('stripe_data' => serialize($stripe_payment_detail_array)));

            } else {

                DB::table('stripe')->insert([
                    'user_id'           => $user_id,
                    'stripe_data'       => serialize($stripe_payment_detail_array),
                    'credential'        => serialize($stripe_info),
                ]);
            }
        }
        /*end from api*/




   		$visma_all_data = Visma::where('user_id', $user['id'])->first();
   		$visma_accounts = (unserialize($visma_all_data['accounts_active']));

   		$gross_amount = $visma_accounts[$all_select_info['gross']];
   		$charges = $visma_accounts[$all_select_info['charges']];
   		$net_amount = $visma_accounts[$all_select_info['net']];

        $_SESSION["gross_amount_account"] = $gross_amount;
        $_SESSION["charges_account"] = $charges;
        $_SESSION["net_amount_account"] = $net_amount;

   		if( !StripeWorkflowData::where('user_id',$user_id)->first()) {
            DB::table('stripeworkflowdata')->insert([
                'user_id' => $user_id,
                'gross_amount' => serialize($gross_amount),
                'charges' => serialize($charges),
                'net_amount' =>  serialize($net_amount),
            ]);
        } else {
            StripeWorkflowData::where('user_id',$user_id)->update(array(

                'gross_amount' => serialize($gross_amount),
                'charges' => serialize($charges),
                'net_amount' =>  serialize($net_amount),
            ));
        }


        if( !StripeWorkflow::where('user_id',$user_id)->first()) {
            DB::table('stripeworkflow')->insert([
                'user_id' => $user_id,
                'gross_amount' => $all_select_info['gross'],
                'charges' => $all_select_info['charges'],
                'net_amount' => $all_select_info['net'],
            ]);
        } else {
            StripeWorkflow::where('user_id',$user_id)->update(array(

                'gross_amount' => $all_select_info['gross'],
                'charges' => $all_select_info['charges'],
                'net_amount' => $all_select_info['net'],
            ));
        }

        if(Token::where('user_id', $user_id)->first()) {

            $array = json_decode(Token::where('user_id', $user_id)->first());
            
            $current_time = strtotime(date("Y-m-d H:i:s"),time());

            $created_time = strtotime($array->created_at);
            $updated_time = strtotime($array->updated_at);

            if($array->updated_at == '-0001-11-30 00:00:00') {
        
                if( $current_time-$created_time <= 3600) {

                    $stripevisma_url = url('stripevisma/access_token');

                } else {

                    $stripevisma_url = url('stripevisma/refresh_token');
                }
            } else {
                if( $current_time-$updated_time <= 3600 ) {

                    $stripevisma_url = url('stripevisma/access_token');
                } else {
                    $stripevisma_url = url('stripevisma/refresh_token');
                }   
            }
                
        }
    	return redirect($stripevisma_url);
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
        $woovisma_schedule_table_info = StripeVismaSchedule::all();
        if(count(json_decode($woovisma_schedule_table_info)) == 0) {
                    
            DB::table('stripevisma_schedule')->insert([
                'user_id'           => $current_user_id,
                'time_interval'     => $time_interval,
                'start_date'        => $start_date,
                'last_run_date'     => null,
                'schedule_on'       => 1,
            ]);

        } else {
            if(StripeVismaSchedule::where('user_id',$current_user_id)->first()) {

                StripeVismaSchedule::where('user_id',$current_user_id)->update(array('time_interval' => $time_interval, 
                    'start_date' => $start_date, 'schedule_on' => 1,));

            } else {

                DB::table('stripevisma_schedule')->insert([
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
