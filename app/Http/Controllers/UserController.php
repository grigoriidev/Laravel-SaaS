<?php

namespace App\Http\Controllers;

use Auth;
use App\CurrentUser;
use Illuminate\Support\Facades\DB;
class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('auth');
        session_start();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        $_SESSION["user"] = $user;
        // CurrentUser::truncate();
        // DB::table('current_user')->insert(['current_user_id'=>$user['id']]);

        if ($user->isAdmin()) {
            return view('pages.admin.home');
        }

        $visma_data = [];
        return view('pages.user.home',['visma_data' => $visma_data]);
    }
}
