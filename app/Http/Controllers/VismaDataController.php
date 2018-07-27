<?php

namespace App\Http\Controllers;
use App\Visma;
use App\Apps;
use App\CurrentUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Token;
class VismaDataController extends Controller
{
    //
    public function __construct()
    {
        // $this->middleware('auth');
        session_start();
    }
    
    public function showData()
    {
        
        $visma_sales_data = [];
        $visma_sales_title = [];
        if(!$_SESSION["user"]) {
            return redirect('/logout');
        }
        $user_id = $_SESSION["user"]["id"];

        $vismatable_info = Visma::all();
        if(Visma::where('user_id',$user_id)->first()) {
    
            $visma_data = Visma::where('user_id',$user_id)->first();
            $visma_data = json_decode($visma_data);
            if(unserialize($visma_data->visma_data)) {
                $visma_data = unserialize($visma_data->visma_data);
                
                $visma_sales_data = [];
                $visma_sales_title = [];

                foreach($visma_data as $data) {
                    
                    if($data->Rows) {

                        array_push($visma_sales_data, $data->Rows);
                    }

                    if($data->NumberAndNumberSeries) {

                        array_push($visma_sales_title, $data->NumberAndNumberSeries);
                    }
                    // array_push($visma_sales_data, $data->Rows);
                    // array_push($visma_sales_title, $data->NumberAndNumberSeries);
                }
            } else {

                $visma_sales_data = null;
            }   

        } else {

        	$visma_sales_data = null;
        }
        
        return view('pages.manageapp.visma', ['visma_data' => $visma_sales_data, 'visma_data_title' => $visma_sales_title]);
    }

    public function assignApp() {

	        $current_user = $_SESSION["user"];
            // $current_app_token_id = $_SESSION["new_token_id"];
            $current_app_token_id = (Token::where('user_id', $current_user["id"])->first())["user_id"];
 
	        $user_app_table_info = Apps::all();
	        if(count(json_decode($user_app_table_info)) == 0) {
                
	        	
                $visma_info_array = array(["id"=>0,"name"=>"Visma eaccounting","app"=>"Accounting", "token_id"=>$current_app_token_id]);
				$visma_info = json_encode($visma_info_array);
				
                DB::table('user_app')->insert([
                    'user_id'  => $current_user['id'],
                    'visma' => $visma_info,
                    'stripe' =>null,
                    'woocommerce' => null,
                    'shopify' => null,
                ]);

            } else {
            	if(Apps::where('user_id',$current_user['id'])->first()) {
            		$item = Apps::where('user_id',$current_user['id'])->first();
            		$visma_array = json_decode($item->visma);
                    if($visma_array == null) {
                        $id = 0;
                        $visma_array = array(["id"=>$id,"name"=>"Visma eaccounting","app"=>"Accounting", "token_id"=>$current_app_token_id]);
                    } else {
                        $id = (end($visma_array))->id + 1;    
                        array_push($visma_array, ["id"=>$id,"name"=>"Visma eaccounting","app"=>"Accounting", "token_id"=>$current_app_token_id]);
                    }
            		
            		
            		$item->visma = json_encode($visma_array);
            		$item->save();
            	} else {
            		$visma_info_array = array(["id"=>0,"name"=>"Visma eaccounting","app"=>"Accounting", "token_id"=>$current_app_token_id]);
	                
					$visma_info = json_encode($visma_info_array);
					
	                DB::table('user_app')->insert([
	                    'user_id'  => $current_user['id'],
	                    'visma' => $visma_info,
	                    'stripe' =>null,
	                    'woocommerce' => null,
	                    'shopify' => null,
	                ]);
            	}

            }
    	
    	return redirect('manageapp');

    }
}
