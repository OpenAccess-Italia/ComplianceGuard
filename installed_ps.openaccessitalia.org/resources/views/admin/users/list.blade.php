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
                    <h5 class="text-dark font-weight-bold my-1 mr-5">Users list</h5>
                    <!--end::Page Title-->
                </div>
                <!--end::Page Heading-->
            </div>
            <!--end::Info-->
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
                        <h3 class="card-label">Users list</h3>
                    </div>
                    <div class="card-toolbar">
                        <!--begin::Button-->
                        <a href="/admin/users/new" class="btn btn-primary font-weight-bolder"><i class="fas fa-plus mr-1"></i>Nuovo utente</a>
                        <!--end::Button-->
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-separate table-head-custom table-checkable" id="datatable">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th class="text-center">Admin</th>
                                <th class="text-center">Enabled</th>
                                <th class="text-center">Piracy Shield</th>
                                <th class="text-center">CNCPO</th>
                                <th class="text-center">ADM</th>
                                <th class="text-center">Manual</th>
                                <th class="text-center"><i class="fas fa-bolt"></i></th>
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
            ajax: "/admin/users/list/get",
            columns: [
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {data: 'admin', name: 'admin', className: 'text-center'},
                {data: 'enabled', name: 'enabled', className: 'text-center'},
                {data: 'piracy', name: 'piracy', className: 'text-center'},
                {data: 'cncpo', name: 'cncpo', className: 'text-center'},
                {data: 'adm', name: 'adm', className: 'text-center'},
                {data: 'manual', name: 'manual', className: 'text-center'},
                {data: 'action', name: 'action', className: 'text-center', orderable: false, searchable: false},
            ],
            language: {
                "processing": "<i class=\"fas fa-2x fa-spin fa-spinner\"></i>"
            },
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