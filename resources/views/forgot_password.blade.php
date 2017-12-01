@extends('includes.layouts.main', ['bodyClass' => 'bodybg', 'index' => 'true'])
@section('title', 'Forgot Password - ToolBX')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <form method="post" id="login-form" novalidate="novalidate">
            	{{ csrf_field() }}
                <div class="row forgotpasswordscreen">
					<div class="text-center"> 
					    <img src="{{ asset('images/toolboxlogo_temp.png') }}" class="img-responsive" style="margin:auto;">
					</div>
					<div class="forgotpass_lblforgotpassword"> FORGOT PASSWORD</div>
					@if(Session::get('success_msg'))
	                    <div class="alert alert-success">{{ Session::get('success_msg') }}</div>
	                @elseif(Session::get('error_msg'))
	                    <div class="alert alert-danger">{{ Session::get('error_msg') }}</div>
	                @endif
					<form name="forgot_password" id="forgot_password" class="form-horizontal" method="post" novalidate="novalidate" _lpchecked="1">
                        <div class="form-group">
                            <input type="email" class="form-control" name="email" id="email" placeholder="ENTER YOUR EMAIL ADDRESS" required="" title="please enter valid email address">
                        </div>
                        <div class="form-group">
                            <button type="submit" name="submit" class="form-control btn-default btncontinue" id="continue">SEND</button>
                        </div>
                        <div class="form-group forgotpass_lblbacktologin">
                            <a style="color: #000;text-decoration:underline;text-align: center" href="{{ url('login') }}">BACK TO LOGIN</a>
                        </div>
					</form>
				</div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script language="javascript">
	var ht = $( window ).height() - 40; 
	$("#ft").css("top",ht.toString() + "px");

	$("#forgot_password").validate({
		rules:
		{
			email: {
				required: true,
				email: true
			},
		},
		messages:
		{
			required : "Please enter email address",
			email: "Please enter valid email address",
		},
	});
</script>
@endsection