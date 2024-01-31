@extends('layouts.home')

@section('page')

<style>
    .break{
        word-break: break-word;
    }
</style>

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
                    <h5 class="text-dark font-weight-bold my-1 mr-5">PS APIs</h5>
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
                            <a class="nav-link active" id="requests-tab" data-toggle="tab" href="#requests" role="tab" aria-controls="requests" aria-selected="true">
                                <span class="nav-icon">
                                    <i class="fas fa-question"></i>
                                </span>
                                <span class="nav-text">Requests (last 24h)</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="access-tab" data-toggle="tab" href="#access" role="tab" aria-controls="access" aria-selected="true">
                                <span class="nav-icon">
                                    <i class="fas fa-key"></i>
                                </span>
                                <span class="nav-text">Access tokens</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="refresh-tab" data-toggle="tab" href="#refresh" role="tab" aria-controls="refresh" aria-selected="false">
                                <span class="nav-icon">
                                    <i class="fas fa-sync"></i>
                                </span>
                                <span class="nav-text">Refresh tokens</span>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="requests" role="tabpanel" aria-labelledby="requests-tab">
                            <div class="card card-custom">
                                <div class="card-body">
                                    <table class="table table-separate table-head-custom table-checkable" id="requests_datatable">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Timestamp</th>
                                                <th>Method</th>
                                                <th>Endpoint</th>
                                                <th>Response code</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="access" role="tabpanel" aria-labelledby="access-tab">
                            <div class="card card-custom">
                                <div class="card-body">
                                    <table class="table table-separate table-head-custom table-checkable" id="access_datatable">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Timestamp</th>
                                                <th>Token</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="refresh" role="tabpanel" aria-labelledby="refresh-tab">
                            <div class="card card-custom">
                                <div class="card-body">
                                    <table class="table table-separate table-head-custom table-checkable" id="refresh_datatable">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Timestamp</th>
                                                <th>Token</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
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

<script>
    $(document).ready(function(){
        var requests_table = $('#requests_datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
			pagingType: 'full_numbers',
            ajax: {
                url: '/admin/logs/ps_api/get',
                type: 'GET'
            },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'timestamp', name: 'timestamp'},
                {data: 'method', name: 'method'},
                {data: 'endpoint', name: 'endpoint'},
                {data: 'code', name: 'code'},
                {
                    className: 'dt-control',
                    orderable: false,
                    data: null,
                    defaultContent: '<i class="fas fa-plus text-dark"></i>'
                }
            ],
            language: {
                "processing": "<i class=\"fas fa-2x fa-spin fa-spinner\"></i>"
            },
            "order": [[ 0, 'desc' ]],
            "drawCallback": function( settings, json ) {
                $('#requests_datatable_wrapper').addClass('row');
                $('#requests_datatable_filter').addClass('col-sm-12 col-md-6');
                $('#requests_datatable_length').addClass('col-sm-12 col-md-6');
                $('#requests_datatable_info').addClass('col-sm-12 col-md-6');
                $('#requests_datatable_paginate').addClass('col-sm-12 col-md-6');
                $('#requests_datatable_filter input').addClass('form-control form-control-sm');
                $('#requests_datatable_length select').addClass('custom-select custom-select-sm form-control form-control-sm');
                $('a.paginate_button.current').addClass('btn btn-primary');
                $('a.paginate_button').not('.current').addClass('btn btn-outline-secondary');
            }
        });
        requests_table.on('click', 'td.dt-control', function (e) {
            let tr = e.target.closest('tr');
            let row = requests_table.row(tr);
            if(row.child.isShown()){
                row.child.hide();
                $(this).html('<i class="fas fa-plus text-dark"></i>');
            }else{
                row.child(format(row.data())).show();
                $(this).html('<i class="fas fa-minus text-dark"></i>');
            }
        });
        var access_table = $('#access_datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
			pagingType: 'full_numbers',
            ajax: {
                url: '/admin/logs/ps_access/get',
                type: 'GET'
            },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'timestamp', name: 'timestamp'},
                {data: 'access_token', name: 'access_token'}
            ],
            language: {
                "processing": "<i class=\"fas fa-2x fa-spin fa-spinner\"></i>"
            },
            "order": [[ 0, 'desc' ]],
            "drawCallback": function( settings, json ) {
                $('#access_datatable_wrapper').addClass('row');
                $('#access_datatable_filter').addClass('col-sm-12 col-md-6');
                $('#access_datatable_length').addClass('col-sm-12 col-md-6');
                $('#access_datatable_info').addClass('col-sm-12 col-md-6');
                $('#access_datatable_paginate').addClass('col-sm-12 col-md-6');
                $('#access_datatable_filter input').addClass('form-control form-control-sm');
                $('#access_datatable_length select').addClass('custom-select custom-select-sm form-control form-control-sm');
                $('a.paginate_button.current').addClass('btn btn-primary');
                $('a.paginate_button').not('.current').addClass('btn btn-outline-secondary');
            },
            columnDefs: [
                {
                    targets: 2,
                    className: 'break'
                },
            ]
        });
        var refresh_table = $('#refresh_datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
			pagingType: 'full_numbers',
            ajax: {
                url: '/admin/logs/ps_refresh/get',
                type: 'GET'
            },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'timestamp', name: 'timestamp'},
                {data: 'refresh_token', name: 'refresh_token'}
            ],
            language: {
                "processing": "<i class=\"fas fa-2x fa-spin fa-spinner\"></i>"
            },
            "order": [[ 0, 'desc' ]],
            "drawCallback": function( settings, json ) {
                $('#refresh_datatable_wrapper').addClass('row');
                $('#refresh_datatable_filter').addClass('col-sm-12 col-md-6');
                $('#refresh_datatable_length').addClass('col-sm-12 col-md-6');
                $('#refresh_datatable_info').addClass('col-sm-12 col-md-6');
                $('#refresh_datatable_paginate').addClass('col-sm-12 col-md-6');
                $('#refresh_datatable_filter input').addClass('form-control form-control-sm');
                $('#refresh_datatable_length select').addClass('custom-select custom-select-sm form-control form-control-sm');
                $('a.paginate_button.current').addClass('btn btn-primary');
                $('a.paginate_button').not('.current').addClass('btn btn-outline-secondary');
            },
            columnDefs: [
                {
                    targets: 2,
                    className: 'break',
                },
            ]
        });
    });

    function format(d) {
        var html = '';
        html += '<div class="row">';
        html += '<div class="col-md-12"><b>Token:</b><textarea class="form-control" rows=3>'+d.token+'</textarea></div>';
        html += '</div>'
        html += '<div class="row">';
        html += '<div class="col-md-6"><b>Request body:</b><textarea class="form-control" rows=3>'+d.body+'</textarea></div>';
        html += '<div class="col-md-6"><b>Response body:</b><textarea class="form-control" rows=5>'+d.answer+'</textarea></div>';
        html += '</div>'
        return html;
    }
</script>

@endsection