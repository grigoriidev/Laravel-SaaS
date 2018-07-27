<?php

namespace App\Http\Controllers;

use App\Woocommerce;
use App\Apps;
use App\CurrentUser;
use App\Stripe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StripeDataController extends Controller
{
    public function __construct()
    {
        session_start();
    }

    // public function showData()
    // {
        
    //     $woocommerce_orders_data = [];
    //     $woocommerce_sales_title = [];
    //     // $visma_data = $_SESSION["visma_data"];
    //     $current_user_id = $_SESSION["user"]["id"];

    //     $woocommercetable_info = Woocommerce::all();
    //     if(Woocommerce::where('user_id',$current_user_id)->first()) {
    
    //         $woocommerce_data = Woocommerce::where('user_id',$current_user_id)->first();

    //         $woocommerce_data = json_decode($woocommerce_data);
            
    //         $woocommerce_data = json_decode($woocommerce_data->woocommerce_data);

    //     } else {

    //     	$woocommerce_data = null;
    //     }
    //     // var_dump($woocommerce_data);exit;
    //     return view('pages.manageapp.woocommerce', ['woocommerce_data' => $woocommerce_data]);
    // }

    public function assignApp() {

       
    	$current_user_id = $_SESSION["user"]["id"];

        $user_app_table_info = Apps::all();
        if(count(json_decode($user_app_table_info)) == 0) {
            
        	
            $stripe_info_array = array(["id"=>0,"name"=>"Stripe","app"=>"Credit-Card"]);
			$stripe_info = json_encode($stripe_info_array);
			
            DB::table('user_app')->insert([

                'user_id'  	  => $current_user_id,
                'visma' 	  => null,
                'stripe' 	  => $stripe_info,
                'woocommerce' => null,
                'shopify' 	  => null,
            ]);

        } else {
        	if(Apps::where('user_id',$current_user_id)->first()) {
        		$item = Apps::where('user_id',$current_user_id)->first();
        		$stripe_array = json_decode($item->stripe);
        		if($stripe_array == null) {
        			$id = 0;
        			$stripe_array = array(["id"=>$id,"name"=>"Stripe","app"=>"Credit-Card"]);
        		} else {
        			$id = (end($stripe_array))->id + 1;	
        			array_push($stripe_array, ["id"=>$id,"name"=>"Stripe","app"=>"Credit-Card"]);
        		}
        		
        		
        		$item->stripe = json_encode($stripe_array);
        		$item->save();
        	} else {
        		$stripe_info_array = array(["id"=>0,"name"=>"Stripe","app"=>"Credit-Card"]);
                
				$stripe_info = json_encode($stripe_info_array);
				
                DB::table('user_app')->insert([
                    'user_id'  	  => $current_user_id,
                    'visma' 	  => null,
                    'stripe' 	  => $stripe_info,
                    'woocommerce' => null,
                    'shopify' 	  => null,
                ]);
        	}
        }
    	
    	return redirect('manageapp');

    }
}
