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
                    <h5 class="text-dark font-weight-bold my-1 mr-5">FQDNs</h5>
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
                        <h3 class="card-label">List</h3>
                    </div>
                    <div class="card-toolbar">
                        <a target="_blank" href="/piracy/lists/fqdn/download/offline" class="btn btn-sm btn-primary font-weight-bold">
                            <i class="flaticon2-download"></i> Offline
                        </a>
                        <a target="_blank" href="/piracy/lists/fqdn/download/online" class="btn btn-sm btn-primary font-weight-bold ml-2">
                            <i class="flaticon2-download"></i> Online
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-separate table-head-custom table-checkable" id="datatable">
                        <thead>
                            <tr>
                                <th>FQDN</th>
                                <th>Timestamp</th>
                                <th>Origin Ticket ID</th>
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
            ajax: "/piracy/lists/fqdn/get",
            columns: [
                {data: 'fqdn', name: 'fqdn'},
                {data: 'timestamp', name: 'timestamp'},
                {data: 'original_ticket_id', name: 'original_ticket_id'}
            ],
            language: {
                "processing": "<i class=\"fas fa-2x fa-spin fa-spinner\"></i>"
            },
            "order": [[ 1, 'desc' ]],
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
            }
        });
    });
</script>

@endsection