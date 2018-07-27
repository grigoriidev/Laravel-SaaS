<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Support\Facades\Input;
use App\Models\Token;
use App\Visma;
use App\CurrentUser;
use App\Woocommerce;
use App\Http\Controller\VismaDataController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VismaAuthController extends Controller
{
    public function __construct()
    {
        session_start();
        set_time_limit(0);
    }

    /**
     * authorization_code
     */
    public function authorization(Request $request)
    {

        $code = Input::get('code');
        $state = Input::get('state');

        if ($code) {

            $REDIRECT_URI = "https://epti.eratio.com/api/eaccounting";
            $data = http_build_query([
                "grant_type" => "authorization_code",
                "redirect_uri" => $REDIRECT_URI,
                "code" => $code
            ]);

            $auth_str = base64_encode(env('VISMA_CLIENT_ID') . ':' . env('VISMA_CLIENT_SECRET'));
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => env('VISMA_AUTH_ENDPOINT') . "/connect/token",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => array(
                    "Accept: application/json",
                    "Authorization: Basic ". $auth_str,
                    "Cache-Control: no-cache",
                    "Content-Type: application/x-www-form-urlencoded"
                ),
            ));

            $response = curl_exec($curl);
            
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {

                echo "cURL Error #:" . $err;

            } else {
                
                $response = json_decode($response);
                $auth_info = [];
                $token_info = [];
                $tokentable_info = Token::all();
                $current_user = $_SESSION["user"];
                
                foreach ($response as $key => $value) {
                    # code...
                    $auth_info[] = $value;
                } 

                if(count(json_decode($tokentable_info)) == 0) {
                    
                    DB::table('token_info')->insert([
                        'user_id'  => $current_user['id'],
                        'refresh_token' => $auth_info[3],
                        'access_token' => $auth_info[0],
                    ]);

                } else { 
                    if(Token::where('user_id', $current_user['id'])->first()) {

                        // if(count($auth_info) < 4) {
                            
                        //     Token::where('user_id',$current_user['id'])->update(array('access_token' => $auth_info[0]));
                        // } else {
                            
                        //     Token::where('user_id',$current_user['id'])->update(array('access_token' => $auth_info[0], 'refresh_token' => $auth_info[3]));
                        // }
                        if(count($auth_info) < 4) {
                             DB::table('token_info')->insert([
                                'user_id'  => $current_user['id'],
                                'access_token' => $auth_info[0],
                            ]);
                        } else {
                            DB::table('token_info')->insert([
                                'user_id'  => $current_user['id'],
                                'refresh_token' => $auth_info[3],   
                                'access_token' => $auth_info[0],
                            ]);
                        }

                    } else {

                        if(count($auth_info) < 4) {
                             DB::table('token_info')->insert([
                                'user_id'  => $current_user['id'],
                                'access_token' => $auth_info[0],
                            ]);
                        } else {
                            DB::table('token_info')->insert([
                                'user_id'  => $current_user['id'],
                                'refresh_token' => $auth_info[3],   
                                'access_token' => $auth_info[0],
                            ]);
                        }
                        
                    }
                }
                $new_token_array = Token::where('user_id',$current_user['id'])->orderBy('id','desc')->first();
                
                $_SESSION["new_token_id"] = $new_token_array["id"];
                $visma_data = $this->attachment();

                if (!isset($response->error)) {     
                    
                } else {

                    $error = $response->error;
                }
            }
        }
        return redirect('manageapp/assigned/visma');
    }

    public function attachment() {
       $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, env('VISMA_API_ENDPOINT')."/v2/companysettings");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        
        $token_info = [];
        $token_info = Token::where('user_id',$_SESSION["user"]["id"])->orderBy('id', 'desc')->first();
        $access_token = $token_info['access_token'];

        /*current_user table reset*/
        DB::table('current_user')->truncate();
        DB::table('current_user')->insert(['current_user_id'=>$_SESSION['user']['id']]);

        $headers = array();
        $headers[] = "Authorization: Bearer ".$access_token;

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        
        if($result != null) {

            curl_setopt($ch, CURLOPT_URL, env('VISMA_API_ENDPOINT')."/v2/vatcodes");
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result_vatcode = curl_exec($ch);

        /*get accounts debit, sales credit, product credit, shipping vat*/
            $accounts = [];

            for ($i=1; $i<6; $i++) {

                curl_setopt($ch, CURLOPT_URL, env('VISMA_API_ENDPOINT').'/v2/accounts?$page='.$i.'&$pagesize=100');
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                
                $result = curl_exec($ch);
                $result = json_decode($result)->Data;
               
                array_push($accounts, $result);
            }         

            $current_user_id = $_SESSION['user']['id'];
            $visma_table_info = Visma::all();
            
            if(count(json_decode($visma_table_info)) == 0) {
                    
                DB::table('visma')->insert([
                    'user_id'     => $current_user_id,
                    'visma_data'  => '',
                    'vatcode'     => $result_vatcode,
                    'accounts'    => serialize($accounts),
                    'accounts_active' => '',
                ]);
              
            } else {
                if(Visma::where('user_id',$current_user_id)->first()) {

                    Visma::where('user_id',$current_user_id)->update(array('vatcode' => $result_vatcode,
                                                                            'accounts' => serialize($accounts),'accounts_active' => '',));
                } else {

                    DB::table('visma')->insert([
                        'user_id'     => $current_user_id,
                        'visma_data'  => '',
                        'vatcode'     => $result_vatcode,
                        'accounts'             => serialize($accounts),
                        'accounts_active' => '',
                    ]);
                }
            }
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
        }

        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
      
        curl_close ($ch);
        
    }

    public function authAccessToken() {

        $current_user = $_SESSION["user"];

        $array = json_decode(Token::where('user_id',$current_user['id'])->orderBy('id','desc')->first());  //token information
        $access_token = $array->access_token;

        /*authorize with access token*/
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, env('VISMA_API_ENDPOINT')."/v2/companysettings");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

        $headers = array();
        $headers[] = "Authorization: Bearer ".$access_token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $id = "1f737f09-11d7-4da1-9724-b1f0e460695d";
        $result = curl_exec($ch);

        if($result != null) {
           
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, env('VISMA_API_ENDPOINT')."/v2/companysettings");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

            $headers = array();
            $headers[] = "Authorization: Bearer ".$access_token;
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            
            /*get vatcode data*/

            /*get accounts debit, sales credit, product credit, shipping vat*/
            if(Visma::where('user_id',$current_user['id'])->first()) {

            } else {

                curl_setopt($ch, CURLOPT_URL,  env('VISMA_API_ENDPOINT')."/v2/vouchers/");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                $result = curl_exec($ch);
                $result_array = json_decode($result);
                $result_array_data = $result_array->Data; 

                $accounts = [];

                for ($i=1; $i<6; $i++) {

                    curl_setopt($ch, CURLOPT_URL, env('VISMA_API_ENDPOINT').'/v2/accounts?$page='.$i.'&$pagesize=100');
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    
                    $result = curl_exec($ch);
                    $result = json_decode($result)->Data;
                   
                    array_push($accounts, $result);
                }         

                if(empty($result_array_data)) {
                    $result_invoice_data = null;
                } else {
                    $result_invoice_data = $result_array_data[0]->Rows; 
                } 
                $visma_table_info = Visma::all();

                if(count(json_decode($visma_table_info)) == 0) {
                        
                    DB::table('visma')->insert([
                        'user_id'     => $user_id,
                        'visma_data'  => '',
                        'vatcode'     => $result_vatcode,
                        'accounts'             => serialize($accounts),
                        'accounts_active' => '',
                    ]);

                } else {
                    if(Visma::where('user_id',$user_id)->first()) {

                        Visma::where('user_id',$user_id)->update(array('vatcode' => $result_vatcode,
                                                                                'accounts' => serialize($accounts),'accounts_active' => '',));
                    } else {

                        DB::table('visma')->insert([
                            'user_id'     => $user_id,
                            'visma_data'  => '',
                            'vatcode'     => $result_vatcode,
                            'accounts'             => serialize($accounts),
                            'accounts_active' => '',
                        ]);
                    }
                }
            }
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
        }
        return redirect('manageapp/assigned/visma');
    }

    public function authRefreshToken() {

        $ch = curl_init();
   
        $user_id = $_SESSION["user"]["id"];
        $tokenArr = json_decode(Token::where('user_id',$user_id)->first());
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

        /*Token::where('user_id',$user_id)->update(array('refresh_token'=>$new_refresh_token, 'access_token'=>$new_access_token));*/
        Token::where('user_id',$user_id)->delete();
        DB::table('token_info')->insert([
            'user_id'  => $user_id,
            'refresh_token' => $new_refresh_token,
            'access_token' => $new_access_token,
        ]);   
        /*end of response*/

        /*authorization with new access_token*/
        $access_token = $new_access_token;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, env('VISMA_API_ENDPOINT')."/v2/companysettings");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

        $headers = array();
        $headers[] = "Authorization: Bearer ".$access_token;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $id = "1f737f09-11d7-4da1-9724-b1f0e460695d";
        $result = curl_exec($ch);
        if($result != null) {

            curl_setopt($ch, CURLOPT_URL,  env('VISMA_API_ENDPOINT').'/v2/vouchers?$page=6');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $result = curl_exec($ch);
            $result_array = json_decode($result);
            $result_array_data = $result_array->Data; 

            /*get accounts debit, sales credit, product credit, shipping vat*/
            if(Visma::where('user_id',$user_id)->first()) {
                /*get vatcode data*/
                curl_setopt($ch, CURLOPT_URL, env('VISMA_API_ENDPOINT')."/v2/attachments");
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $result_vatcode = curl_exec($ch);
           

            } else {
                /*get vatcode data*/
                curl_setopt($ch, CURLOPT_URL, env('VISMA_API_ENDPOINT')."/v2/attachments");
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $result_vatcode = curl_exec($ch);
           

                $accounts = [];

                for ($i=1; $i<6; $i++) {

                    curl_setopt($ch, CURLOPT_URL, env('VISMA_API_ENDPOINT').'/v2/accounts?$page='.$i.'&$pagesize=100');
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    
                    $result = curl_exec($ch);
                    $result = json_decode($result)->Data;
                   
                    array_push($accounts, $result);
                }              
                if(empty($result_array_data)) {
                    $result_invoice_data = null;
                } else {
                    $result_invoice_data = $result_array_data[0]->Rows; 
                } 
                $visma_table_info = Visma::all();

                if(count(json_decode($visma_table_info)) == 0) {
                        
                    DB::table('visma')->insert([
                        'user_id'     => $user_id,
                        'visma_data'  => '',
                        'vatcode'     => $result_vatcode,
                        'accounts'             => serialize($accounts),
                        'accounts_active' => '',
                    ]);

                } else {
                    if(Visma::where('user_id',$user_id)->first()) {

                        Visma::where('user_id',$user_id)->update(array('vatcode' => $result_vatcode,
                                                                                'accounts' => serialize($accounts),'accounts_active' => '',));
                    } else {

                        DB::table('visma')->insert([
                            'user_id'     => $user_id,
                            'visma_data'  => '',
                            'vatcode'     => $result_vatcode,
                            'accounts'    => serialize($accounts),
                            'accounts_active' => '',
                        ]);
                    }
                }
            }
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
        }
        return redirect('manageapp/assigned/visma');
    }

    
}
        