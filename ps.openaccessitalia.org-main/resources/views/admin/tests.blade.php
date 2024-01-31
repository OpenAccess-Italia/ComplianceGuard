@extends('layouts.home')

@section('page')

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-6 py-lg-8 subheader-transparent" id="kt_subheader">
        <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-center flex-wrap mr-1">
                <!--begin::Page Heading-->
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <!--begin::Page Title-->
                    <h5 class="text-dark font-weight-bold my-1 mr-5">Tests</h5>
                    <!--end::Page Title-->
                </div>
                <!--end::Page Heading-->
            </div>
        </div>
    </div>
    <!--end::Subheader-->
    <!--begin::Entry-->
    <div class="d-flex flex-column-fluid">
        <!--begin::Container-->
        <div class="container">
            <div class="card card-custom">
                <div class="card-body">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="piracy-tab" data-toggle="tab" href="#piracy" role="tab" aria-controls="piracy" aria-selected="true">
                                <span class="nav-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </span>
                                <span class="nav-text">Piracy Shield</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="cncpo-tab" data-toggle="tab" href="#cncpo" role="tab" aria-controls="cncpo" aria-selected="true">
                                <span class="nav-icon">
                                    <i class="fas fa-child"></i>
                                </span>
                                <span class="nav-text">CNCPO</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="adm-tab" data-toggle="tab" href="#adm" role="tab" aria-controls="adm" aria-selected="false">
                                <span class="nav-icon">
                                    <i class="fas fa-user-secret"></i>
                                </span>
                                <span class="nav-text">ADM</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="dns-tab" data-toggle="tab" href="#dns" role="tab" aria-controls="dns" aria-selected="false">
                                <span class="nav-icon">
                                    <i class="fas fa-server"></i>
                                </span>
                                <span class="nav-text">DNS servers</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="bgp-tab" data-toggle="tab" href="#bgp" role="tab" aria-controls="bgp" aria-selected="false">
                                <span class="nav-icon">
                                    <i class="fas fa-bezier-curve"></i>
                                </span>
                                <span class="nav-text">BGP</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="smtp-tab" data-toggle="tab" href="#smtp" role="tab" aria-controls="smtp" aria-selected="false">
                                <span class="nav-icon">
                                    <i class="fas fa-paper-plane"></i>
                                </span>
                                <span class="nav-text">SMTP</span>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="piracy" role="tabpanel" aria-labelledby="piracy-tab">
                            <div class="card card-custom">
                                <div class="card-body">
                                    <div class="text-center">
                                        <button class="btn btn-primary" id="run_piracy"><i class="fas fa-tasks mr-2"></i>Run test</button>
                                    </div>
                                    <h5 class="text-center mt-2">Settings</h5>
                                    <div class="input-group mt-1">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text" id="piracy_settings_icon">
                                                <i class="fas fa-question text-dark"></i>
                                            </div>
                                        </div>
                                        <input class="form-control" id="piracy_settings_passed" value="Unknown" readonly>
                                    </div>
                                    <textarea id="piracy_settings_messages" class="form-control mt-1" readonly rows="3"></textarea>
                                    <h5 class="text-center mt-2">Hosts file</h5>
                                    <div class="input-group mt-1">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text" id="piracy_hosts_file_icon">
                                                <i class="fas fa-question text-dark"></i>
                                            </div>
                                        </div>
                                        <input class="form-control" id="piracy_hosts_file_passed" value="Unknown" readonly>
                                    </div>
                                    <textarea id="piracy_hosts_file_messages" class="form-control mt-1" readonly rows="3"></textarea>
                                    <h5 class="text-center mt-2">API status</h5>
                                    <div class="input-group mt-1">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text" id="piracy_api_status_icon">
                                                <i class="fas fa-question text-dark"></i>
                                            </div>
                                        </div>
                                        <input class="form-control" id="piracy_api_status_passed" value="Unknown" readonly>
                                    </div>
                                    <textarea id="piracy_api_status_messages" class="form-control mt-1" readonly rows="3"></textarea>
                                    <h5 class="text-center mt-2">Credentials</h5>
                                    <div class="input-group mt-1">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text" id="piracy_credentials_icon">
                                                <i class="fas fa-question text-dark"></i>
                                            </div>
                                        </div>
                                        <input class="form-control" id="piracy_credentials_passed" value="Unknown" readonly>
                                    </div>
                                    <textarea id="piracy_credentials_messages" class="form-control mt-1" readonly rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="cncpo" role="tabpanel" aria-labelledby="cncpo-tab">
                            <div class="card card-custom">
                                <div class="card-body">
                                    <div class="text-center">
                                        <button class="btn btn-primary" id="run_cncpo"><i class="fas fa-tasks mr-2"></i>Run test</button>
                                    </div>
                                    <h5 class="text-center mt-2">Settings</h5>
                                    <div class="input-group mt-1">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text" id="cncpo_settings_icon">
                                                <i class="fas fa-question text-dark"></i>
                                            </div>
                                        </div>
                                        <input class="form-control" id="cncpo_settings_passed" value="Unknown" readonly>
                                    </div>
                                    <textarea id="cncpo_settings_messages" class="form-control mt-1" readonly rows="3"></textarea>
                                    <h5 class="text-center mt-2">Download</h5>
                                    <div class="input-group mt-1">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text" id="cncpo_download_icon">
                                                <i class="fas fa-question text-dark"></i>
                                            </div>
                                        </div>
                                        <input class="form-control" id="cncpo_download_passed" value="Unknown" readonly>
                                    </div>
                                    <textarea id="cncpo_download_messages" class="form-control mt-1" readonly rows="3"></textarea>
                                    <h5 class="text-center mt-2">Validation</h5>
                                    <div class="input-group mt-1">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text" id="cncpo_validation_icon">
                                                <i class="fas fa-question text-dark"></i>
                                            </div>
                                        </div>
                                        <input class="form-control" id="cncpo_validation_passed" value="Unknown" readonly>
                                    </div>
                                    <textarea id="cncpo_validation_messages" class="form-control mt-1" readonly rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="adm" role="tabpanel" aria-labelledby="adm-tab">
                            <div class="card card-custom">
                                <div class="card-body">
                                    <div class="text-center">
                                        <button class="btn btn-primary" id="run_adm"><i class="fas fa-tasks mr-2"></i>Run test</button>
                                    </div>
                                    <h5 class="text-center mt-2">Settings</h5>
                                    <div class="input-group mt-1">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text" id="adm_settings_icon">
                                                <i class="fas fa-question text-dark"></i>
                                            </div>
                                        </div>
                                        <input class="form-control" id="adm_settings_passed" value="Unknown" readonly>
                                    </div>
                                    <textarea id="adm_settings_messages" class="form-control mt-1" readonly rows="3"></textarea>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h4 class="text-center mt-2">Betting</h4>
                                            <hr>
                                            <h5 class="text-center mt-2">Links</h5>
                                            <div class="input-group mt-1">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text" id="adm_betting_links_icon">
                                                        <i class="fas fa-question text-dark"></i>
                                                    </div>
                                                </div>
                                                <input class="form-control" id="adm_betting_links_passed" value="Unknown" readonly>
                                            </div>
                                            <textarea id="adm_betting_links_messages" class="form-control mt-1" readonly rows="3"></textarea>
                                            <h5 class="text-center mt-2">Download</h5>
                                            <div class="input-group mt-1">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text" id="adm_betting_download_icon">
                                                        <i class="fas fa-question text-dark"></i>
                                                    </div>
                                                </div>
                                                <input class="form-control" id="adm_betting_download_passed" value="Unknown" readonly>
                                            </div>
                                            <textarea id="adm_betting_download_messages" class="form-control mt-1" readonly rows="3"></textarea>
                                            <h5 class="text-center mt-2">Validation</h5>
                                            <div class="input-group mt-1">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text" id="adm_betting_validation_icon">
                                                        <i class="fas fa-question text-dark"></i>
                                                    </div>
                                                </div>
                                                <input class="form-control" id="adm_betting_validation_passed" value="Unknown" readonly>
                                            </div>
                                            <textarea id="adm_betting_validation_messages" class="form-control mt-1" readonly rows="3"></textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <h4 class="text-center mt-2">Smoking</h4>
                                            <hr>
                                            <h5 class="text-center mt-2">Links</h5>
                                            <div class="input-group mt-1">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text" id="adm_smoking_links_icon">
                                                        <i class="fas fa-question text-dark"></i>
                                                    </div>
                                                </div>
                                                <input class="form-control" id="adm_smoking_links_passed" value="Unknown" readonly>
                                            </div>
                                            <textarea id="adm_smoking_links_messages" class="form-control mt-1" readonly rows="3"></textarea>
                                            <h5 class="text-center mt-2">Download</h5>
                                            <div class="input-group mt-1">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text" id="adm_smoking_download_icon">
                                                        <i class="fas fa-question text-dark"></i>
                                                    </div>
                                                </div>
                                                <input class="form-control" id="adm_smoking_download_passed" value="Unknown" readonly>
                                            </div>
                                            <textarea id="adm_smoking_download_messages" class="form-control mt-1" readonly rows="3"></textarea>
                                            <h5 class="text-center mt-2">Validation</h5>
                                            <div class="input-group mt-1">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text" id="adm_smoking_validation_icon">
                                                        <i class="fas fa-question text-dark"></i>
                                                    </div>
                                                </div>
                                                <input class="form-control" id="adm_smoking_validation_passed" value="Unknown" readonly>
                                            </div>
                                            <textarea id="adm_smoking_validation_messages" class="form-control mt-1" readonly rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="dns" role="tabpanel" aria-labelledby="dns-tab">
                            <div class="card card-custom">
                                <div class="card-body">
                                    <div class="text-center">
                                        <button class="btn btn-primary" id="run_dns"><i class="fas fa-tasks mr-2"></i>Run test</button>
                                    </div>
                                    <h5 class="text-center mt-2">Settings</h5>
                                    <div class="input-group mt-1">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text" id="dns_settings_icon">
                                                <i class="fas fa-question text-dark"></i>
                                            </div>
                                        </div>
                                        <input class="form-control" id="dns_settings_passed" value="Unknown" readonly>
                                    </div>
                                    <textarea id="dns_settings_messages" class="form-control mt-1" readonly rows="3"></textarea>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h4 class="text-center mt-2">Primary server</h4>
                                            <hr>
                                            <h5 class="text-center mt-2">Write privileges</h5>
                                            <div class="input-group mt-1">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text" id="dns_primary_write_icon">
                                                        <i class="fas fa-question text-dark"></i>
                                                    </div>
                                                </div>
                                                <input class="form-control" id="dns_primary_write_passed" value="Unknown" readonly>
                                            </div>
                                            <textarea id="dns_primary_write_messages" class="form-control mt-1" readonly rows="3"></textarea>
                                            <h5 class="text-center mt-2">Read privileges</h5>
                                            <div class="input-group mt-1">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text" id="dns_primary_read_icon">
                                                        <i class="fas fa-question text-dark"></i>
                                                    </div>
                                                </div>
                                                <input class="form-control" id="dns_primary_read_passed" value="Unknown" readonly>
                                            </div>
                                            <textarea id="dns_primary_read_messages" class="form-control mt-1" readonly rows="3"></textarea>
                                            <h5 class="text-center mt-2">Service reload</h5>
                                            <div class="input-group mt-1">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text" id="dns_primary_reload_icon">
                                                        <i class="fas fa-question text-dark"></i>
                                                    </div>
                                                </div>
                                                <input class="form-control" id="dns_primary_reload_passed" value="Unknown" readonly>
                                            </div>
                                            <textarea id="dns_primary_reload_messages" class="form-control mt-1" readonly rows="3"></textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <h4 class="text-center mt-2">Secondary server</h4>
                                            <hr>
                                            <h5 class="text-center mt-2">Write privileges</h5>
                                            <div class="input-group mt-1">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text" id="dns_secondary_write_icon">
                                                        <i class="fas fa-question text-dark"></i>
                                                    </div>
                                                </div>
                                                <input class="form-control" id="dns_secondary_write_passed" value="Unknown" readonly>
                                            </div>
                                            <textarea id="dns_secondary_write_messages" class="form-control mt-1" readonly rows="3"></textarea>
                                            <h5 class="text-center mt-2">Read privileges</h5>
                                            <div class="input-group mt-1">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text" id="dns_secondary_read_icon">
                                                        <i class="fas fa-question text-dark"></i>
                                                    </div>
                                                </div>
                                                <input class="form-control" id="dns_secondary_read_passed" value="Unknown" readonly>
                                            </div>
                                            <textarea id="dns_secondary_read_messages" class="form-control mt-1" readonly rows="3"></textarea>
                                            <h5 class="text-center mt-2">Service reload</h5>
                                            <div class="input-group mt-1">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text" id="dns_secondary_reload_icon">
                                                        <i class="fas fa-question text-dark"></i>
                                                    </div>
                                                </div>
                                                <input class="form-control" id="dns_secondary_reload_passed" value="Unknown" readonly>
                                            </div>
                                            <textarea id="dns_secondary_reload_messages" class="form-control mt-1" readonly rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="bgp" role="tabpanel" aria-labelledby="bgp-tab">
                            <div class="card card-custom">
                                <div class="card-body">
                                    <div class="text-center">
                                        <button class="btn btn-primary" id="run_bgp"><i class="fas fa-tasks mr-2"></i>Run test</button>
                                    </div>
                                    <h5 class="text-center mt-2">Settings</h5>
                                    <div class="input-group mt-1">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text" id="bgp_settings_icon">
                                                <i class="fas fa-question text-dark"></i>
                                            </div>
                                        </div>
                                        <input class="form-control" id="bgp_settings_passed" value="Unknown" readonly>
                                    </div>
                                    <textarea id="bgp_settings_messages" class="form-control mt-1" readonly rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="smtp" role="tabpanel" aria-labelledby="smtp-tab">
                            <div class="card card-custom">
                                <div class="card-body">
                                    <div class="text-center">
                                        <button class="btn btn-primary" id="run_smtp"><i class="fas fa-tasks mr-2"></i>Run test</button>
                                    </div>
                                    <h5 class="text-center mt-2">Settings</h5>
                                    <div class="input-group mt-1">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text" id="smtp_settings_icon">
                                                <i class="fas fa-question text-dark"></i>
                                            </div>
                                        </div>
                                        <input class="form-control" id="smtp_settings_passed" value="Unknown" readonly>
                                    </div>
                                    <textarea id="smtp_settings_messages" class="form-control mt-1" readonly rows="3"></textarea>
                                    <h5 class="text-center mt-2">Test mail</h5>
                                    <div class="input-group mt-1">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text" id="smtp_testmail_icon">
                                                <i class="fas fa-question text-dark"></i>
                                            </div>
                                        </div>
                                        <input class="form-control" id="smtp_testmail_passed" value="Unknown" readonly>
                                    </div>
                                    <textarea id="smtp_testmail_messages" class="form-control mt-1" readonly rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->

