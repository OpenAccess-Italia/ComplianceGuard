<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class ProfileController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function save(Request $request){
        $errors = array();
        if(!$request->filled("friendly_name")){
            \Auth::logout();
            $errors[] = "Required field missing";
        }
        if(count($errors) == 0){
            $user = \App\User::find(\Auth::user()->id);
            if($user){
                try{
                    if($request->hasFile('avatar')){
                        if($request->file('avatar')->isValid()){
                            $brand_logo = $request->file('avatar');
                            if($brand_logo->getSize() <= 102400){
                                $brand_logo_path = $brand_logo->getRealPath();
                                $type = $brand_logo->extension();
                                $data = file_get_contents($brand_logo_path);
                                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                                \App\User::where("id",\Auth::user()->id)->update([
                                    'avatar' => $base64,
                                ]);
                            }else{
                                $result = new \StdClass();
                                $result->status = "KO";
                                $result->errors = ["Maximum logo size exceeded"];
                                \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"failed to save user ".\Auth::user()->id." (".implode(",",$result->errors).")");
                                return json_encode($result);
                            }   
                        }else{
                            $result = new \StdClass();
                            $result->status = "KO";
                            $result->errors = ["Invalid logo file"];
                            \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"failed to save user ".\Auth::user()->id." (".implode(",",$result->errors).")");
                            return json_encode($result);
                        }
                    }
                    if($request->has("delete_avatar")){
                        \App\User::where("id",\Auth::user()->id)->update([
                            'avatar' => null,
                        ]);
                    }
                    \App\User::where("id",\Auth::user()->id)->update([
                        'friendly_name' => $request->input("friendly_name")
                    ]);
                    $result = new \StdClass();
                    $result->status = "OK";
                    \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"succeded to save user ".\Auth::user()->id." (not exists)");
                    return json_encode($result);
                }catch(\Illuminate\Database\QueryException $e){
                    $result = new \StdClass();
                    $result->status = "KO";
                    $result->errors = ["Update failed"];
                    \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"failed to save user ".\Auth::user()->id." (".implode(",",$result->errors).")");
                    return json_encode($result);
                }
            }else{
                \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"failed to save user ".\Auth::user()->id." (not exists)");
                \Auth::logout();
                return redirect('/');
            }
        }else{
            $result = new \StdClass();
            $result->status = "KO";
            $result->errors = array_unique($errors);
            \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"failed to save user ".\Auth::user()->id." (".implode(",",$result->errors).")");
            return json_encode($result);
        }
    }
}
