@extends('includes.layouts.main', ['bodyClass' => 'bodybg'])
@section('title', 'Login - ToolBX')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <form method="post" id="login-form" novalidate="novalidate">
                {{ csrf_field() }}
                <div class="row loginscreen">
                    <div class="text-center">
                        <img src="{{ asset('images/toolboxlogo_temp.png') }}" class="img-responsive" style="margin:auto;">
                    </div>
                    <div class=" lbladminlogin"> SUPER ADMIN LOGIN
                    </div> 
                    <div id="error">
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control valid" name="email" id="email" placeholder="Email address" required="" autocomplete="off" style="background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGP6zwAAAgcBApocMXEAAAAASUVORK5CYII=&quot;); cursor: auto;">
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control valid" name="password" id="password" placeholder="Password" required="" autocomplete="off" style="background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGP6zwAAAgcBApocMXEAAAAASUVORK5CYII=&quot;); cursor: auto;">
                    </div>
                    <div class="form-group">
                        <button class="form-control btn-default btnlogin" id="login">LOGIN</button>
                    </div>
                    <div class=" form-group loginlblforgotpassword">
                        <a href="{{ url('forgot_password') }}">FORGOT PASSWORD?</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            /* validation */
            $("#login-form").validate({
                rules: {
                    password: {
                        required: true,
                    },
                    email: {
                        required: true,
                        email: true
                    },
                },
                messages:
                {
                    password:{
                        required: "Please enter your password"
                    },
                    email: "Please enter valid email address",
                },
                submitHandler: submitForm 
            });  
            /* validation */
    
            /* login submit */
            function submitForm()
            {
                $.ajaxSetup({
                    headers: {
                        'Access-Control-Allow-Origin': '*',
                        'Access-Control-Allow-Methods': 'GET, POST, PUT, DELETE',
                        'Access-Control-Allow-Headers': 'Authorization',
                        'Access-Control-Allow-Credentials': 'true'
                    }
                });

                $.ajax({
                    type : 'POST',
                    url  : '/login',
                    data : $("#login-form").serialize(),
                    beforeSend: function() { 
                        $("#error").fadeOut();
                        $("#btn-login").html('<span class="glyphicon glyphicon-transfer"></span> &nbsp; sending ...');
                    },
                    success: function(response) {    
                        if(response=="ok") {
                            $("#btn-login").html('<img src="btn-ajax-loader.gif" /> &nbsp; Signing In ...');
                            window.location.href = "/admin/user/list_users";
                        } else {
                            $("#error").fadeIn(1000, function() {      
                                $("#error").html('<div class="alert alert-danger"> '+response+' !</div>');
                                $("#btn-login").html('<span class="glyphicon glyphicon-log-in"></span> &nbsp; Sign In');
                            });
                        }
                    }
                });
                return false;
            }
            /* login submit */
        });
    </script>
@endsection