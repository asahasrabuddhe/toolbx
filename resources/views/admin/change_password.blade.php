@extends('includes.layouts.main', ['bodyClass' => 'bodybg', 'index' => 'true'])
@section('title', 'Change Password - ToolBX Admin')
@section('styles')
<style>
	input[type="email"], input[type="password"] {
		margin-top: 0px !important;
	}
</style>
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
                    <h4><img onclick="window.location.assign('{{ url('/admin/user/list_users') }}')" src="{{ asset('/images/arrow_16.png') }}" style="cursor: pointer;"> <label>HOME</label></h4>
                    <form class="top-form" id="top_form1" action="{{ url('/admin/change_password') }}" method="POST">
                    	{{ csrf_field() }}
                        <div class="col-sm-9" style="background-color:#ffffff;">
                        	<div class="row">
                    			 <div class="col-sm-4 labelalign">
                                    <br/><br/><h4>PASSWORD RESET</h4><br/><br/>
                                </div>
                    		</div>
                            <div class="row">
                                <div class="col-sm-4 labelalign">
                                    <label>CURRENT PASSWORD</label>
                                </div>
                                <div class="form-group col-sm-8">
                                    <input class="form-control" placeholder="" required="" name="current_password" type="password" value="">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4 labelalign">
                                    <label>NEW PASSWORD</label>
                                </div>
                                <div class="form-group col-sm-8">
                                    <input class="form-control" placeholder="" required="" name="new_password" type="password" value="">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4 labelalign">
                                    <label>CONFIRM PASSWORD</label>
                                </div>
                                <div class="form-group col-sm-8">
                                    <input class="form-control" placeholder="" required="" name="confirm_password" type="password" value="">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-offset-4 col-sm-4 form-group">
                                    <button style="width: 160px;" class="form-control btn-default btn-gray" onclick="window.location.assign('{{ url('/admin/user/list_users') }}')" type="reset">CANCEL</button>
                                </div>   
                                <div class="col-sm-4 form-group">
                                    <button style="width: 160px;" class="form-control btn-default btn-blue" name="submit" type="submit">SAVE</button>
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