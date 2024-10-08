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
                    <h5 class="text-dark font-weight-bold my-1 mr-5">Actions Logs</h5>
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
                <div class="card-header flex-wrap py-5">
                    <div class="card-title">
                        <h3 class="card-label">Log <small>last 24 hours shown</small></h3>
                    </div>
                    <div class="card-toolbar">
                        <label class="text-right mr-3">Hide systems and crons</label>
                        <span class="switch d-inline">
                            <label>
                                <input id="hide_system_cron" type="checkbox" name="select"/>
                                <span></span>
                            </label>
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-separate table-head-custom table-checkable" id="datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Timestamp</th>
                                <th>Username</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
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
        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
			pagingType: 'full_numbers',
            ajax: {
                url: '/admin/logs/actions/get',
                type: 'GET',
                data: function(data){
                    data.hide_system_cron = $('#hide_system_cron').is(":checked");
                }
            },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'timestamp', name: 'timestamp'},
                {data: 'username', name: 'username'},
                {data: 'action', name: 'action'}
            ],
            language: {
                "processing": "<i class=\"fas fa-2x fa-spin fa-spinner\"></i>"
            },
            "order": [[ 0, 'desc' ]],
            "drawCallback": function( settings, json ) {
                $('#datatable_wrapper').addClass('row');
                $('#datatable_filter').addClass('col-sm-12 col-md-6');
                $('#datatable_length').addClass('col-sm-12 col-md-6');
                $('#datatable_info').addClass('col-sm-12 col-md-6');
                $('#datatable_paginate').addClass('col-sm-12 col-md-6');
                $('#datatable_filter input').addClass('form-control form-control-sm');
                $('#datatable_length select').addClass('custom-select custom-select-sm form-control form-control-sm');
                $('a.paginate_button.current').addClass('btn btn-primary');
                $('a.paginate_button').not('.current').addClass('btn btn-outline-secondary');
            },
            columnDefs: [
                {
                    targets: 1,
                    className: 'text-right'
                },
                {
                    targets: 2,
                    className: 'text-right'
                },
                {
                    targets: 3,
                    className: 'break'
                }
            ]
        });

        $('#hide_system_cron').click(function(){
            $('#datatable').DataTable().ajax.reload();
        });
    });
</script>

@endsection