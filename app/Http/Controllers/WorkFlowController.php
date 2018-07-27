<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Apps;
use Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
class WorkFlowController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {
    	$user = Auth::user();
        if(count(json_decode(Apps::all())) == 0) {
            $assigned_app = null;
        } else {
            
                $assigned_app = Apps::where('user_id',$user['id'])->first();
                $assigned_app =json_decode($assigned_app["visma"]);
        }
        return view('pages.manageapp.workflows.workflows',['assigned_app' => $assigned_app]);
    }
}
