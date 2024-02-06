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
                    <h5 class="text-dark font-weight-bold my-1 mr-5">Edit settings</h5>
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
                            <a class="nav-link" id="manual-tab" data-toggle="tab" href="#manual" role="tab" aria-controls="manual" aria-selected="false">
                                <span class="nav-icon">
                                    <i class="fas fa-hand-scissors"></i>
                                </span>
                                <span class="nav-text">Manual</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="false">
                                <span class="nav-icon">
                                    <i class="flaticon-doc"></i>
                                </span>
                                <span class="nav-text">General</span>
                            </a>
                        </li>
                    </ul>
                    <form action="/admin/setting/edit/save" method="POST">
                    @csrf
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="piracy" role="tabpanel" aria-labelledby="piracy-tab">
                            <div class="card card-custom">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="PIRACY_SHIELD_ENABLED">Module</label>
                                            <div class="input-group">
                                                <select class="form-control" id="PIRACY_SHIELD_ENABLED" name="PIRACY_SHIELD_ENABLED">
                                                    <option value="0" @if(env("PIRACY_SHIELD_ENABLED") == "0") selected @endif>Disabled</option>
                                                    <option value="1" @if(env("PIRACY_SHIELD_ENABLED") == "1") selected @endif>Enabled</option>
                                                </select>
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <i class="fas fa-at text-dark mr-1"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="PIRACY_SHIELD_MAIL">Mail address</label>
                                            <div class="input-group">
                                                <input class="form-control" id="PIRACY_SHIELD_MAIL" name="PIRACY_SHIELD_MAIL" value="{{env("PIRACY_SHIELD_MAIL")}}" placeholder="dummy@dummy.com">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <i class="fas fa-at text-dark mr-1"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="PIRACY_SHIELD_PSW">Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="PIRACY_SHIELD_PSW" name="PIRACY_SHIELD_PSW" value="{{env("PIRACY_SHIELD_PSW")}}">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <i class="fas fa-key text-dark mr-1"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="PIRACY_SHIELD_API_URL">API url</label>
                                            <div class="input-group">
                                                <input class="form-control" id="PIRACY_SHIELD_API_URL" name="PIRACY_SHIELD_API_URL" value="{{env("PIRACY_SHIELD_API_URL")}}" placeholder="https://service.domain.it">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <i class="fas fa-link text-dark mr-1"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="PIRACY_SHIELD_DNS_REDIRECT_IP">DNS redirect IP</label>
                                            <div class="input-group">
                                                <input class="form-control" id="PIRACY_SHIELD_DNS_REDIRECT_IP" name="PIRACY_SHIELD_DNS_REDIRECT_IP" value="{{env("PIRACY_SHIELD_DNS_REDIRECT_IP")}}" placeholder="127.0.0.1">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <i class="fas fa-hashtag text-dark mr-1"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="PIRACY_SHIELD_VPN_PEER_IP">VPN peer IP</label>
                                            <div class="input-group">
                                                <input class="form-control" id="PIRACY_SHIELD_VPN_PEER_IP" name="PIRACY_SHIELD_VPN_PEER_IP" value="{{env("PIRACY_SHIELD_VPN_PEER_IP")}}" placeholder="127.0.0.1">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <i class="fas fa-hashtag text-dark mr-1"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="PIRACY_SHIELD_VPN_REMOTE_LAN_IP">VPN remote LAN IP</label>
                                            <div class="input-group">
                                                <input class="form-control" id="PIRACY_SHIELD_VPN_REMOTE_LAN_IP" name="PIRACY_SHIELD_VPN_REMOTE_LAN_IP" value="{{env("PIRACY_SHIELD_VPN_REMOTE_LAN_IP")}}" placeholder="127.0.0.1">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <i class="fas fa-hashtag text-dark mr-1"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="PIRACY_SHIELD_VPN_LOCAL_LAN_IP">VPN local LAN IP</label>
                                            <div class="input-group">
                                                <input class="form-control" id="PIRACY_SHIELD_VPN_LOCAL_LAN_IP" name="PIRACY_SHIELD_VPN_LOCAL_LAN_IP" value="{{env("PIRACY_SHIELD_VPN_LOCAL_LAN_IP")}}" placeholder="127.0.0.1">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <i class="fas fa-hashtag text-dark mr-1"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="PIRACY_SHIELD_VPN_PSK">VPN pre-shared key</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="PIRACY_SHIELD_VPN_PSK" name="PIRACY_SHIELD_VPN_PSK" value="{{base64_decode(env("PIRACY_SHIELD_VPN_PSK"))}}" placeholder="127.0.0.1">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <i class="fas fa-key text-dark mr-1"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="cncpo" role="tabpanel" aria-labelledby="cncpo-tab">
                            <div class="card card-custom">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="CNCPO_ENABLED">Module</label>
                                            <div class="input-group">
                                                <select class="form-control" id="CNCPO_ENABLED" name="CNCPO_ENABLED">
                                                    <option value="0" @if(env("CNCPO_ENABLED") == "0") selected @endif>Disabled</option>
                                                    <option value="1" @if(env("CNCPO_ENABLED") == "1") selected @endif>Enabled</option>
                                                </select>
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <i class="fas fa-at text-dark mr-1"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="CNCPO_DOWNLOAD_URL">Download URL</label>
                                            <div class="input-group">
                                                <input class="form-control" id="CNCPO_DOWNLOAD_URL" name="CNCPO_DOWNLOAD_URL" value="{{env("CNCPO_DOWNLOAD_URL")}}" placeholder="https://xxx.xxx.xxx.xxx/Blacklist/Download">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <i class="fas fa-link text-dark mr-1"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="CNCPO_PFX_PATH">PFX cert path</label>
                                            <div class="input-group">
                                                <input class="form-control" id="CNCPO_PFX_PATH" name="CNCPO_PFX_PATH" value="{{env("CNCPO_PFX_PATH")}}" placeholder="/path/to/cert.pfx">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <i class="fas fa-certificate text-dark mr-1"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <span class="form-text text-dark">The certificate is provided by the authority in base64 encoding, to use the certificate correctly it is necessary to convert it with the following command:<br><pre>openssl enc -base64 -d -in in_file.pfx -out out_file.pfx</pre></span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="CNCPO_PFX_PASS">PFX cert password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="CNCPO_PFX_PASS" name="CNCPO_PFX_PASS" value="{{env("CNCPO_PFX_PASS")}}">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <i class="fas fa-certificate text-dark mr-1"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="CNCPO_DNS_REDIRECT_IP">DNS redirect IP</label>
                                            <div class="input-group">
                                                <input class="form-control" id="CNCPO_DNS_REDIRECT_IP" name="CNCPO_DNS_REDIRECT_IP" value="{{env("CNCPO_DNS_REDIRECT_IP")}}" placeholder="127.0.0.1">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <i class="fas fa-hashtag text-dark mr-1"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="adm" role="tabpanel" aria-labelledby="adm-tab">
                            <div class="card card-custom">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="ADM_ENABLED">Module</label>
                                            <div class="input-group">
                                                <select class="form-control" id="ADM_ENABLED" name="ADM_ENABLED">
                                                    <option value="0" @if(env("ADM_ENABLED") == "0") selected @endif>Disabled</option>
                                                    <option value="1" @if(env("ADM_ENABLED") == "1") selected @endif>Enabled</option>
                                                </select>
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <i class="fas fa-at text-dark mr-1"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="ADM_BETTING_URL">Betting download page URL</label>
                                            <div class="input-group">
                                                <input class="form-control" id="ADM_BETTING_URL" name="ADM_BETTING_URL" value="{{env("ADM_BETTING_URL")}}" placeholder="https://www.adm.gov.it/portale/siti-web-inibiti-giochi">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <i class="fas fa-link text-dark mr-1"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="ADM_SMOKING_URL">Smoking download page URL</label>
                                            <div class="input-group">
                                                <input class="form-control" id="ADM_SMOKING_URL" name="ADM_SMOKING_URL" value="{{env("ADM_SMOKING_URL")}}" placeholder="https://www.adm.gov.it/portale/siti-inibiti-tabacchi">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <i class="fas fa-link text-dark mr-1"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="ADM_DNS_REDIRECT_IP">DNS redirect IP</label>
                                            <div class="input-group">
                                                <input class="form-control" id="ADM_DNS_REDIRECT_IP" name="ADM_DNS_REDIRECT_IP" value="{{env("ADM_DNS_REDIRECT_IP")}}" placeholder="127.0.0.1">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <i class="fas fa-hashtag text-dark mr-1"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="manual" role="tabpanel" aria-labelledby="manual-tab">
                            <div class="card card-custom">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="ADM_ENABLED">Module</label>
                                            <div class="input-group">
                                                <select class="form-control" id="MANUAL_ENABLED" name="MANUAL_ENABLED">
                                                    <option value="0" @if(env("MANUAL_ENABLED") == "0") selected @endif>Disabled</option>
                                                    <option value="1" @if(env("MANUAL_ENABLED") == "1") selected @endif>Enabled</option>
                                                </select>
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <i class="fas fa-at text-dark mr-1"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <label for="MANUAL_DNS_REDIRECT_IP">DNS redirect IP</label>
                                            <div class="input-group">
                                                <input class="form-control" id="MANUAL_DNS_REDIRECT_IP" name="MANUAL_DNS_REDIRECT_IP" value="{{env("MANUAL_DNS_REDIRECT_IP")}}" placeholder="127.0.0.1">
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <i class="fas fa-hashtag text-dark mr-1"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="general" role="tabpanel" aria-labelledby="general-tab">
                            <div class="card card-custom">
                                <div class="card-body">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link active" id="various-tab" data-toggle="tab" href="#various" role="tab" aria-controls="various" aria-selected="true">
                                                <span class="nav-icon">
                                                    <i class="fas fa-cog"></i>
                                                </span>
                                                <span class="nav-text">Various</span>
                                            </a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="net-tab" data-toggle="tab" href="#net" role="tab" aria-controls="net">
                                                <span class="nav-icon">
                                                    <i class="fas fa-ethernet"></i>
                                                </span>
                                                <span class="nav-text">Network</span>
                                            </a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="bgp-tab" data-toggle="tab" href="#bgp" role="tab" aria-controls="bgp">
                                                <span class="nav-icon">
                                                    <i class="fas fa-bezier-curve"></i>
                                                </span>
                                                <span class="nav-text">BGP</span>
                                            </a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="primary-tab" data-toggle="tab" href="#primary" role="tab" aria-controls="primary">
                                                <span class="nav-icon">
                                                    <i class="fas fa-server"></i>
                                                </span>
                                                <span class="nav-text">Primary DNS</span>
                                            </a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="secondary-tab" data-toggle="tab" href="#secondary" role="tab" aria-controls="secondary">
                                                <span class="nav-icon">
                                                    <i class="fas fa-server"></i>
                                                </span>
                                                <span class="nav-text">Secondary DNS</span>
                                            </a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a class="nav-link" id="smtp-tab" data-toggle="tab" href="#smtp" role="tab" aria-controls="smtp">
                                                <span class="nav-icon">
                                                    <i class="fas fa-paper-plane"></i>
                                                </span>
                                                <span class="nav-text">SMTP</span>
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane fade active show" id="various" role="tabpanel" aria-labelledby="various-tab">
                                            <div class="card card-custom">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="EXTERNAL_DNS_SERVERS">External DSN servers (comma separated)</label>
                                                            <div class="input-group">
                                                                <input class="form-control" id="EXTERNAL_DNS_SERVERS" name="EXTERNAL_DNS_SERVERS" value="{{env("EXTERNAL_DNS_SERVERS")}}" placeholder="x.x.x.x,y.y.y.y,z.z.z.z">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <i class="fas fa-server text-dark mr-1"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="net" role="tabpanel" aria-labelledby="net-tab">
                                            <div class="card card-custom">
                                                <div class="card-body">
                                                    <div class="alert alert-custom alert-light-warning show mb-5" role="alert">
                                                        <div class="alert-icon"><i class="flaticon-warning"></i></div>
                                                        <div class="alert-text">Warning: if you update the network configuration with bad data you have to reset the default IP via command console.</div>
                                                        <div class="alert-close">
                                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                                <span aria-hidden="true"><i class="ki ki-close"></i></span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="NET_IP">IP address</label>
                                                            <div class="input-group">
                                                                <input class="form-control" id="NET_IP" name="NET_IP" value="{{env("NET_IP")}}" placeholder="xxx.xxx.xxx.xxx">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <i class="fas fa-server text-dark mr-1"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="NET_MASK">Subnet netmask</label>
                                                            <div class="input-group">
                                                                <input class="form-control" id="NET_MASK" name="NET_MASK" value="{{env("NET_MASK")}}" placeholder="xxx.xxx.xxx.xxx">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <i class="fas fa-server text-dark mr-1"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="NET_GATEWAY">Gateway</label>
                                                            <div class="input-group">
                                                                <input class="form-control" id="NET_GATEWAY" name="NET_GATEWAY" value="{{env("NET_GATEWAY")}}" placeholder="xxx.xxx.xxx.xxx">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <i class="fas fa-server text-dark mr-1"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="bgp" role="tabpanel" aria-labelledby="bgp-tab">
                                            <div class="card card-custom">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="BGP_ROUTER_IP">BGP router IP</label>
                                                            <div class="input-group">
                                                                <input class="form-control" id="BGP_ROUTER_IP" name="BGP_ROUTER_IP" value="{{env("BGP_ROUTER_IP")}}" placeholder="xxx.xxx.xxx.xxx">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <i class="fas fa-server text-dark mr-1"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="BGP_ASN">ASN</label>
                                                            <div class="input-group">
                                                                <input class="form-control" id="BGP_ASN" name="BGP_ASN" value="{{env("BGP_ASN")}}" placeholder="65100">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <i class="fas fa-user text-dark mr-1"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="BGP_LOCAL_IP">Local IP</label>
                                                            <div class="input-group">
                                                                <input class="form-control" id="BGP_LOCAL_IP" name="BGP_LOCAL_IP" value="{{env("BGP_LOCAL_IP")}}" placeholder="xxx.xxx.xxx.xxx">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <i class="fas fa-server text-dark mr-1"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="BGP_LOCAL_MASK">Local IP netmask</label>
                                                            <div class="input-group">
                                                                <input class="form-control" id="BGP_LOCAL_MASK" name="BGP_LOCAL_MASK" value="{{env("BGP_LOCAL_MASK")}}" placeholder="xxx.xxx.xxx.xxx">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <i class="fas fa-server text-dark mr-1"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="BGP_LOCAL_GATEWAY">Local IP gateway</label>
                                                            <div class="input-group">
                                                                <input class="form-control" id="BGP_LOCAL_GATEWAY" name="BGP_LOCAL_GATEWAY" value="{{env("BGP_LOCAL_GATEWAY")}}" placeholder="xxx.xxx.xxx.xxx">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <i class="fas fa-server text-dark mr-1"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="primary" role="tabpanel" aria-labelledby="primary-tab">
                                            <div class="card card-custom">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="DNS_SERVER_PRIMARY_IP">IP address</label>
                                                            <div class="input-group">
                                                                <input class="form-control" id="DNS_SERVER_PRIMARY_IP" name="DNS_SERVER_PRIMARY_IP" value="{{env("DNS_SERVER_PRIMARY_IP")}}" placeholder="xxx.xxx.xxx.xxx">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <i class="fas fa-server text-dark mr-1"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="DNS_SERVER_PRIMARY_PORT">SSH port</label>
                                                            <div class="input-group">
                                                                <input class="form-control" id="DNS_SERVER_PRIMARY_PORT" name="DNS_SERVER_PRIMARY_PORT" value="{{env("DNS_SERVER_PRIMARY_PORT")}}" placeholder="22">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <i class="fas fa-server text-dark mr-1"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="DNS_SERVER_PRIMARY_USER">SSH username</label>
                                                            <div class="input-group">
                                                                <input class="form-control" id="DNS_SERVER_PRIMARY_USER" name="DNS_SERVER_PRIMARY_USER" value="{{env("DNS_SERVER_PRIMARY_USER")}}" placeholder="root">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <i class="fas fa-user text-dark mr-1"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="DNS_SERVER_PRIMARY_PSW">SSH password</label>
                                                            <div class="input-group">
                                                                <input type="password" class="form-control" id="DNS_SERVER_PRIMARY_PSW" name="DNS_SERVER_PRIMARY_PSW" value="{{env("DNS_SERVER_PRIMARY_PSW")}}">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <i class="fas fa-key text-dark mr-1"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="DNS_SERVER_PRIMARY_PATH">Zone path</label>
                                                            <div class="input-group">
                                                                <input class="form-control" id="DNS_SERVER_PRIMARY_PATH" name="DNS_SERVER_PRIMARY_PATH" value="{{env("DNS_SERVER_PRIMARY_PATH")}}" placeholder="/etc/bind/named.conf.block">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <i class="fas fa-file text-dark mr-1"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="DNS_SERVER_PRIMARY_RELOAD">Service reload command</label>
                                                            <div class="input-group">
                                                                <input class="form-control" id="DNS_SERVER_PRIMARY_RELOAD" name="DNS_SERVER_PRIMARY_RELOAD" value="{{env("DNS_SERVER_PRIMARY_RELOAD")}}" placeholder="service named reload">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <i class="fas fa-terminal text-dark mr-1"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="secondary" role="tabpanel" aria-labelledby="secondary-tab">
                                            <div class="card card-custom">
                                                <div class="card-body">
                                                    If secondary DNS not needed, do not complete the IP address field.
                                                    <hr>
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="DNS_SERVER_SECONDARY_IP">IP address</label>
                                                            <div class="input-group">
                                                                <input class="form-control" id="DNS_SERVER_SECONDARY_IP" name="DNS_SERVER_SECONDARY_IP" value="{{env("DNS_SERVER_SECONDARY_IP")}}" placeholder="xxx.xxx.xxx.xxx">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <i class="fas fa-server text-dark mr-1"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="DNS_SERVER_SECONDARY_PORT">SSH port</label>
                                                            <div class="input-group">
                                                                <input class="form-control" id="DNS_SERVER_SECONDARY_PORT" name="DNS_SERVER_SECONDARY_PORT" value="{{env("DNS_SERVER_SECONDARY_PORT")}}" placeholder="22">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <i class="fas fa-server text-dark mr-1"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="DNS_SERVER_SECONDARY_USER">SSH username</label>
                                                            <div class="input-group">
                                                                <input class="form-control" id="DNS_SERVER_SECONDARY_USER" name="DNS_SERVER_SECONDARY_USER" value="{{env("DNS_SERVER_SECONDARY_USER")}}" placeholder="root">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <i class="fas fa-user text-dark mr-1"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="DNS_SERVER_SECONDARY_PSW">SSH password</label>
                                                            <div class="input-group">
                                                                <input type="password" class="form-control" id="DNS_SERVER_SECONDARY_PSW" name="DNS_SERVER_SECONDARY_PSW" value="{{env("DNS_SERVER_SECONDARY_PSW")}}">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <i class="fas fa-key text-dark mr-1"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="DNS_SERVER_SECONDARY_PATH">Zone path</label>
                                                            <div class="input-group">
                                                                <input class="form-control" id="DNS_SERVER_SECONDARY_PATH" name="DNS_SERVER_SECONDARY_PATH" value="{{env("DNS_SERVER_SECONDARY_PATH")}}" placeholder="/etc/bind/named.conf.block">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <i class="fas fa-file text-dark mr-1"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="DNS_SERVER_SECONDARY_RELOAD">Service reload command</label>
                                                            <div class="input-group">
                                                                <input class="form-control" id="DNS_SERVER_SECONDARY_RELOAD" name="DNS_SERVER_SECONDARY_RELOAD" value="{{env("DNS_SERVER_SECONDARY_RELOAD")}}" placeholder="service named reload">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <i class="fas fa-terminal text-dark mr-1"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="smtp" role="smtp" aria-labelledby="smtp-tab">
                                            <div class="card card-custom">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="form-group col-md-10">
                                                            <label for="MAIL_HOST">SMTP server host</label>
                                                            <div class="input-group">
                                                                <input class="form-control" id="MAIL_HOST" name="MAIL_HOST" value="{{env("MAIL_HOST")}}" placeholder="smtp.provider.com">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <i class="fas fa-server text-dark mr-1"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-md-2">
                                                            <label for="MAIL_PORT">Port</label>
                                                            <div class="input-group">
                                                                <input class="form-control" id="MAIL_PORT" name="MAIL_PORT" value="{{env("MAIL_PORT")}}" placeholder="25|587|465...">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <i class="fas fa-hashtag text-dark mr-1"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="MAIL_ENCRYPTION">Encryption</label>
                                                            <div class="input-group">
                                                                <select class="form-control" id="MAIL_ENCRYPTION" name="MAIL_ENCRYPTION">
                                                                    <option value="null" @if(env("MAIL_ENCRYPTION") == null) selected @endif>None</option>
                                                                    <option value="ssl" @if(env("MAIL_ENCRYPTION") == "ssl") selected @endif>SSL</option>
                                                                    <option value="tls" @if(env("MAIL_ENCRYPTION") == "tls") selected @endif>STARTTLS</option>
                                                                </select>
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <i class="fas fa-user text-dark mr-1"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-md-6">
                                                            <label for="MAIL_USERNAME">Username</label>
                                                            <div class="input-group">
                                                                <input class="form-control" id="MAIL_USERNAME" name="MAIL_USERNAME" value="{{env("MAIL_USERNAME")}}">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <i class="fas fa-user text-dark mr-1"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label for="MAIL_PASSWORD">Password</label>
                                                            <div class="input-group">
                                                                <input type="password" class="form-control" id="MAIL_PASSWORD" name="MAIL_PASSWORD" value="{{env("MAIL_PASSWORD")}}">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <i class="fas fa-key text-dark mr-1"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-md-6">
                                                            <label for="MAIL_FROM_ADDRESS">From address</label>
                                                            <div class="input-group">
                                                                <input class="form-control" id="MAIL_FROM_ADDRESS" name="MAIL_FROM_ADDRESS" value="{{env("MAIL_FROM_ADDRESS")}}" placeholder="manager@domain.com">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <i class="fas fa-id-badge text-dark mr-1"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group col-md-6">
                                                            <label for="MAIL_FROM_NAME">From name</label>
                                                            <div class="input-group">
                                                                <input class="form-control" id="MAIL_FROM_NAME" name="MAIL_FROM_NAME" value="{{env("MAIL_FROM_NAME")}}" placeholder="Manager">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <i class="fas fa-id-badge text-dark mr-1"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label for="MAIL_TO_ADDRESSES">To addresses (comma separated)</label>
                                                            <div class="input-group">
                                                                <input class="form-control" id="MAIL_TO_ADDRESSES" name="MAIL_TO_ADDRESSES" value="{{env("MAIL_TO_ADDRESSES")}}" placeholder="me@domain.com,you@domain.com,they@domain.com">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <i class="fas fa-id-badge text-dark mr-1"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->

@endsection