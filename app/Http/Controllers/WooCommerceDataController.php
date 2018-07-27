<?php

namespace App\Http\Controllers;

use App\Woocommerce;
use App\Apps;
use App\CurrentUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WooCommerceDataController extends Controller
{
    public function __construct()
    {
        session_start();
    }

    public function showData()
    {
        
        $woocommerce_orders_data = [];
        $woocommerce_sales_title = [];
        // $visma_data = $_SESSION["visma_data"];
        $current_user_id = $_SESSION["user"]["id"];

        $woocommercetable_info = Woocommerce::all();
        if(Woocommerce::where('user_id',$current_user_id)->first()) {
    
            $woocommerce_data = Woocommerce::where('user_id',$current_user_id)->first();

            $woocommerce_data = json_decode($woocommerce_data);
            
            $woocommerce_data = json_decode($woocommerce_data->woocommerce_data);

        } else {

        	$woocommerce_data = null;
        }
        // var_dump($woocommerce_data);exit;
        return view('pages.manageapp.woocommerce', ['woocommerce_data' => $woocommerce_data]);
    }

    public function assignApp() {

       
    	$current_user_id = $_SESSION["user"]["id"];

        $user_app_table_info = Apps::all();
        if(count(json_decode($user_app_table_info)) == 0) {
            
        	
            $woocommerce_info_array = array(["id"=>0,"name"=>"WooCommerce","app"=>"e-Commerce"]);
			$woocommerce_info = json_encode($woocommerce_info_array);
			
            DB::table('user_app')->insert([

                'user_id'  	  => $current_user_id,
                'visma' 	  => null,
                'stripe' 	  => null,
                'woocommerce' => $woocommerce_info,
                'shopify' 	  => null,
            ]);

        } else {
        	if(Apps::where('user_id',$current_user_id)->first()) {
        		$item = Apps::where('user_id',$current_user_id)->first();
        		$woocommerce_array = json_decode($item->woocommerce);
        		if($woocommerce_array == null) {
        			$id = 0;
        			$woocommerce_array = array(["id"=>$id,"name"=>"WooCommerce","app"=>"e-Commerce"]);
        		} else {
        			$id = (end($woocommerce_array))->id + 1;	
        			array_push($woocommerce_array, ["id"=>$id,"name"=>"WooCommerce","app"=>"e-Commerce"]);
        		}
        		
        		
        		$item->woocommerce = json_encode($woocommerce_array);
        		$item->save();
        	} else {
        		$woocommerce_info_array = array(["id"=>0,"name"=>"WooCommerce","app"=>"e-Commerce"]);
                
				$woocommerce_info = json_encode($woocommerce_info_array);
				
                DB::table('user_app')->insert([
                    'user_id'  	  => $current_user_id,
                    'visma' 	  => null,
                    'stripe' 	  =>null,
                    'woocommerce' => $woocommerce_info,
                    'shopify' 	  => null,
                ]);
        	}
        }
    	
    	return redirect('manageapp');

    }
}