<div class="modal" id="modal_alert" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content bg-light">
            <div class="modal-body" id="modal_alert_content">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>

    $(document).ready(function(){
        $('#run_piracy').on('click',function(){
            reset('piracy_settings');
            reset('piracy_hosts_file');
            reset('piracy_api_status');
            reset('piracy_credentials');
            $(this).prop("disabled",true);
            $(this).html('<i class="fas fa-spinner fa-spin"></i>');
            $.ajax({
                type: "GET",
                url: '/admin/tests/piracy',
                statusCode: {
                    200: function(data) {
                        var result = JSON.parse(data);
                        if(result.hasOwnProperty('settings')){
                            parse('piracy_settings',result.settings);
                        }
                        if(result.hasOwnProperty('hosts_file')){
                            parse('piracy_hosts_file',result.hosts_file);
                        }
                        if(result.hasOwnProperty('api_status')){
                            parse('piracy_api_status',result.api_status);
                        }
                        if(result.hasOwnProperty('credentials')){
                            parse('piracy_credentials',result.credentials);
                        }
                        $('#run_piracy').prop("disabled",false);
                        $('#run_piracy').html('<i class="fas fa-tasks mr-2"></i>Run test');
                    },
                    500: function(data) {
                        $('#run_adm').prop("disabled",false);
                        $('#run_adm').html('<i class="fas fa-tasks mr-2"></i>Run test');
                        $('#modal_alert_content').html('<div class="alert alert-custom alert-danger" role="alert"><div class="alert-icon"><i class="flaticon-cancel"></i></div><div class="alert-text">Error</div></div>')
                        $('#modal_alert').modal('show');
                    }
                }
            });
        });
        $('#run_cncpo').on('click',function(){
            reset('cncpo_settings');
            reset('cncpo_download');
            reset('cncpo_validation');
            $(this).prop("disabled",true);
            $(this).html('<i class="fas fa-spinner fa-spin"></i>');
            $.ajax({
                type: "GET",
                url: '/admin/tests/cncpo',
                statusCode: {
                    200: function(data) {
                        var result = JSON.parse(data);
                        if(result.hasOwnProperty('settings')){
                            parse('cncpo_settings',result.settings);
                        }
                        if(result.hasOwnProperty('download')){
                            parse('cncpo_download',result.download);
                        }
                        if(result.hasOwnProperty('validation')){
                            parse('cncpo_validation',result.validation);
                        }
                        $('#run_cncpo').prop("disabled",false);
                        $('#run_cncpo').html('<i class="fas fa-tasks mr-2"></i>Run test');
                    },
                    500: function(data) {
                        $('#run_adm').prop("disabled",false);
                        $('#run_adm').html('<i class="fas fa-tasks mr-2"></i>Run test');
                        $('#modal_alert_content').html('<div class="alert alert-custom alert-danger" role="alert"><div class="alert-icon"><i class="flaticon-cancel"></i></div><div class="alert-text">Error</div></div>')
                        $('#modal_alert').modal('show');
                    }
                }
            });
        });
        $('#run_adm').on('click',function(){
            reset('adm_settings');
            reset('adm_betting_links');
            reset('adm_betting_download');
            reset('adm_betting_validation');
            reset('adm_smoking_links');
            reset('adm_smoking_download');
            reset('adm_smoking_validation');
            $(this).prop("disabled",true);
            $(this).html('<i class="fas fa-spinner fa-spin"></i>');
            $.ajax({
                type: "GET",
                url: '/admin/tests/adm',
                statusCode: {
                    200: function(data) {
                        var result = JSON.parse(data);
                        if(result.hasOwnProperty('settings')){
                            parse('adm_settings',result.settings);
                        }
                        if(result.hasOwnProperty('betting_links')){
                            parse('adm_betting_links',result.betting_links);
                        }
                        if(result.hasOwnProperty('betting_download')){
                            parse('adm_betting_download',result.betting_download);
                        }
                        if(result.hasOwnProperty('betting_validation')){
                            parse('adm_betting_validation',result.betting_validation);
                        }
                        if(result.hasOwnProperty('smoking_links')){
                            parse('adm_smoking_links',result.smoking_links);
                        }
                        if(result.hasOwnProperty('smoking_download')){
                            parse('adm_smoking_download',result.smoking_download);
                        }
                        if(result.hasOwnProperty('smoking_validation')){
                            parse('adm_smoking_validation',result.smoking_validation);
                        }
                        $('#run_adm').prop("disabled",false);
                        $('#run_adm').html('<i class="fas fa-tasks mr-2"></i>Run test');
                    },
                    500: function(data) {
                        $('#run_adm').prop("disabled",false);
                        $('#run_adm').html('<i class="fas fa-tasks mr-2"></i>Run test');
                        $('#modal_alert_content').html('<div class="alert alert-custom alert-danger" role="alert"><div class="alert-icon"><i class="flaticon-cancel"></i></div><div class="alert-text">Error</div></div>')
                        $('#modal_alert').modal('show');
                    }
                }
            });
        });
        $('#run_dns').on('click',function(){
            reset('dns_settings');
            reset('dns_primary_write');
            reset('dns_primary_read');
            reset('dns_primary_reload');
            reset('dns_secondary_write');
            reset('dns_secondary_read');
            reset('dns_secondary_reload');
            $(this).prop("disabled",true);
            $(this).html('<i class="fas fa-spinner fa-spin"></i>');
            $.ajax({
                type: "GET",
                url: '/admin/tests/dns',
                statusCode: {
                    200: function(data) {
                        var result = JSON.parse(data);
                        if(result.hasOwnProperty('settings')){
                            parse('dns_settings',result.settings);
                        }
                        if(result.hasOwnProperty('primary')){
                            if(result.primary.hasOwnProperty('write')){
                                parse('dns_primary_write',result.primary.write);
                            }
                            if(result.primary.hasOwnProperty('read')){
                                parse('dns_primary_read',result.primary.read);
                            }
                            if(result.primary.hasOwnProperty('reload')){
                                parse('dns_primary_reload',result.primary.reload);
                            }
                        }
                        if(result.hasOwnProperty('secondary')){
                            if(result.secondary.hasOwnProperty('write')){
                                parse('dns_secondary_write',result.primary.write);
                            }
                            if(result.secondary.hasOwnProperty('read')){
                                parse('dns_secondary_read',result.primary.read);
                            }
                            if(result.secondary.hasOwnProperty('reload')){
                                parse('dns_secondary_reload',result.primary.reload);
                            }
                        }
                        $('#run_dns').prop("disabled",false);
                        $('#run_dns').html('<i class="fas fa-tasks mr-2"></i>Run test');
                    },
                    500: function(data) {
                        $('#run_dns').prop("disabled",false);
                        $('#run_dns').html('<i class="fas fa-tasks mr-2"></i>Run test');
                        $('#modal_alert_content').html('<div class="alert alert-custom alert-danger" role="alert"><div class="alert-icon"><i class="flaticon-cancel"></i></div><div class="alert-text">Error</div></div>')
                        $('#modal_alert').modal('show');
                    }
                }
            });
        });
        $('#run_bgp').on('click',function(){
            reset('bgp_settings');
            $(this).prop("disabled",true);
            $(this).html('<i class="fas fa-spinner fa-spin"></i>');
            $.ajax({
                type: "GET",
                url: '/admin/tests/bgp',
                statusCode: {
                    200: function(data) {
                        var result = JSON.parse(data);
                        if(result.hasOwnProperty('settings')){
                            parse('bgp_settings',result.settings);
                        }
                        $('#run_bgp').prop("disabled",false);
                        $('#run_bgp').html('<i class="fas fa-tasks mr-2"></i>Run test');
                    },
                    500: function(data) {
                        $('#run_bgp').prop("disabled",false);
                        $('#run_bgp').html('<i class="fas fa-tasks mr-2"></i>Run test');
                        $('#modal_alert_content').html('<div class="alert alert-custom alert-danger" role="alert"><div class="alert-icon"><i class="flaticon-cancel"></i></div><div class="alert-text">Error</div></div>')
                        $('#modal_alert').modal('show');
                    }
                }
            });
        });
        $('#run_smtp').on('click',function(){
            reset('smtp_settings');
            $(this).prop("disabled",true);
            $(this).html('<i class="fas fa-spinner fa-spin"></i>');
            $.ajax({
                type: "GET",
                url: '/admin/tests/smtp',
                statusCode: {
                    200: function(data) {
                        var result = JSON.parse(data);
                        if(result.hasOwnProperty('settings')){
                            parse('smtp_settings',result.settings);
                        }
                        if(result.hasOwnProperty('testmail')){
                            parse('smtp_testmail',result.testmail);
                        }
                        $('#run_smtp').prop("disabled",false);
                        $('#run_smtp').html('<i class="fas fa-tasks mr-2"></i>Run test');
                    },
                    500: function(data) {
                        $('#run_smtp').prop("disabled",false);
                        $('#run_smtp').html('<i class="fas fa-tasks mr-2"></i>Run test');
                        $('#modal_alert_content').html('<div class="alert alert-custom alert-danger" role="alert"><div class="alert-icon"><i class="flaticon-cancel"></i></div><div class="alert-text">Error</div></div>')
                        $('#modal_alert').modal('show');
                    }
                }
            });
        });
    });

    function reset(test){
        $('#'+test+'_icon').html('<i class="fas fa-question text-dark"></i>');
        $('#'+test+'_passed').val('Unknown');
        $('#'+test+'_messages').val('');
    }

    function parse(test,data){
        if(data.passed){
            $('#'+test+'_icon').html('<i class="fas fa-check text-success"></i>');
            $('#'+test+'_passed').val('Passed');
        }else{
            $('#'+test+'_icon').html('<i class="fas fa-times text-danger"></i>');
            $('#'+test+'_passed').val('Not passed');
        }
        var content = '';
        $.each(data.messages, function (indexInArray, valueOfElement) { 
            content += valueOfElement+'\n';
        });
        $('#'+test+'_messages').val(content);
    }

</script>

@endsection