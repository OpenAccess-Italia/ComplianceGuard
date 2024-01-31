<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Hash;

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
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        $login = request()->input('identity');  
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$field => $login]);   
        return $field;
    }


    public function login(Request $request)
    {
        $username = $request->input('email');
        $password = $request->input('password');
        if(filter_var($username, FILTER_VALIDATE_EMAIL)) {
            //IF IS AN EMAIL ADDRESS
            if(\App\User::where('email',$username)->first()){
                $salt = \App\User::where('email',$username)->first()->salt;
                $saltedpassword = hash('sha512', $password.$salt);
                $credentials = array("email" => $username,"password" => $saltedpassword);
                if(\Auth::attempt($credentials,$request->has('remember'))){
                    if(\Auth::user()->enabled == 1){
                        return redirect()->intended($this->redirectPath());
                    }else{
                        \Auth::logout();
                        return redirect()->back()->withInput()->withErrors([ 'email' => "User not authorized for the platform" ]);
                    }
                }else{
                    return redirect()->back()->withInput()->withErrors([ 'email' => "Incorrect login credentials" ]);
                }
            }else{
                return redirect()->back()->withInput()->withErrors([ 'email' => "Incorrect login credential" ]);
            }
        }else{
            //IF IS AN USERNAME
            if(\App\User::where('name',$username)->first()){
                $salt = \App\User::where('name',$username)->first()->salt;
                $saltedpassword = hash('sha512', $password.$salt);
                $credentials = array("name" => $username,"password" => $saltedpassword);
                if(\Auth::attempt($credentials,$request->has('remember'))){
                    if(\Auth::user()->enabled == 1){
                        return redirect()->intended($this->redirectPath());
                    }else{
                        \Auth::logout();
                        return redirect()->back()->withInput()->withErrors([ 'email' => "User not authorized for the platform" ]);
                    }
                }else{
                    return redirect()->back()->withInput()->withErrors([ 'email' => "Incorrect login credentials" ]);
                }
            }else{
                return redirect()->back()->withInput()->withErrors([ 'email' => "Incorrect login credential" ]);
            }
        }
    }
}
