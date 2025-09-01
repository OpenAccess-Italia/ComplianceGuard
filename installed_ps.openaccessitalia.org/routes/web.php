<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//VIEWS
//PIRACY
Route::view('/piracy/whitelist','piracy.whitelist')->middleware('auth.piracy');
Route::view('/piracy/lists/tickets','piracy.lists.tickets')->middleware('auth.piracy');
Route::view('/piracy/lists/fqdn','piracy.lists.fqdn')->middleware('auth.piracy');
Route::view('/piracy/lists/ipv4','piracy.lists.ipv4')->middleware('auth.piracy');
Route::view('/piracy/lists/ipv6','piracy.lists.ipv6')->middleware('auth.piracy');
//CNCPO
Route::view('/cncpo/files','cncpo.files')->middleware('auth.cncpo');
Route::view('/cncpo/blacklist','cncpo.blacklist')->middleware('auth.cncpo');
//ADM
Route::view('/adm/betting/files','adm.betting.files')->middleware('auth.adm');
Route::view('/adm/betting/blacklist','adm.betting.blacklist')->middleware('auth.adm');
Route::view('/adm/smoking/files','adm.smoking.files')->middleware('auth.adm');
Route::view('/adm/smoking/blacklist','adm.smoking.blacklist')->middleware('auth.adm');
//MANUAL
Route::view('/manual/lists/fqdn','manual.lists.fqdn')->middleware('auth.manual');
Route::view('/manual/lists/ipv4','manual.lists.ipv4')->middleware('auth.manual');
Route::view('/manual/lists/ipv6','manual.lists.ipv6')->middleware('auth.manual');
//ADMIN
//--users--
Route::view('/admin/users/list', 'admin.users.list')->middleware('auth.admin');
Route::view('/admin/users/new', 'admin.users.new')->middleware('auth.admin');
//--settings--
Route::view('/admin/settings/edit','admin.settings.edit')->middleware('auth.admin');
//--logs--
Route::view('/admin/logs/actions','admin.logs.actions')->middleware('auth.admin');
Route::view('/admin/logs/ps_api','admin.logs.ps_api')->middleware('auth.admin');
//--tests--
Route::view('/admin/tests','admin.tests')->middleware('auth.admin');
//HOME
Route::get('/','HomeController@index')->name('home')->middleware('auth');
Route::get('/home.php','HomeController@index')->name('home')->middleware('auth');
//PROFILE
Route::view('/profile', 'pages.profile')->middleware('auth');

