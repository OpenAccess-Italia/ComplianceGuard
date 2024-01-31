<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Hash;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
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
        $this->middleware('guest');
    }

    public function reset(Request $request)
    {
        $this->validate($request, $this->rules(), $this->validationErrorMessages());
        $email = $request->input('email');
        $password = $request->input('password');
        $password_conf = $request->input('password_confirmation');
        if(\App\User::where('email',$email)->first()){
            if(\App\User::where('email',$email)->first()->enabled == 1){
                $salt = \App\User::where('email',$email)->first()->salt;
                $saltedpassword = hash('sha512', $password.$salt);
                $saltedpassword_conf = hash('sha512', $password_conf.$salt);
                $credentials = $request->only(
                    'email',
                    'password',
                    'password_confirmation',
                    'token'
                );
                $credentials["password"] = $saltedpassword;
                $credentials["password_confirmation"] = $saltedpassword_conf;
                $response = $this->broker()->reset(
                    $credentials,
                    function($user, $saltedpassword){
                        $this->resetPassword($user, $saltedpassword);
                    }
                );
                return $response == \Password::PASSWORD_RESET
                ? $this->sendResetResponse($request, $response)
                : $this->sendResetFailedResponse($request, $response);
            }else{
                return $this->sendResetFailedResponse($request,"Utente non abilitato alla piattaforma");
            }
        }else{
            return $this->sendResetFailedResponse($request,"Indirizzo email non trovato");
        }
    }
}
