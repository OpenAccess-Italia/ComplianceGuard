<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use DataTables;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Mail;

class UserController extends Controller
{
    //
    public function __construct()
    {
    }

    public function datatable_user(Request $request)
    {
        if ($request->ajax()) {
            $data = User::query();

            return Datatables::of($data)
                ->rawColumns(
                    ['name', 'email']
                )
                ->addColumn('admin', function ($row) {
                    if ($row->admin) {
                        return '<i class="fas fa-check text-dark"></i>';
                    }
                })
                ->addColumn('enabled', function ($row) {
                    if ($row->enabled) {
                        return '<i class="fas fa-check text-dark"></i>';
                    }
                })
                ->addColumn('piracy', function ($row) {
                    if ($row->piracy) {
                        return '<i class="fas fa-check text-dark"></i>';
                    }
                })
                ->addColumn('cncpo', function ($row) {
                    if ($row->cncpo) {
                        return '<i class="fas fa-check text-dark"></i>';
                    }
                })
                ->addColumn('adm', function ($row) {
                    if ($row->adm) {
                        return '<i class="fas fa-check text-dark"></i>';
                    }
                })
                ->addColumn('manual', function ($row) {
                    if ($row->manual) {
                        return '<i class="fas fa-check text-dark"></i>';
                    }
                })
                ->addColumn('action', function ($row) {
                    return "<a href=\"/admin/users/view/$row->id/\" class=\"edit btn btn-primary btn-icon\"><i class=\"fas fa-pencil-alt\"></i></a>";
                })
                ->escapeColumns('action')->make(true);
        }
    }

    public function add_user(Request $request)
    {
        $errors = [];
        if ($request->filled(['name', 'email', 'friendly_name'])) {
            // CHECK USERNAME
            if (strlen($request->input('name')) >= 8) {
                if (User::where('name', $request->input('name'))->get()->first()) {
                    $errors[] = 'Username already exists';
                }
            } else {
                $errors[] = 'The username must have at least 8 characters';
            }
        } else {
            $errors[] = 'Missing required fields';
        }
        if (count($errors) == 0) {
            $newuser = new User;
            if ($request->hasFile('avatar')) {
                if ($request->file('avatar')->isValid()) {
                    $avatar = $request->file('avatar');
                    if ($avatar->getSize() <= 102400) {
                        $avatar_path = $avatar->getRealPath();
                        $type = $avatar->extension();
                        $data = file_get_contents($avatar_path);
                        $newuser->avatar = 'data:image/'.$type.';base64,'.base64_encode($data);
                    } else {
                        $result = new \StdClass;
                        $result->status = 'KO';
                        $result->errors = ['Maximum logo size exceeded'];
                        ActionLogController::log(Auth::user()->id, Auth::user()->name, 'failed to add user ('.implode(',', $result->errors).')');

                        return json_encode($result);
                    }
                } else {
                    $result = new \StdClass;
                    $result->status = 'KO';
                    $result->errors = ['Invalid logo file'];
                    ActionLogController::log(Auth::user()->id, Auth::user()->name, 'failed to add user ('.implode(',', $result->errors).')');

                    return json_encode($result);
                }
            }
            $password = User::generatePassword(16);
            $salt = hash('sha512', uniqid(random_int(1, mt_getrandmax()), true));
            $newuser->password = \Hash::make(hash('sha512', hash('sha512', $password).$salt));
            $newuser->salt = $salt;
            $newuser->email = $request->input('email');
            $newuser->name = $request->input('name');
            $newuser->friendly_name = $request->input('friendly_name');
            $newuser->admin = (int) $request->has('admin');
            $newuser->enabled = (int) $request->has('enabled');
            $newuser->piracy = (int) $request->has('piracy');
            $newuser->cncpo = (int) $request->has('cncpo');
            $newuser->adm = (int) $request->has('adm');
            $newuser->manual = (int) $request->has('manual');
            try {
                $result = new \StdClass;
                if ($newuser->save()) {
                    $result->status = 'OK';
                    $result->id = $newuser->id;
                    ActionLogController::log(Auth::user()->id, Auth::user()->name, "succeded to add user $newuser->id");
                    if (Mail::send('mail.welcome', [
                                'alias' => $newuser->friendly_name,
                                'username' => $newuser->name,
                                'email' => $newuser->email,
                                'password' => $password,
                            ],
                            static function (Message $message) use ($newuser) {
                                $message->subject('Welcome in '.config('app.name'));
                                $message->from(\Settings::get(\App\SettingKeys::MAIL_FROM_ADDRESS), env('MAIL_FROM_NAME'));
                                $message->to($newuser->email);
                            }
                        )
                    ) {
                        ActionLogController::log(Auth::user()->id, Auth::user()->name, "succeded to send welcome mail tu user $newuser->id");
                    } else {
                        ActionLogController::log(Auth::user()->id, Auth::user()->name, "failed to send welcome mail tu user $newuser->id");
                    }

                    return json_encode($result);
                }

                $result->status = 'KO';
                $result->errors = ['Failed insert'];
                ActionLogController::log(Auth::user()->id, Auth::user()->name, 'failed to add user ('.implode(',', $result->errors).')');

                return json_encode($result);
            } catch (QueryException $e) {
                $result = new \StdClass;
                $result->status = 'KO';
                $result->errors = ['Failed insert'];
                ActionLogController::log(Auth::user()->id, Auth::user()->name, 'failed to add user ('.implode(',', $result->errors).')');

                return json_encode($result);
            }
        } else {
            $result = new \StdClass;
            $result->status = 'KO';
            $result->errors = array_unique($errors);
            ActionLogController::log(Auth::user()->id, Auth::user()->name, 'failed to add user ('.implode(',', $result->errors).')');

            return json_encode($result);
        }
    }

