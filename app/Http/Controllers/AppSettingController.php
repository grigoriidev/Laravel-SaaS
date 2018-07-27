<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Apps;
use App\CurrentUser;
use App\Visma;
use App\Models\Token;
use App\StripeVismaSchedule;
use App\WoovismaSchedule;
use App\Workflow;
use App\WorkflowData;
use App\StripeWorkflow;
use App\StripeWorkflowData;

class AppSettingController extends Controller
{
     public function __construct()
    {
       
        session_start();
    }

    public function showSettingPage($app, $index, $name) {
    	// echo ($app.$index);exit;
    	$REDIRECT_URI = "https://epti.eratio.com/api/eaccounting";
        $STATE = md5(time());
        $visma_authorize_url = env('VISMA_AUTH_ENDPOINT') . "/connect/authorize?client_id=". env('VISMA_CLIENT_ID') ."&redirect_uri=". $REDIRECT_URI ."&scope=". env('VISMA_SCOPES') ."&state=". $STATE ."&response_type=code&prompt=login";
        
    	return view('pages.manageapp.appsetting.credentials-'.$app,['app' => $app, 'id' => $index, 'name' => $name, 'authUrl' => $visma_authorize_url]);
    	// return view('pages.manageapp.appsetting.credentials',['app' => $app, 'id' => $index]);
    }

    public function renameApp() {
    	$new_name = Input::get('appname');
    	$app_type = Input::get('app');
    	$id = Input::get('id');
    	
    	$current_user = $_SESSION["user"];
    	$apps = Apps::where('user_id',$current_user['id'])->first();
		if($app_type == 'visma') {
			$app_array = json_decode($apps->visma);
			$return_uri = 'Accounting';
		}
		if ($app_type == 'woocommerce') {
			$app_array = json_decode($apps->woocommerce);
			$return_uri = 'e-Commerce';
		}
		if ($app_type == 'stripe') {
			$app_array = json_decode($apps->stripe);
			$return_uri = 'Credit-Card';
		}
		// var_dump($app_array);exit;
		foreach($app_array as $app) {
			if($id == strval($app->id)) {
				$app->name = $new_name;
				
				Apps::where('user_id',$current_user['id'])->update(array($app_type => json_encode($app_array)));
			}
		}
		return redirect('manageapp');
		// return redirect(url()->previous());
    }

    public function deleteApp() {
    	$new_name = Input::get('appname');
    	$app_type = Input::get('app');
    	$id = Input::get('id');
		$index = 0;
    	$current_user = $_SESSION["user"];
    	$apps = Apps::where('user_id',$current_user['id'])->first();
		if($app_type == 'visma') {
			$app_array = json_decode($apps->visma);
			$return_uri = 'Accounting';
			
			Visma::where('user_id', $current_user['id'])->delete();
			Token::where('user_id', $current_user['id'])->delete();

			if(StripeVismaSchedule::where('user_id', $current_user['id'])->first()) {

				StripeVismaSchedule::where('user_id', $current_user['id'])->delete();
			}

			if(WoovismaSchedule::where('user_id', $current_user['id'])->first()) {

				WoovismaSchedule::where('user_id', $current_user['id'])->delete();
			}

			if(Workflow::where('user_id', $current_user['id'])->first()) {

				Workflow::where('user_id', $current_user['id'])->delete();
			}

			if(WorkflowData::where('user_id', $current_user['id'])->first()) {
				
				WorkflowData::where('user_id', $current_user['id'])->delete();
			}

			if(StripeWorkflow::where('user_id', $current_user['id'])->first()) {
				
				StripeWorkflow::where('user_id', $current_user['id'])->delete();
			}

			if(StripeWorkflowData::where('user_id', $current_user['id'])->first()) {
				
				StripeWorkflowData::where('user_id', $current_user['id'])->delete();
			}
			
		}
		if ($app_type == 'woocommerce') {
			$app_array = json_decode($apps->woocommerce);
			$return_uri = 'e-Commerce';
			if(WoovismaSchedule::where('user_id', $current_user['id'])->first()) {

				WoovismaSchedule::where('user_id', $current_user['id'])->delete();
			}

			if(Workflow::where('user_id', $current_user['id'])->first()) {

				Workflow::where('user_id', $current_user['id'])->delete();
			}

			if(WorkflowData::where('user_id', $current_user['id'])->first()) {
				
				WorkflowData::where('user_id', $current_user['id'])->delete();
			}
		}

		if ($app_type == 'stripe') {
			$app_array = json_decode($apps->stripe);
			$return_uri = 'Credit-Card';
			if(StripeVismaSchedule::where('user_id', $current_user['id'])->first()) {

				StripeVismaSchedule::where('user_id', $current_user['id'])->delete();
			}

			if(StripeWorkflow::where('user_id', $current_user['id'])->first()) {
				
				StripeWorkflow::where('user_id', $current_user['id'])->delete();
			}

			if(StripeWorkflowData::where('user_id', $current_user['id'])->first()) {
				
				StripeWorkflowData::where('user_id', $current_user['id'])->delete();
			}
		}
		// var_dump($app_array);exit;
		foreach($app_array as $app) {
			if($id == strval($app->id)) {
				unset($app_array[$index]);
			 	$app_array = array_values($app_array);
			 	if(count($app_array) != 0) {

					Apps::where('user_id',$current_user['id'])->update(array($app_type => json_encode($app_array)));
			 	}
			 	if(count($app_array) == 0) {

			 		Apps::where('user_id',$current_user['id'])->update(array($app_type => null));	
			 	}

			}
			$index++;
		}
		return redirect('manageapp');
		// return redirect(url()->previous());
    }
}
