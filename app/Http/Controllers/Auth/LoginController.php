<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\ToolbxAPI;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $toolbxAPI = new ToolbxAPI;

        $response = $toolbxAPI->post('admin/adminlogin', '', [
            'Email' => $request->get('email'),
            'Password' => $request->get('password')
        ]);

        if( $response->message_code == '1000' )
        {
            Session::put('user_data', $response->data_text);
            Session::put('logged_in', true);
            return 'ok';
        }
        else
        {
            return response()->json($response);
        }
    }
}