    public function view_user(Request $request, $id)
    {
        $user = User::find($id);
        if ($user) {
            ActionLogController::log(Auth::user()->id, Auth::user()->name, "succeded to view user $id");

            return view('admin.users.view', [
                'user' => $user,
            ]);

        }

        ActionLogController::log(Auth::user()->id, Auth::user()->name, "failed to view user $id (not exists)");
        Auth::logout();

        return redirect('/');
    }

    public function save_user(Request $request)
    {
        $errors = [];
        if (! $request->filled('user_id')) {
            Auth::logout();
            $errors[] = 'Required field missing';
        }
        if (count($errors) == 0) {
            if (User::find($request->input('user_id'))) {
                try {
                    if ($request->hasFile('avatar')) {
                        if ($request->file('avatar')->isValid()) {
                            $brand_logo = $request->file('avatar');
                            if ($brand_logo->getSize() <= 102400) {
                                $brand_logo_path = $brand_logo->getRealPath();
                                $type = $brand_logo->extension();
                                $data = file_get_contents($brand_logo_path);
                                $base64 = 'data:image/'.$type.';base64,'.base64_encode($data);
                                User::where('id', $request->input('user_id'))->update([
                                    'avatar' => $base64,
                                ]);
                            } else {
                                $result = new \StdClass;
                                $result->status = 'KO';
                                $result->errors = ['Maximum logo size exceeded'];
                                ActionLogController::log(Auth::user()->id, Auth::user()->name, 'failed to save user '.$request->input('user_id').' ('.implode(',', $result->errors).')');

                                return json_encode($result);
                            }
                        } else {
                            $result = new \StdClass;
                            $result->status = 'KO';
                            $result->errors = ['Invalid logo file'];
                            ActionLogController::log(Auth::user()->id, Auth::user()->name, 'failed to save user '.$request->input('user_id').' ('.implode(',', $result->errors).')');

                            return json_encode($result);
                        }
                    }
                    if ($request->has('delete_avatar')) {
                        User::where('id', $request->input('user_id'))->update([
                            'avatar' => null,
                        ]);
                    }
                    User::where('id', $request->input('user_id'))->update([
                        'friendly_name' => $request->input('friendly_name'),
                        'admin' => (int) $request->has('admin'),
                        'enabled' => (int) $request->has('enabled'),
                        'piracy' => (int) $request->has('piracy'),
                        'cncpo' => (int) $request->has('cncpo'),
                        'adm' => (int) $request->has('adm'),
                        'manual' => (int) $request->has('manual'),
                    ]);
                    $result = new \StdClass;
                    $result->status = 'OK';
                    $result->id = $request->input('user_id');
                    ActionLogController::log(Auth::user()->id, Auth::user()->name, 'succeded to save user '.$request->input('user_id'));

                    return json_encode($result);
                } catch (QueryException $e) {
                    $result = new \StdClass;
                    $result->status = 'KO';
                    $result->errors = ['Update failed'];
                    ActionLogController::log(Auth::user()->id, Auth::user()->name, 'failed to save user '.$request->input('user_id').' ('.implode(',', $result->errors).')');

                    return json_encode($result);
                }
            } else {
                ActionLogController::log(Auth::user()->id, Auth::user()->name, 'failed to save user '.$request->input('user_id').' (not exists)');
                Auth::logout();

                return redirect('/');
            }
        } else {
            $result = new \StdClass;
            $result->status = 'KO';
            $result->errors = array_unique($errors);
            ActionLogController::log(Auth::user()->id, Auth::user()->name, 'failed to save user '.$request->input('user_id').' ('.implode(',', $result->errors).')');

            return json_encode($result);
        }
    }
}
