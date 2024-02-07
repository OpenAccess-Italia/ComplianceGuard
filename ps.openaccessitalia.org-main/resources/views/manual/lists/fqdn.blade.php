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
                        <button class="btn btn-primary font-weight-bolder" data-toggle="modal" data-target="#modal_import">
                            <i class="fa fa-upload mr-1"></i>Import
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <b>Add to FQDNs list</b>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="value">Value</label>
                            <div class="input-group">
                                <input class="form-control" id="value" name="value" placeholder="Ex. xyz.com">
                            </div>
                        </div>
                        <div class="form-group col-md-5">
                            <label for="comment">Comment</label>
                            <div class="input-group">
                                <input class="form-control" id="comment" name="comment" placeholder="">
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
                                <th>Value</th>
                                <th>Comment</th>
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

<div class="modal" id="modal_import" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content bg-light">
            <div class="modal-body" id="modal_import_content">
                <form class="dropzone dropzone-default dropzone-primary dz-clickable mb-2" id="kt_dropzone" action="/dummy/">
                    <div class="dropzone-msg dz-message needsclick">
                        <h3 class="dropzone-msg-title">Upload text file (one FQDN each line)</h3>
                        <span class="dropzone-msg-desc">10 MB max</span>
                    </div>
                </form>
                Import results
                <div class="row mt-2">
                    <div class="col-md-6">
                        <label for="import_successes">Successes:</label>
                        <textarea id="import_successes" class="form-control" readonly rows="3"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="import_errors">Errors:</label>
                        <textarea id="import_errors" class="form-control" readonly rows="3"></textarea>
                    </div>
                </div>
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
            ajax: "/manual/lists/fqdn/get",
            columns: [
                {data: 'fqdn', name: 'fqdn'},
                {data: 'comment', name: 'comment'},
                {data: 'timestamp', name: 'timestamp'},
                {
                    defaultContent: '<button class="btn btn-danger btn-icon btn-xs del"><i class="fas fa-trash"></i></button>',
                    className: 'text-center'
                }
            ],
            language: {
                "processing": "<i class=\"fas fa-2x fa-spin fa-spinner\"></i>"
            },
            "order": [[ 2, 'desc' ]],
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
                    if(confirm('Do you want to delete this item from the list?')){
                        var row = $(this).closest('tr');
                        var data = table.row(row).data();
                        $.ajax({
                            type: "GET",
                            url: '/manual/lists/fqdn/delete/'+data.fqdn,
                            statusCode: {
                                200: function(data) {
                                    table.ajax.reload();
                                },
                                404: function(data) {
                                    $('#modal_alert_content').html('<div class="alert alert-custom alert-danger" role="alert"><div class="alert-icon"><i class="flaticon-cancel"></i></div><div class="alert-text">Error: '+data.responseText+'</div></div>')
                                    $('#modal_alert').modal('show');
                                },
                                500: function(data) {
                                    $('#modal_alert_content').html('<div class="alert alert-custom alert-danger" role="alert"><div class="alert-icon"><i class="flaticon-cancel"></i></div><div class="alert-text">Error: '+data.responseText+'</div></div>')
                                    $('#modal_alert').modal('show');
                                }
                            }
                        });
                    }
                });
            }
        });

        $('#add').on('click',function(){
            if($('#value').val() != ''){
                $.ajax({
                    type: "POST",
                    url: '/manual/lists/fqdn/add',
                    data: {
                        value: $('#value').val(),
                        comment: $('#comment').val()
                    },
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    statusCode: {
                        200: function(data) {
                            table.ajax.reload();
                        },
                        404: function(data) {
                            $('#modal_alert_content').html('<div class="alert alert-custom alert-danger" role="alert"><div class="alert-icon"><i class="flaticon-cancel"></i></div><div class="alert-text">Error: '+data.responseText+'</div></div>')
                            $('#modal_alert').modal('show');
                        },
                        500: function(data) {
                            $('#modal_alert_content').html('<div class="alert alert-custom alert-danger" role="alert"><div class="alert-icon"><i class="flaticon-cancel"></i></div><div class="alert-text">Error: '+data.responseText+'</div></div>')
                            $('#modal_alert').modal('show');
                        }
                    }
                });
            }else{
                $('#modal_alert_content').html('<div class="alert alert-custom alert-warning" role="alert"><div class="alert-icon"><i class="flaticon-cancel"></i></div><div class="alert-text">Warning: missing required value</div></div>')
                $('#modal_alert').modal('show');
            }
        });
        setInilizedDropzone(Dropzone.Dropzone.instances[0]);
    });

    function setInilizedDropzone(instance){
        instance.destroy();
        $('#kt_dropzone').dropzone({
            url: "/manual/lists/fqdn/import",
            paramName: "file",
            maxFiles: 1,
            maxFilesize: 10,
            acceptedFiles: "text/plain",
            success: function(file){
                var result = JSON.parse(file.xhr.response);
                var content = '';
                $.each(result.successes, function (indexInArray, valueOfElement) { 
                    content += valueOfElement+'\n';
                });
                $('#import_successes').val(content);
                var content = '';
                $.each(result.errors, function (indexInArray, valueOfElement) { 
                    content += valueOfElement+'\n';
                });
                $('#import_errors').val(content);
                $('#datatable').DataTable().ajax.reload();
                this.removeFile(file);
            }
        });
    }
</script>

@endsection