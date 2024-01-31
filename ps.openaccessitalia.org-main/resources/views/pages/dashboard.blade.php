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
                    <h5 class="text-dark font-weight-bold my-1 mr-5">Dashboard</h5>
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
            <div class="row">
                <div class="col-lg-3">
                    <!--begin::Stats Widget 1-->
                    <div class="card card-custom card-stretch gutter-b">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-6">
                            <h3 class="card-title">
                                <span class="card-label font-weight-bolder font-size-h4 text-dark-75">Ciao <span class="text-primary">{{\Auth::user()->friendly_name}}</span></span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body d-flex align-items-center justify-content-between pt-7 flex-wrap">
                            @if(Auth::user()->id == 1 && Auth::user()->admin == 1)
                                <div class="alert alert-custom alert-light-warning show mb-5" role="alert">
                                    <div class="alert-icon"><i class="flaticon-warning"></i></div>
                                    <div class="alert-text">You should not use this user. You should create a new user with admin policy and disable this one.</div>
                                    <div class="alert-close">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true"><i class="ki ki-close"></i></span>
                                        </button>
                                    </div>
                                </div>
                            @else
                                <!--begin::label-->
                                <span class="font-weight-bolder w-100 text-center display5 text-dark-75 py-4 pl-5 pr-5">
                                    <div class="symbol symbol-100 mr-5">
                                        @if(Auth::user()->avatar)
                                            <div class="symbol-label" style="background-image:url('{{ Auth::user()->avatar }}')"></div>
                                        @else
                                            <span class="symbol-label font-size-h1 font-weight-bold">{{strtoupper(substr(Auth::user()->friendly_name,0,1))}}</span>
                                        @endif
                                        <i class="symbol-badge bg-success"></i>
                                    </div>
                                </span>
                                <!--end::label-->
                            @endif
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 1-->
                </div>
                @if(\Auth::user()->piracy && env("PIRACY_SHIELD_ENABLED") == "1")
                <div class="col-lg-4">
                    <!--begin::Stats Widget 2-->
                    <div class="card card-custom card-stretch gutter-b">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-6">
                            <h3 class="card-title">
                                <span class="card-label font-weight-bolder font-size-h4 text-dark-75">Piracy Shield</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body d-flex align-items-center justify-content-between pt-7 flex-wrap">
                            <table class="table" id="cpus_state_table">
                                <tr>
                                    <th scope="col" class="text-center"></th>
                                    <th scope="col" class="text-center">Tickets</th>
                                    <th scope="col" class="text-center">FQDNs</th>
                                    <th scope="col" class="text-center">IPv4s</th>
                                    <th scope="col" class="text-center">IPv6s</th>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-center"><i title="Count" class="fas text-dark fa-hashtag"></i></th>
                                    <td class="text-center"><a href="/piracy/lists/tickets">{{ \App\Piracy\Tickets::get()->count() }}</a></td>
                                    <td class="text-center"><a href="/piracy/lists/fqdn">{{ \App\Piracy\FQDNs::get()->count() }}</a></td>
                                    <td class="text-center"><a href="/piracy/lists/ipv4">{{ \App\Piracy\IPv4s::get()->count() }}</a></td>
                                    <td class="text-center"><a href="/piracy/lists/ipv6">{{ \App\Piracy\IPv6s::get()->count() }}</a></td>
                                </tr>
                            </table>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 2-->
                </div>
                @endif
            </div>
            <div class="row">
                @if(\Auth::user()->cncpo && env("CNCPO_ENABLED") == "1")
                <div class="col-lg-2">
                    <!--begin::Stats Widget 2-->
                    <div class="card card-custom card-stretch gutter-b">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-6">
                            <h3 class="card-title">
                                <span class="card-label font-weight-bolder font-size-h4 text-dark-75">CNCPO</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body d-flex align-items-center justify-content-between pt-7 flex-wrap">
                            <table class="table" id="cpus_state_table">
                                <tr>
                                    <th scope="col" class="text-center"></th>
                                    <th scope="col" class="text-center">Items</th>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-center"><i title="Count" class="fas text-dark fa-hashtag"></i></th>
                                    @if(\App\CNCPO\Files::where('id', \DB::raw("(select max(`id`) from cncpo_files)"))->first())
                                        <td class="text-center"><a href="/cncpo/blacklist">{{ \App\CNCPO\Blacklist::where('id', \DB::raw("(select max(`id`) from cncpo_blacklist)"))->first()->id }}</a></td>
                                    @else
                                        <td class="text-center">No items</td>
                                    @endif
                                </tr>
                                <tr>
                                    <th scope="row" class="text-center"><i title="Updated" class="fas text-dark fa-clock"></i></th>
                                    @if(\App\CNCPO\Files::where('id', \DB::raw("(select max(`id`) from cncpo_files)"))->first())
                                        <td class="text-center">{{ \App\CNCPO\Files::where('id', \DB::raw("(select max(`id`) from cncpo_files)"))->first()->timestamp->format("d/m/Y H:i:s") }}</td>
                                    @else
                                        <td class="text-center">No files</td>
                                    @endif
                                </tr>
                            </table>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 2-->
                </div>
                @endif
                @if(\Auth::user()->adm && env("ADM_ENABLED") == "1")
                <div class="col-lg-3">
                    <!--begin::Stats Widget 2-->
                    <div class="card card-custom card-stretch gutter-b">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-6">
                            <h3 class="card-title">
                                <span class="card-label font-weight-bolder font-size-h4 text-dark-75">ADM</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body d-flex align-items-center justify-content-between pt-7 flex-wrap">
                            <table class="table" id="cpus_state_table">
                                <tr>
                                    <th scope="col" class="text-center"></th>
                                    <th scope="col" class="text-center">Betting</th>
                                    <th scope="col" class="text-center">Smoking</th>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-center"><i title="Count" class="fas text-dark fa-hashtag"></i></th>
                                    @if (\App\ADM\BettingBlacklist::where('id', \DB::raw("(select max(`id`) from adm_betting_blacklist)"))->first())
                                        <td class="text-center"><a href="/adm/betting/blacklist">{{ \App\ADM\BettingBlacklist::where('id', \DB::raw("(select max(`id`) from adm_betting_blacklist)"))->first()->id }}</a></td>
                                    @else
                                        <td class="text-center">No items</td>
                                    @endif
                                    @if (\App\ADM\SmokingBlacklist::where('id', \DB::raw("(select max(`id`) from adm_smoking_blacklist)"))->first())
                                        <td class="text-center"><a href="/adm/smoking/blacklist">{{ \App\ADM\SmokingBlacklist::where('id', \DB::raw("(select max(`id`) from adm_smoking_blacklist)"))->first()->id }}</a></td>
                                    @else
                                        <td class="text-center">No items</td>
                                    @endif
                                </tr>
                                <tr>
                                    <th scope="row" class="text-center"><i title="Updated" class="fas text-dark fa-clock"></i></th>
                                    @if (\App\ADM\BettingBlacklist::where('id', \DB::raw("(select max(`id`) from adm_betting_blacklist)"))->first())
                                        <td class="text-center">{{ \App\ADM\BettingFiles::where('id', \DB::raw("(select max(`id`) from adm_betting_files)"))->first()->timestamp->format("d/m/Y H:i:s") }}</td>
                                    @else
                                        <td class="text-center">No files</td>
                                    @endif
                                    @if (\App\ADM\SmokingBlacklist::where('id', \DB::raw("(select max(`id`) from adm_smoking_blacklist)"))->first())
                                        <td class="text-center">{{ \App\ADM\SmokingFiles::where('id', \DB::raw("(select max(`id`) from adm_smoking_files)"))->first()->timestamp->format("d/m/Y H:i:s") }}</td>
                                    @else
                                        <td class="text-center">No files</td>
                                    @endif
                                </tr>
                            </table>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 2-->
                </div>
                @endif
                @if(\Auth::user()->manual && env("MANUAL_ENABLED") == "1")
                <div class="col-lg-5">
                    <!--begin::Stats Widget 2-->
                    <div class="card card-custom card-stretch gutter-b">
                        <!--begin::Header-->
                        <div class="card-header border-0 pt-6">
                            <h3 class="card-title">
                                <span class="card-label font-weight-bolder font-size-h4 text-dark-75">Manual</span>
                            </h3>
                        </div>
                        <!--end::Header-->
                        <!--begin::Body-->
                        <div class="card-body d-flex align-items-center justify-content-between pt-7 flex-wrap">
                            <table class="table" id="cpus_state_table">
                                <tr>
                                    <th scope="col" class="text-center"></th>
                                    <th scope="col" class="text-center">FQDNs</th>
                                    <th scope="col" class="text-center">IPv4s</th>
                                    <th scope="col" class="text-center">IPv6s</th>
                                </tr>
                                <tr>
                                    <th scope="row" class="text-center"><i title="Count" class="fas text-dark fa-hashtag"></i></th>
                                    <td class="text-center"><a href="/manual/lists/fqdn">{{ \App\Manual\FQDNs::get()->count() }}</a></td>
                                    <td class="text-center"><a href="/manual/lists/ipv4">{{ \App\Manual\IPv4s::get()->count() }}</a></td>
                                    <td class="text-center"><a href="/manual/lists/ipv6">{{ \App\Manual\IPv6s::get()->count() }}</a></td>
                                </tr>
                            </table>
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Stats Widget 2-->
                </div>
                @endif
            </div>
        </div>
        <!--end::Container-->
    </div>
    <!--end::Entry-->
</div>
<!--end::Content-->

<script>
    
</script>
@endsection