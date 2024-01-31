<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Notifications\Messages\MailMessage;
use DataTables;
use DB;

class UserController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth.admin');
    }

    public function datatable_user(Request $request){
        if($request->ajax()){
            $data = \App\User::get();
            return Datatables::of($data)
                ->rawColumns(
                    ['name',
                    'email']
                )
                ->addColumn('admin', function($row){
                    if($row->admin){
                        return '<i class="fas fa-check text-dark"></i>';
                    }
                })
                ->addColumn('enabled', function($row){
                    if($row->enabled){
                        return '<i class="fas fa-check text-dark"></i>';
                    }
                })
                ->addColumn('piracy', function($row){
                    if($row->piracy){
                        return '<i class="fas fa-check text-dark"></i>';
                    }
                })
                ->addColumn('cncpo', function($row){
                    if($row->cncpo){
                        return '<i class="fas fa-check text-dark"></i>';
                    }
                })
                ->addColumn('adm', function($row){
                    if($row->adm){
                        return '<i class="fas fa-check text-dark"></i>';
                    }
                })
                ->addColumn('manual', function($row){
                    if($row->manual){
                        return '<i class="fas fa-check text-dark"></i>';
                    }
                })
                ->addColumn('action', function($row){
                    $btn = "<a href=\"/admin/users/view/$row->id/\" class=\"edit btn btn-primary btn-icon\"><i class=\"fas fa-pencil-alt\"></i></a>";
                    return $btn;
                })
                ->escapeColumns('action')->make(true);
        }
    }

    public function add_user(Request $request){
        $errors = array();
        if($request->filled(["name","email","friendly_name"])){
            //CHECK USERNAME
            if(strlen($request->input("name")) >= 8){
                $user = \App\User::where("name",$request->input("name"))->get()->first();
                if($user){
                    $errors[] = "Username already exists";
                }
            }else{
                $errors[] = "The username must have at least 8 characters";
            }
        }else{
            $errors[] = "Missing required fields";
        }
        if(count($errors) == 0){
            $newuser = new \App\User();
            if($request->hasFile('avatar')){
                if($request->file('avatar')->isValid()){
                    $avatar = $request->file('avatar');
                    if($avatar->getSize() <= 102400){
                        $avatar_path = $avatar->getRealPath();
                        $type = $avatar->extension();
                        $data = file_get_contents($avatar_path);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                        $newuser->avatar = $base64;
                    }else{
                        $result = new \StdClass();
                        $result->status = "KO";
                        $result->errors = ["Maximum logo size exceeded"];
                        \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"failed to add user (".implode(",",$result->errors).")");
                        return json_encode($result);
                    }   
                }else{
                    $result = new \StdClass();
                    $result->status = "KO";
                    $result->errors = ["Invalid logo file"];
                    \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"failed to add user (".implode(",",$result->errors).")");
                    return json_encode($result);
                }
            }
            $password = self::generatePassword(16);
            $salt = hash('sha512',uniqid(mt_rand(1, mt_getrandmax()),true));
            $newuser->password = \Hash::make(hash('sha512',hash('sha512',$password).$salt));
            $newuser->salt = $salt;
            $newuser->email = $request->input("email");
            $newuser->name = $request->input("name");
            $newuser->friendly_name = $request->input("friendly_name");
            $newuser->admin = (int)$request->has("admin");
            $newuser->enabled = (int)$request->has("enabled");
            $newuser->piracy = (int)$request->has("piracy");
            $newuser->cncpo = (int)$request->has("cncpo");
            $newuser->adm = (int)$request->has("adm");
            $newuser->manual = (int)$request->has("manual");
            try{
                if($newuser->save()){
                    $result = new \StdClass();
                    $result->status = "OK";
                    $result->id = $newuser->id;
                    \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"succeded to add user $newuser->id");
                    \Mail::send('mail.welcome', [
                        'alias' => $newuser->friendly_name,
                        'username' => $newuser->name,
                        'email' => $newuser->email,
                        'password' => $password
                    ], 
                    function (\Illuminate\Mail\Message $message) use ($newuser){
	                	$message->subject("Welcome in ".env('APP_NAME'));
	                	$message->from(env('MAIL_FROM_ADDRESS'),env('MAIL_FROM_NAME'));
	                	$message->to($newuser->email);
                    });
                    if(count(\Mail::failures()) == 0){
                        \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"succeded to send welcome mail tu user $newuser->id");
                    }else{
                        \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"failed to send welcome mail tu user $newuser->id");
                    }
                    return json_encode($result);
                }else{
                    $result = new \StdClass();
                    $result->status = "KO";
                    $result->errors = ["Failed insert"];
                    \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"failed to add user (".implode(",",$result->errors).")");
                    return json_encode($result);
                }
            }catch(\Illuminate\Database\QueryException $e){
                $result = new \StdClass();
                $result->status = "KO";
                $result->errors = ["Failed insert"];
                \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"failed to add user (".implode(",",$result->errors).")");
                return json_encode($result);
            }
        }else{
            $result = new \StdClass();
            $result->status = "KO";
            $result->errors = array_unique($errors);
            \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"failed to add user (".implode(",",$result->errors).")");
            return json_encode($result);
        }
    }

    public function view_user(Request $request,$id){
        $user = \App\User::find($id);
        if($user){
            \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"succeded to view user $id");
            return view('admin.users.view', [
                "user" => $user
            ]);

        }else{
            \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"failed to view user $id (not exists)");
            \Auth::logout();
            return redirect('/');
        }
    }

    public function save_user(Request $request){
        $errors = array();
        if(!$request->filled("user_id")){
            \Auth::logout();
            $errors[] = "Required field missing";
        }
        if(count($errors) == 0){
            $user = \App\User::find($request->input("user_id"));
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
                                \App\User::where("id",$request->input("user_id"))->update([
                                    'avatar' => $base64,
                                ]);
                            }else{
                                $result = new \StdClass();
                                $result->status = "KO";
                                $result->errors = ["Maximum logo size exceeded"];
                                \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"failed to save user ".$request->input("user_id")." (".implode(",",$result->errors).")");
                                return json_encode($result);
                            }   
                        }else{
                            $result = new \StdClass();
                            $result->status = "KO";
                            $result->errors = ["Invalid logo file"];
                            \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"failed to save user ".$request->input("user_id")." (".implode(",",$result->errors).")");
                            return json_encode($result);
                        }
                    }
                    if($request->has("delete_avatar")){
                        \App\User::where("id",$request->input("user_id"))->update([
                            'avatar' => null,
                        ]);
                    }
                    \App\User::where("id",$request->input("user_id"))->update([
                        'friendly_name' => $request->input("friendly_name"),
                        'admin' => (int)$request->has("admin"),
                        'enabled' => (int)$request->has("enabled"),
                        'piracy' => (int)$request->has("piracy"),
                        'cncpo' => (int)$request->has("cncpo"),
                        'adm' => (int)$request->has("adm"),
                        'manual' => (int)$request->has("manual")
                    ]);
                    $result = new \StdClass();
                    $result->status = "OK";
                    $result->id = $request->input("user_id");
                    \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"succeded to save user ".$request->input("user_id"));
                    return json_encode($result);
                }catch(\Illuminate\Database\QueryException $e){
                    $result = new \StdClass();
                    $result->status = "KO";
                    $result->errors = ["Update failed"];
                    \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"failed to save user ".$request->input("user_id")." (".implode(",",$result->errors).")");
                    return json_encode($result);
                }
            }else{
                \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"failed to save user ".$request->input("user_id")." (not exists)");
                \Auth::logout();
                return redirect('/');
            }
        }else{
            $result = new \StdClass();
            $result->status = "KO";
            $result->errors = array_unique($errors);
            \App\Http\Controllers\Admin\ActionLogController::log(\Auth::user()->id,\Auth::user()->name,"failed to save user ".$request->input("user_id")." (".implode(",",$result->errors).")");
            return json_encode($result);
        }
    }

    private static function generatePassword($length){
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < $length; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass);
    }
}
