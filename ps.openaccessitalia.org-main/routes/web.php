<?php

use App\Http\Controllers\ADMController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CNCPOController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ManualController;
use App\Http\Controllers\PiracyController;
use App\Http\Controllers\ProfileController;


Route::group(['prefix' => 'piracy'], function () {
    Route::view('/whitelist', 'piracy.whitelist');
    Route::view('/lists/tickets', 'piracy.lists.tickets');
    Route::view('/lists/fqdn', 'piracy.lists.fqdn');
    Route::view('/lists/ipv4', 'piracy.lists.ipv4');
    Route::view('/lists/ipv6', 'piracy.lists.ipv6');

    Route::get('/ticket/{ticket_id}', [PiracyController::class,'view_ticket']);
    Route::get('/whitelist/get', [PiracyController::class,'datatable_whitelist']);
    Route::post('/whitelist/delete', [PiracyController::class,'delete_from_whitelist']);
    Route::post('/whitelist/add', [PiracyController::class,'add_to_whitelist']);
    Route::get('/lists/tickets/get', [PiracyController::class,'datatable_tickets']);
    Route::get('/lists/fqdn/get', [PiracyController::class,'datatable_fqdn']);
    Route::get('/lists/fqdn/download/{line}', [PiracyController::class,'download_fqdn']);
    Route::get('/lists/ipv4/get', [PiracyController::class,'datatable_ipv4']);
    Route::get('/lists/ipv4/download/{line}', [PiracyController::class,'download_ipv4']);
    Route::get('/lists/ipv6/get', [PiracyController::class,'datatable_ipv6']);
    Route::get('/lists/ipv6/download/{line}', [PiracyController::class,'download_ipv6']);
    Route::post('/lists/{type}/crud/{action}', [PiracyController::class,'crud']);
})->middleware('auth.piracy');

// CNCPO
Route::group(['prefix' => 'cncpo'], function () {
    Route::view('/files', 'cncpo.files');
    Route::view('/blacklist', 'cncpo.blacklist');

    Route::get('/files/get', [CNCPOController::class,'datatable_files']);
    Route::get('/blacklist/get', [CNCPOController::class,'datatable_blacklist']);
    Route::get('/blacklist/download/{type}', [CNCPOController::class,'download_blacklist']);
})->middleware('auth.cncpo');

// ADM
Route::group(['prefix' => 'adm'], function () {
    Route::view('/betting/files', 'adm.betting.files');
    Route::view('/betting/blacklist', 'adm.betting.blacklist');
    Route::view('/smoking/files', 'adm.smoking.files');
    Route::view('/smoking/blacklist', 'adm.smoking.blacklist');

    Route::get('/betting/files/get', [ADMController::class,'datatable_betting_files']);
    Route::get('/betting/blacklist/get', [ADMController::class,'datatable_betting_blacklist']);
    Route::get('/betting/blacklist/download', [ADMController::class, 'download_betting_blacklist']);
    Route::get('/smoking/files/get', [ADMController::class, 'datatable_smoking_files']);
    Route::get('/smoking/blacklist/get', [ADMController::class, 'datatable_smoking_blacklist']);
    Route::get('/smoking/blacklist/download', [ADMController::class, 'download_smoking_blacklist']);
})->middleware('auth.adm');

// MANUAL
Route::group(['prefix' => 'manual'], function () {
    Route::view('/lists/fqdn', 'manual.lists.fqdn');
    Route::view('/lists/ipv4', 'manual.lists.ipv4');
    Route::view('/lists/ipv6', 'manual.lists.ipv6');

    Route::get('/lists/fqdn/get', [ManualController::class,'datatable_fqdn']);
    Route::get('/lists/fqdn/delete/{value}', [ManualController::class,'delete_fqdn']);
    Route::post('/lists/fqdn/add', [ManualController::class,'add_fqdn']);
    Route::get('/lists/ipv4/get', [ManualController::class,'datatable_ipv4']);
    Route::get('/lists/ipv4/delete/{value}', [ManualController::class,'delete_ipv4']);
    Route::post('/lists/ipv4/add', [ManualController::class,'add_ipv4']);
    Route::get('/lists/ipv6/get', [ManualController::class,'datatable_ipv6']);
    Route::get('/lists/ipv6/delete/{value}', [ManualController::class,'delete_ipv6']);
    Route::post('/lists/ipv6/add', [ManualController::class,'add_ipv6']);
    Route::post('/lists/{type}/import', [ManualController::class,'import']);
})->middleware('auth.manual');

// ADMIN
Route::prefix('admin')->group(function () {
    Route::view('/users/list', 'admin.users.list');
    Route::view('/users/new', 'admin.users.new');
    Route::view('/settings/edit', 'admin.settings.edit');
    Route::view('/logs/actions', 'admin.logs.actions');
    Route::view('/logs/ps_api', 'admin.logs.ps_api');
    Route::view('/tests', 'admin.tests');

    Route::get('/users/list/get', [UserController::class,'datatable_user']);
    Route::post('/users/add', [UserController::class,'add_user']);
    Route::get('/users/view/{id}', [UserController::class,'view_user']);
    Route::post('/users/save', [UserController::class,'save_user']);
    Route::post('/setting/edit/save', [AdminController::class,'save_settings']);
    Route::get('/logs/actions/get', [AdminController::class,'datatable_actions_log']);
    Route::get('/logs/ps_api/get', [AdminController::class,'datatable_ps_api_log']);
    Route::get('/logs/ps_access/get', [AdminController::class,'datatable_ps_access_tokens']);
    Route::get('/logs/ps_refresh/get', [AdminController::class,'datatable_ps_refresh_tokens']);
    Route::get('/tests/piracy', [PiracyController::class,'test']);
    Route::get('/tests/cncpo', [CNCPOController::class,'test']);
    Route::get('/tests/adm', [ADMController::class,'test']);
    Route::get('/tests/dns', [AdminController::class,'test_dns']);
    Route::get('/tests/bgp', [AdminController::class,'test_bgp']);
    Route::get('/tests/smtp', [AdminController::class,'test_smtp']);
})->middleware('auth.admin');

Route::middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/home.php', [HomeController::class, 'index'])->name('home');
    Route::view('/profile', 'pages.profile');
    Route::post('/profile/save', [ProfileController::class, 'save']);
});

// AUTH
Auth::routes(['register' => false]);
