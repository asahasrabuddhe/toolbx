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
                    <h4><img onclick="window.location.assign('{{ url('/admin/company/list_companies') }}')" src="{{ asset('/images/arrow_16.png') }}" style="cursor: pointer;"> <label>VIEW USER</label></h4>
                    <form class="top-form" id="top_form1" action="{{ url('/company/' . Request::route('id') . '/update') }}" method="post">
                        {{ csrf_field() }}
                        <div class="col-sm-9" style="background-color:#ffffff;">
                            <div class="row">
                                <div class="col-sm-4 labelalign">
                                    <label>ENTER COMPANY NAME</label>
                                </div>
                                <div class="form-group col-sm-5">
                                    <input class="form-control" placeholder="" required="" style="background:#FFFFFF" name="company" type="text" value="{{ $company_info->CompanyName or '' }}">
                                </div>
                            </div>
                            <div class="col-sm-offset-4 col-sm-8">
                                <div class="col-sm-6">
                                    <button style="margin-left: -15px;width: 135px;" class="form-control btn-default btn-gray" onclick="window.location.assign('{{ url('/admin/company/list_companies') }}')" type="reset">Cancel</button>
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