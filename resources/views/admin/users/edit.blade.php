@extends('includes.layouts.main')
@section('title', 'View Runner - ToolBX Admin')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/jquery.dataTables.min.css') }}" type="text/css">
@endsection
@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-3 npr">
            @include('includes.sidenav')
        </div>
        <div class="col-sm-9 npl">
            @include('includes.header')
            <div class="clearfix"></div>
            <div class="content" id="myDiv">
                <div class="data-table table-responsive">
                    @if(Request::is('admin/user/*'))
                        <h4><img onclick="window.location.assign('{{ url('/admin/user/list_users') }}')" src="{{ asset('/images/arrow_16.png') }}" style="cursor: pointer;"> <label>EDIT USER</label></h4>
                        <form class="top-form" id="top_form1" action="{{ url('/runners/' . Request::route('id') . '/update') }}" method="post">
                    @elseif(Request::is('admin/employee/*'))
                        <h4><img onclick="window.location.assign('{{ url('/admin/user/list_users') }}')" src="{{ asset('/images/arrow_16.png') }}" style="cursor: pointer;"> <label>EDIT EMPLOYEE</label></h4>
                        <form class="top-form" id="top_form1" action="{{ url('/employee/' . Request::route('id') . '/update') }}" method="post">
                    @elseif(Request::is('admin/owner/*'))
                        <h4><img onclick="window.location.assign('{{ url('/admin/user/list_users') }}')" src="{{ asset('/images/arrow_16.png') }}" style="cursor: pointer;"> <label>EDIT OWNER</label></h4>
                        <form class="top-form" id="top_form1" action="{{ url('/owner/' . Request::route('id') . '/update') }}" method="post">
                    @endif
                        {{ csrf_field() }}
                        <div class="col-sm-9" style="background-color:#ffffff;">
                            <div class="row">
                                <div class="col-sm-4 labelalign">
                                    <label>NAME</label>
                                </div>
                                <div class="form-group col-sm-5">
                                    <input class="form-control" placeholder="" required="" style="background:#FFFFFF" name="name" type="text" value="{{ $user_info->RegistrationName or '' }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4 labelalign">
                                    <label>PHONE NUMBER</label>
                                </div>
                                <div class="form-group col-sm-5">
                                    <input class="form-control" placeholder="" required="" style="background:#FFFFFF" name="phoneno" type="text" value="{{ $user_info->RegistrationPhoneNo or '' }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4 labelalign">
                                    <label>EMAIL</label>
                                </div>
                                <div class="form-group col-sm-5">
                                    <input class="form-control" placeholder="" readonly disabled required="" style="background:#FFFFFF" type="text" value="{{ $user_info->RegistrationEmail or '' }}">
                                </div>
                            </div>
                            @if(Request::is('admin/employee/*') || Request::is('admin/owner/*'))
                                 <div class="row">
                                    <div class="col-sm-4 labelalign">
                                        <label>ENTER COMPANY</label>
                                    </div>
                                    <div class="form-group col-sm-5">
                                        @if(Request::is('admin/employee/*'))
                                            <input class="form-control" placeholder="" readonly disabled required="" style="background:#FFFFFF" type="text" name="company" value="{{ $user_info->CompanyName or '' }}">
                                            <input type="hidden" name="type" value="employee">
                                        @elseif(Request::is('admin/owner/*'))
                                            <input class="form-control" placeholder="" required="" style="background:#FFFFFF" type="text" name="company" value="{{ $user_info->CompanyName or '' }}">
                                            <input type="hidden" name="type" value="owner">
                                        @endif
                                    </div>
                                </div>
                            @endif
                            <div class="col-sm-offset-4 col-sm-8">
                                <div class="col-sm-6">
                                    <button style="margin-left: -15px;width: 135px;" class="form-control btn-default btn-gray" onclick="window.location.assign('{{ url('/admin/user/list_users') }}')" type="reset">Cancel</button>
                                </div>   
                                <div class="col-sm-6">
                                    <button style="margin-left: -15px;width: 135px;" class="form-control btn-default btn-blue" name="submit" type="submit">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
    <script>
        // Javascript to enable link to tab
        var url = document.location.toString();
        if (url.match('#')) {
            $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
        } 

        // Change hash for page-reload
        $('.nav-tabs a').on('shown.bs.tab', function (e) {
            window.location.hash = e.target.hash;
        });
    </script>
@endsection