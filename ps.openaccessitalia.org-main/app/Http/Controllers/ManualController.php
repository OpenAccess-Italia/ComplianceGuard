<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\ActionLogController;
use App\Models\Manual\FQDNs;
use App\Models\Manual\IPv4s;
use App\Models\Manual\IPv6s;
use Auth;
use DataTables;
use Illuminate\Http\Request;

class ManualController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth.manual');
    }

    public function datatable_fqdn(Request $request)
    {
        if ($request->ajax()) {
            $data = FQDNs::query();

            return Datatables::of($data)
                ->rawColumns(
                    ['fqdn', 'comment', 'timestamp']
                )->make(true);
        }
    }

    public function datatable_ipv4(Request $request)
    {
        if ($request->ajax()) {
            $data = IPv4s::query();

            return Datatables::of($data)
                ->rawColumns(
                    ['ipv4', 'comment', 'timestamp']
                )->make(true);
        }
    }

    public function datatable_ipv6(Request $request)
    {
        if ($request->ajax()) {
            $data = IPv6s::query();

            return Datatables::of($data)
                ->rawColumns(
                    ['ipv6', 'comment', 'timestamp']
                )->make(true);
        }
    }

    public function add_fqdn(Request $request)
    {
        if ($request->has(['value', 'comment'])) {
            if ($request->filled(['value'])) {
                ActionLogController::log(Auth::user()->id, Auth::user()->name, 'trying to add '.$request->input('value').' to manual FQDN list');
                if (self::validateFQDN($request->input('value'))) {
                    if (! FQDNs::find($request->input('value'))) {
                        $new = new FQDNs;
                        $new->fqdn = $request->input('value');
                        $new->comment = $request->input('comment');
                        if ($new->save()) {
                            ActionLogController::log(Auth::user()->id, Auth::user()->name, 'succeded to add '.$request->input('value').' to manual FQDN list');

                            return response('', 200);
                        }
                        ActionLogController::log(Auth::user()->id, Auth::user()->name, 'failed to add '.$request->input('value').' to manual FQDN list (failed insert)');

                        return response('insert failed', 500);
                    }
                    ActionLogController::log(Auth::user()->id, Auth::user()->name, 'failed to add '.$request->input('value').' to manual FQDN list (FQDN already exists)');

                    return response('FQDN already exists', 500);
                }
                ActionLogController::log(Auth::user()->id, Auth::user()->name, 'failed to add '.$request->input('value').' to manual FQDN list (invalid FQDN)');

                return response('Invalid FQDN', 500);
            }

            return response('Insert a value', 500);
        }

        return response('', 500);
    }

    public function delete_fqdn(Request $request, $value)
    {
        ActionLogController::log(Auth::user()->id, Auth::user()->name, "trying to delete $value from manual FQDN list");
        if (self::validateFQDN($value)) {
            if (FQDNs::find($value)) {
                if (FQDNs::find($value)->delete()) {
                    ActionLogController::log(Auth::user()->id, Auth::user()->name, "succeded to delete $value from manual FQDN list");

                    return response('', 200);
                }
                ActionLogController::log(Auth::user()->id, Auth::user()->name, "failed to delete $value from manual FQDN list (delete failed)");

                return response('delete failed', 500);
            }
            ActionLogController::log(Auth::user()->id, Auth::user()->name, "failed to delete $value from manual FQDN list (FQDN not exists)");

            return response('FQDN not exists', 500);
        }
        ActionLogController::log(Auth::user()->id, Auth::user()->name, "failed to delete $value from manual FQDN list (invalid FQDN)");

        return response('Invalid FQDN', 500);
    }

    public function add_ipv4(Request $request)
    {
        if ($request->has(['value', 'comment'])) {
            if ($request->filled(['value'])) {
                ActionLogController::log(Auth::user()->id, Auth::user()->name, 'trying to add '.$request->input('value').' to manual IPv4 list');
                if (filter_var($request->input('value'), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                    if (! IPv4s::find($request->input('value'))) {
                        $new = new IPv4s;
                        $new->ipv4 = $request->input('value');
                        $new->comment = $request->input('comment');
                        if ($new->save()) {
                            ActionLogController::log(Auth::user()->id, Auth::user()->name, 'succeded to add '.$request->input('value').' to manual IPv4 list');

                            return response('', 200);
                        }
                        ActionLogController::log(Auth::user()->id, Auth::user()->name, 'failed to add '.$request->input('value').' to manual IPv4 list (failed insert)');

                        return response('insert failed', 500);
                    }
                    ActionLogController::log(Auth::user()->id, Auth::user()->name, 'failed to add '.$request->input('value').' to manual IPv4 list (IPv4 already exists)');

                    return response('IPv4 already exists', 500);
                }
                ActionLogController::log(Auth::user()->id, Auth::user()->name, 'failed to add '.$request->input('value').' to manual IPv4 list (invalid IPv4)');

                return response('Invalid IPv4', 500);
            }

            return response('Insert a value', 500);
        }

        return response('', 500);
    }

    public function delete_ipv4(Request $request, $value)
    {
        ActionLogController::log(Auth::user()->id, Auth::user()->name, "trying to delete $value from manual IPv4 list");
        if (filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            if (IPv4s::find($value)) {
                if (IPv4s::find($value)->delete()) {
                    ActionLogController::log(Auth::user()->id, Auth::user()->name, "succeded to delete $value from manual IPv4 list");

                    return response('', 200);
                }
                ActionLogController::log(Auth::user()->id, Auth::user()->name, "failed to delete $value from manual IPv4 list (delete failed)");

                return response('delete failed', 500);
            }
            ActionLogController::log(Auth::user()->id, Auth::user()->name, "failed to delete $value from manual IPv4 list (IPv4 not exists)");

            return response('IPv4 not exists', 500);
        }
        ActionLogController::log(Auth::user()->id, Auth::user()->name, "failed to delete $value from manual IPv4 list (invalid IPv4)");

        return response('Invalid IPv4', 500);
    }

    public function add_ipv6(Request $request)
    {
        if ($request->has(['value', 'comment'])) {
            if ($request->filled(['value'])) {
                ActionLogController::log(Auth::user()->id, Auth::user()->name, 'trying to add '.$request->input('value').' to manual IPv6 list');
                if (filter_var($request->input('value'), FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                    if (! IPv6s::find($request->input('value'))) {
                        $new = new IPv6s;
                        $new->ipv6 = $request->input('value');
                        $new->comment = $request->input('comment');
                        if ($new->save()) {
                            ActionLogController::log(Auth::user()->id, Auth::user()->name, 'succeded to add '.$request->input('value').' to manual IPv6 list');

                            return response('', 200);
                        }
                        ActionLogController::log(Auth::user()->id, Auth::user()->name, 'failed to add '.$request->input('value').' to manual IPv6 list (failed insert)');

                        return response('insert failed', 500);
                    }
                    ActionLogController::log(Auth::user()->id, Auth::user()->name, 'failed to add '.$request->input('value').' to manual IPv6 list (IPv6 already exists)');

                    return response('IPv6 already exists', 500);
                }
                ActionLogController::log(Auth::user()->id, Auth::user()->name, 'failed to add '.$request->input('value').' to manual IPv6 list (invalid IPv6)');

                return response('Invalid IPv6', 500);
            }

            return response('Insert a value', 500);
        }

        return response('', 500);
    }

    public function delete_ipv6(Request $request, $value)
    {
        ActionLogController::log(Auth::user()->id, Auth::user()->name, "trying to delete $value from manual IPv6 list");
        if (filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            if (IPv6s::find($value)) {
                if (IPv6s::find($value)->delete()) {
                    ActionLogController::log(Auth::user()->id, Auth::user()->name, "succeded to delete $value from manual IPv6 list");

                    return response('', 200);
                }
                ActionLogController::log(Auth::user()->id, Auth::user()->name, "failed to delete $value from manual IPv6 list (delete failed)");

                return response('delete failed', 500);
            }
            ActionLogController::log(Auth::user()->id, Auth::user()->name, "failed to delete $value from manual IPv6 list (IPv6 not exists)");

            return response('IPv6 not exists', 500);
        }
        ActionLogController::log(Auth::user()->id, Auth::user()->name, "failed to delete $value from manual IPv6 list (invalid IPv6)");

        return response('Invalid IPv6', 500);
    }

    private static function validateFQDN($domain)
    {
        $pattern = "/(?=^.{1,254}$)(^(?:(?!\d+\.|-)[a-zA-Z0-9_\-]{1,63}(?<!-)\.?)+(?:[a-zA-Z]{2,})$)/";
        if (preg_match($pattern, $domain)) {
            return true;
        } else {
            return false;
        }
    }

    public function import(Request $request, $type)
    {
        $file = $request->file('file');
        $filename = $file->getClientOriginalName();
        $mimeType = $file->getMimeType();
        $datafile = file_get_contents($file->getPathname());
        ActionLogController::log(Auth::user()->id, Auth::user()->name, "trying to import file $filename into manual $type list");
        switch ($type) {
            case 'fqdn':
                $rows = explode("\n", trim($datafile));
                $successes = $errors = [];
                $i = 0;
                foreach ($rows as $row) {
                    $i++;
                    if (self::validateFQDN(trim($row))) {
                        if (! FQDNs::find(trim($row))) {
                            $new = new FQDNs;
                            $new->fqdn = trim($row);
                            $new->comment = "Imported from $filename";
                            if ($new->save()) {
                                $successes[] = "Row $i: insert of $row succeded";
                            } else {
                                $errors[] = "Row $i: insert of $row failed";
                            }
                        } else {
                            $errors[] = "Row $i: $row is alredy present as FQDN";
                        }
                    } else {
                        $errors[] = "Row $i: $row is not a valid FQDN";
                    }
                }
                ActionLogController::log(Auth::user()->id, Auth::user()->name, "succeded to import file $filename into manual $type list (successes: ".count($successes).' - errors: '.count($errors).')');

                return response()->json(['success' => true, 'payload' => 'File has been imported', 'successes' => $successes, 'errors' => $errors]);
                break;
            case 'ipv4':
                $rows = explode("\n", trim($datafile));
                $successes = $errors = [];
                $i = 0;
                foreach ($rows as $row) {
                    $i++;
                    if (filter_var(trim($row), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                        if (! IPv4s::find(trim($row))) {
                            $new = new IPv4s;
                            $new->ipv4 = trim($row);
                            $new->comment = "Imported from $filename";
                            if ($new->save()) {
                                $successes[] = "Row $i: insert of $row succeded";
                            } else {
                                $errors[] = "Row $i: insert of $row failed";
                            }
                        } else {
                            $errors[] = "Row $i: $row is alredy present as IPv4";
                        }
                    } else {
                        $errors[] = "Row $i: $row is not a valid IPv4";
                    }
                }
                ActionLogController::log(Auth::user()->id, Auth::user()->name, "succeded to import file $filename into manual $type list (successes: ".count($successes).' - errors: '.count($errors).')');

                return response()->json(['success' => true, 'payload' => 'File has been imported', 'successes' => $successes, 'errors' => $errors]);
            case 'ipv6':
                $rows = explode("\n", trim($datafile));
                $successes = $errors = [];
                $i = 0;
                foreach ($rows as $row) {
                    $i++;
                    if (filter_var(trim($row), FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                        if (! IPv6s::find(trim($row))) {
                            $new = new IPv6s;
                            $new->ipv6 = trim($row);
                            $new->comment = "Imported from $filename";
                            if ($new->save()) {
                                $successes[] = "Row $i: insert of $row succeded";
                            } else {
                                $errors[] = "Row $i: insert of $row failed";
                            }
                        } else {
                            $errors[] = "Row $i: $row is alredy present as IPv6";
                        }
                    } else {
                        $errors[] = "Row $i: $row is not a valid IPv6";
                    }
                }
                ActionLogController::log(Auth::user()->id, Auth::user()->name, "succeded to import file $filename into manual $type list (successes: ".count($successes).' - errors: '.count($errors).')');

                return response()->json(['success' => true, 'payload' => 'File has been imported', 'successes' => $successes, 'errors' => $errors]);
            default:
                ActionLogController::log(Auth::user()->id, Auth::user()->name, "failed to import file $filename into manual $type list (invalid type)");

                return response()->json(['success' => false, 'payload' => 'Invalid type']);
        }

    }
}
