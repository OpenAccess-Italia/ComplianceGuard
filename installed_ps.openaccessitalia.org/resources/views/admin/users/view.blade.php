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
                    <h5 class="text-dark font-weight-bold my-1 mr-5">Edit user {{$user->friendly_name}} (Username: {{$user->name}})</h5>
                    <!--end::Page Title-->
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item text-muted">
                            <a href="/admin/users/list" class="text-muted">Users list</a>
                        </li>
                        <li class="breadcrumb-item text-muted">
                            <a href="/admin/users/new" class="text-muted">New user</a>
                        </li>
                    </ul>
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
                        <h3 class="card-label">Edit user</h3>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" id="form" action="/admin/users/save"  onsubmit="event.preventDefault();beforesubmit()">
                        @csrf
                        <input name="user_id" value="{{$user->id}}" type="hidden">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="name">Username</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="name" id="name" autocomplete="none" value="{{$user->name}}" placeholder="Username" readonly>
                                            <div class="input-group-append"><span class="input-group-text"><i class="fas fa-user text-dark"></i></span></div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="email">Email</label>
                                        <div class="input-group">
                                            <input type="email" class="form-control" name="email" id="email" autocomplete="none" value="{{$user->email}}" placeholder="Email" readonly>
                                            <div class="input-group-append"><span class="input-group-text"><i class="fas fa-at text-dark"></i></span></div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <h3 class="font-size-lg text-dark font-weight-bold mb-6">Policies</h3>
                                        <div class="form-row mt-3">
                                            <div class="col-md-2 text-center">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="piracy" name="piracy" @if($user->piracy) checked @endif>
                                                    <label class="form-check-label" for="piracy">
                                                        Piracy Shield<i class="fas fa-lg fa-shield-alt text-dark ml-2"></i>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="cncpo" name="cncpo" @if($user->cncpo) checked @endif>
                                                    <label class="form-check-label" for="cncpo">
                                                        CNCPO<i class="fas fa-lg fa-child text-dark ml-2"></i>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="adm" name="adm" @if($user->adm) checked @endif>
                                                    <label class="form-check-label" for="adm">
                                                        ADM<i class="fas fa-lg fa-user-secret text-dark ml-2"></i>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="manual" name="manual" @if($user->manual) checked @endif>
                                                    <label class="form-check-label" for="manual">
                                                        Manual<i class="fas fa-lg fa-hand-scissors text-dark ml-2"></i>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="admin" name="admin" @if($user->admin) checked @endif>
                                                    <label class="form-check-label" for="admin">
                                                        Admin<i class="fas fa-lg fa-key text-dark ml-2"></i>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="enabled" name="enabled" @if($user->enabled) checked @endif>
                                                    <label class="form-check-label" for="enabled">
                                                        Enabled<i class="fas fa-lg fa-thumbs-up text-dark ml-2"></i>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="form-group">
                                    <label for="friendly_name">Name</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="friendly_name" id="friendly_name" autocomplete="none" value="{{$user->friendly_name}}" placeholder="Name">
                                        <div class="input-group-append"><span class="input-group-text"><i class="fas fa-tag text-dark"></i></span></div>
                                    </div>
                                </div>
                                Avatar<br><small>We recommend uploading a PNG image with a transparent background (maximum size 100 KB)</small>
                                <div class="image-input image-input-outline text-center w-100" id="avatar_container">
                                    @if($user->avatar)
                                        <div class="image-input-wrapper w-100" style="height: 276px;background-color: lightgrey;background-image:url({{$user->avatar}})" id="avatar_preview"></div>
                                    @else
                                        <div class="image-input-wrapper w-100" style="height: 276px;background-color: lightgrey" id="avatar_preview"></div>
                                    @endif
                                    <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Carica">
                                        <i class="fa fa-pen icon-sm text-muted"></i>
                                        <input type="file" name="avatar" accept=".png, .jpg, .jpeg"/>
                                        <input type="hidden" name="avatar_remove"/>
                                    </label>
                                    <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="cancel" data-toggle="tooltip" title="Elimina">
                                        <i class="ki ki-bold-close icon-xs text-muted"></i>
                                    </span>
                                </div>
                                <div class="form-check mt-1">
                                    <input class="form-check-input" type="checkbox" name="delete_avatar" id="delete_avatar">
                                    <label class="form-check-label" for="delete_avatar">
                                        Remove avatar<i class="fas fa-lg fa-times text-dark ml-2"></i>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Save</button>
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

<div class="modal" id="modal_validation" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body" id="modal_validation_content">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="modal_progress" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body" id="modal_progress_content"></div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        var brand_logo = new KTImageInput('avatar_container');
        setInterval(() => {
            var image = $('#avatar_preview').css('background-image').replace('url("','').substring(0, $('#avatar_preview').css('background-image').replace('url("','').length - 1);
            if(image != 'non'){
                $('#avatar_preview').html('<img id="tmp_img" style="width:100%;visibility:hidden" src="'+image+'">').css('height',$('#tmp_img').css('height')).css('background-color','white');
            }else{
                $('#avatar_preview').html('').css('height','276px').css('background-color','lightgrey');
            }
        }, 200);
    });

    function beforesubmit(){
        var validation = [];
        var velement = [];
        if($('#friendly_name').val() == ""){
            validation.push('Name is not filled');
            velement.push($('friendly_name'));
        }
        if(validation.length > 0){
            velement.forEach(element => {
                element.addClass("is-invalid");
            });
            $('.is-invalid').on('change',function(){$(this).removeClass('is-invalid')});
            var alert_content = '';
            validation.forEach(alert => {
                alert_content += '<div class="alert alert-danger" role="alert">'+alert+'</div>'
            });
            $('#modal_validation_content').html(alert_content);
            $('#modal_validation').modal("show");
        }else{
            $('#modal_progress_content').html('<div class="progress"><div class="progress-bar progress-bar-striped progress-bar-animated " role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div></div>');
            $('#modal_progress').modal("show");
            $.ajax({
                type: "POST",
                url: "/admin/users/save",
                data: new FormData($("#form")[0]),
                dataType: "json",
                processData: false, 
                contentType: false,
                success: function(data) {
                    if(data.status == "OK"){
                        window.location.href = '/admin/users/view/'+data.id;
                    }else{
                        if(data.id){
                            window.location.href = '/admin/users/view/'+data.id;
                        }else{
                            $('#modal_progress').modal("hide");
                            var alert_content = '';
                            data.errors.forEach(alert => {
                                alert_content += '<div class="alert alert-danger" role="alert">'+alert+'</div>'
                            });
                            $('#modal_validation_content').html(alert_content);
                            $('#modal_validation').modal("show");
                        }
                    }
                }
            });
        }    
    }

</script>

@endsection