//ROUTES
//PIRACY
Route::get('/piracy/ticket/{ticket_id}','PiracyController@view_ticket')->middleware('auth.piracy');
Route::get('/piracy/whitelist/get','PiracyController@datatable_whitelist')->middleware('auth.piracy');
Route::post('/piracy/whitelist/delete','PiracyController@delete_from_whitelist')->middleware('auth.piracy');
Route::post('/piracy/whitelist/add','PiracyController@add_to_whitelist')->middleware('auth.piracy');
Route::get('/piracy/lists/tickets/get','PiracyController@datatable_tickets')->middleware('auth.piracy');
Route::get('/piracy/lists/fqdn/get','PiracyController@datatable_fqdn')->middleware('auth.piracy');
Route::get('/piracy/lists/fqdn/download/{line}','PiracyController@download_fqdn')->middleware('auth.piracy');
Route::get('/piracy/lists/ipv4/get','PiracyController@datatable_ipv4')->middleware('auth.piracy');
Route::get('/piracy/lists/ipv4/download/{line}','PiracyController@download_ipv4')->middleware('auth.piracy');
Route::get('/piracy/lists/ipv6/get','PiracyController@datatable_ipv6')->middleware('auth.piracy');
Route::get('/piracy/lists/ipv6/download/{line}','PiracyController@download_ipv6')->middleware('auth.piracy');
//CNCPO
Route::get('/cncpo/files/get', 'CNCPOController@datatable_files')->middleware('auth.cncpo');
Route::get('/cncpo/blacklist/get', 'CNCPOController@datatable_blacklist')->middleware('auth.cncpo');
Route::get('/cncpo/blacklist/download/{type}', 'CNCPOController@download_blacklist')->middleware('auth.cncpo');
//ADM
Route::get('/adm/betting/files/get', 'ADMController@datatable_betting_files')->middleware('auth.adm');
Route::get('/adm/betting/blacklist/get', 'ADMController@datatable_betting_blacklist')->middleware('auth.adm');
Route::get('/adm/betting/blacklist/download', 'ADMController@download_betting_blacklist');
Route::get('/adm/smoking/files/get', 'ADMController@datatable_smoking_files')->middleware('auth.adm');
Route::get('/adm/smoking/blacklist/get', 'ADMController@datatable_smoking_blacklist')->middleware('auth.adm');
Route::get('/adm/smoking/blacklist/download', 'ADMController@download_smoking_blacklist');
//MANUAL
Route::get('/manual/lists/fqdn/get','ManualController@datatable_fqdn')->middleware('auth.manual');
Route::get('/manual/lists/fqdn/delete/{value}','ManualController@delete_fqdn')->middleware('auth.manual');
Route::post('/manual/lists/fqdn/add','ManualController@add_fqdn')->middleware('auth.manual');
Route::get('/manual/lists/ipv4/get','ManualController@datatable_ipv4')->middleware('auth.manual');
Route::get('/manual/lists/ipv4/delete/{value}','ManualController@delete_ipv4')->middleware('auth.manual');
Route::post('/manual/lists/ipv4/add','ManualController@add_ipv4')->middleware('auth.manual');
Route::get('/manual/lists/ipv6/get','ManualController@datatable_ipv6')->middleware('auth.manual');
Route::get('/manual/lists/ipv6/delete/{value}','ManualController@delete_ipv6')->middleware('auth.manual');
Route::post('/manual/lists/ipv6/add','ManualController@add_ipv6')->middleware('auth.manual');
Route::post("/manual/lists/{type}/import",'ManualController@import')->middleware('auth.manual');
//ADMIN
//--users--
Route::get('/admin/users/list/get', 'Admin\UserController@datatable_user')->middleware('auth.admin');
Route::post('/admin/users/add','Admin\UserController@add_user')->middleware('auth.admin');
Route::get('/admin/users/view/{id}', 'Admin\UserController@view_user')->middleware('auth.admin');
Route::post('/admin/users/save', 'Admin\UserController@save_user')->middleware('auth.admin');
//--settings--
Route::post('/admin/setting/edit/save', 'Admin\AdminController@save_settings')->middleware('auth.admin');
//--logs--
Route::get('/admin/logs/actions/get', 'Admin\AdminController@datatable_actions_log')->middleware('auth.admin');
Route::get('/admin/logs/ps_api/get', 'Admin\AdminController@datatable_ps_api_log')->middleware('auth.admin');
Route::get('/admin/logs/ps_access/get', 'Admin\AdminController@datatable_ps_access_tokens')->middleware('auth.admin');
Route::get('/admin/logs/ps_refresh/get', 'Admin\AdminController@datatable_ps_refresh_tokens')->middleware('auth.admin');
//--tests--
Route::get('/admin/tests/piracy', 'PiracyController@test')->middleware('auth.admin');
Route::get('/admin/tests/cncpo', 'CNCPOController@test')->middleware('auth.admin');
Route::get('/admin/tests/adm', 'ADMController@test')->middleware('auth.admin');
Route::get('/admin/tests/dns', 'Admin\AdminController@test_dns')->middleware('auth.admin');
Route::get('/admin/tests/bgp', 'Admin\AdminController@test_bgp')->middleware('auth.admin');
Route::get('/admin/tests/smtp', 'Admin\AdminController@test_smtp')->middleware('auth.admin');
//PROFILE
Route::post('/profile/save', 'ProfileController@save')->middleware('auth');

//AUTH
Auth::routes(['register' => false]);
