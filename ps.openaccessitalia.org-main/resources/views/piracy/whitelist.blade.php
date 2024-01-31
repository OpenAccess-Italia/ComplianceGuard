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
                    <h5 class="text-dark font-weight-bold my-1 mr-5">Whitelist</h5>
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
                    <b>Add to whitelist</b>
                    <div class="row">
                        <div class="form-group col-md-5">
                            <label for="item">Item</label>
                            <div class="input-group">
                                <input class="form-control" id="item" name="item" placeholder="Ex. 1.1.1.1, xyz.com, 2001:db8:3333:4444:5555:6666:7777:8888">
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="genre">Genre</label>
                            <div class="input-group">
                                <select class="form-control" id="genre" name="genre">
                                    <option value="fqdn" data-attr-label="Registrar" data-attr-placeholder="Ex. ARUBA,REGISTER...">FQDN</option>
                                    <option value="ipv4" data-attr-label="AS number" data-attr-placeholder="Ex. ASxxxxx">IPv4</option>
                                    <option value="ipv6" data-attr-label="AS number" data-attr-placeholder="Ex. ASxxxxx">IPv6</option>
                                    <option value="cidr_ipv4" data-attr-label="AS number" data-attr-placeholder="Ex. ASxxxxx">CIDR IPv4</option>
                                    <option value="cidr_ipv6" data-attr-label="AS number" data-attr-placeholder="Ex. ASxxxxx">CIDR IPv6</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="attr" id="attr-label">Registrar</label>
                            <div class="input-group">
                                <input class="form-control" id="attr" name="attr" placeholder="Ex. ARUBA,REGISTER...">
                            </div>
                        </div>
                        <div class="form-group col-md-1">
                            <label>&nbsp;</label>
                            <div class="input-group">
                                <button id="add" class="btn btn-icon btn-primary"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                    <table class="table table-separate table-head-custom table-checkable" id="datatable">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Genre</th>
                                <th>Is active</th>
                                <th>Timestamp</th>
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
        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
			pagingType: 'full_numbers',
            ajax: "/piracy/whitelist/get",
            columns: [
                {data: 'value', name: 'value'},
                {data: 'genre', name: 'genre'},
                {data: 'is_active', name: 'is_active'},
                {data: 'metadata.created_at', name: 'metadata.created_at'},
                {
                    defaultContent: '<button class="btn btn-danger btn-icon btn-xs del"><i class="fas fa-trash"></i></button>',
                    className: 'text-center'
                }
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
                $('.del').off('click').on('click',function(){
                    if(confirm('Do you want to delete this item from whitelist?')){
                        var row = $(this).closest('tr');
                        var data = table.row(row).data();
                        $.ajax({
                            type: "POST",
                            url: '/piracy/whitelist/delete',
                            data: {
                                genre: data.genre,
                                item: data.value
                            },
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')},
                            statusCode: {
                                200: function(data) {
                                    table.ajax.reload();
                                },
                                404: function() {
                                    $('#modal_alert_content').html('<div class="alert alert-custom alert-danger" role="alert"><div class="alert-icon"><i class="flaticon-cancel"></i></div><div class="alert-text">Error, view log for more details</div></div>')
                                    $('#modal_alert').modal('show');
                                },
                                500: function() {
                                    $('#modal_alert_content').html('<div class="alert alert-custom alert-danger" role="alert"><div class="alert-icon"><i class="flaticon-cancel"></i></div><div class="alert-text">Error, view log for more details</div></div>')
                                    $('#modal_alert').modal('show');
                                }
                            }
                        });
                    }
                });
            }
        });

        $('#genre').change(function(){
            $('#attr-label').html($('#genre option:selected').data('attr-label'));
            $('#attr').attr('placeholder',$('#genre option:selected').data('attr-placeholder'));
        });

        $('#add').on('click',function(){
            if($('#item').val() != '' && $('#attr').val() != ''){
                $.ajax({
                    type: "POST",
                    url: '/piracy/whitelist/add',
                    data: {
                        item: $('#item').val(),
                        genre: $('#genre').val(),
                        attr: $('#attr').val(),
                    },
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')},
                    statusCode: {
                        200: function(data) {
                            table.ajax.reload();
                        },
                        404: function() {
                            $('#modal_alert_content').html('<div class="alert alert-custom alert-danger" role="alert"><div class="alert-icon"><i class="flaticon-cancel"></i></div><div class="alert-text">Error, view log for more details</div></div>')
                            $('#modal_alert').modal('show');
                        },
                        500: function() {
                            $('#modal_alert_content').html('<div class="alert alert-custom alert-danger" role="alert"><div class="alert-icon"><i class="flaticon-cancel"></i></div><div class="alert-text">Error, view log for more details</div></div>')
                            $('#modal_alert').modal('show');
                        }
                    }
                });
            }else{

            }
        });
    });
</script>

@endsection