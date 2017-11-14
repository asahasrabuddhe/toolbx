@extends('includes.layouts.main')
@section('title', 'Invite Runner - ToolBX Admin')
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
    				<h4><label> INVITE RUNNER </label></h4>
					<div class="top-form" id="top_form2">
						<form action="{{ url('admin/user/invite') }}" method="post" id="form_inv" novalidate="novalidate">
							{{ csrf_field() }}
							<div class="col-sm-9" style="background-color:#ffffff;">
							<div class="row">
				    			<div class="col-sm-5 labelalign">
				    	    		<label>ENTER RUNNER NAME</label>
				    			</div>
								<div class="form-group col-sm-7">
									<input type="text" name="name" class="form-control" value="" placeholder="">
								</div>
							</div>
							<div class="row">
					    		<div class="col-sm-5 labelalign">
							        <label>ENTER PHONE NUMBER</label>
						        </div>
								<div class="form-group col-sm-7">
									<input type="text" name="phonenumber" class="form-control" value="" placeholder="">
								</div>
							</div>
							<div class="row">
							    <div class="col-sm-5 labelalign">
							        <label>ENTER EMAIL</label>
							    </div>
								<div class="form-group col-sm-7">
									<input type="text" name="email" class="form-control" value="" placeholder="">
								</div>
							</div>
							<div class="col-sm-offset-5 col-sm-7">
								<div class="col-sm-6">
								    <button style="margin-left: -15px;width: 135px;" class="form-control btn-default btn-gray" onclick="window.location.assign('{{ url('/admin/user/list_users') }}')" type="reset">CANCEL</button>
								</div>
								<div class="col-sm-6">
								    <button style="margin-left: -15px;width: 135px;" class="form-control btn-default btn-blue common" name="submit" type="submit">INVITE</button>
								</div>
							</div>
						</div>
					</form>
					<div class="clearfix"></div>
				</div>        
         		<div class="modal fade" id="getCodeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		   			<div class="modal-dialog modal-sm">
		      			<div class="modal-content">
		       				<div class="modal-header">
		         				<h4 class="modal-title" id="myModalLabel"> ToolBX </h4>
		       				</div>
		       				<div class="modal-body" id="getCode" style=""></div>
		       				<div class="modal-footer">
			       				<div class="col-sm-offset-6 col-sm-6" style="height:100%;margin-top:-21px">
		              				<a href="{{ url('/admin/user/list_users') }}" id="button123" class="btn btn-info" role="button" style="width: 100%;background-color: transparent; border: medium; color:#333;text-align: right;">OK</a>                                                  
		            			</div>
	            				</div>
		    				</div>
		   				</div>
		 			</div>
            	</div>
   			</div>
   		</div>
	</div>
</div>
@endsection
@section('scripts')
			<script type="text/javascript">

		    $('document').ready(function()
			{
			   /* validation */
			   	jQuery.validator.addMethod("lettersonly", function(value, element)
				{
					return this.optional(element) || /^[a-z," "]+$/i.test(value);
				}, "Letters and spaces only please");
				jQuery.validator.methods.email = function( value, element ) {
				  return this.optional( element ) || /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test( value );
				}
			  	$("#form_inv").validate({
			    	rules:
				   	{
					   name:
					   {
						   required: true,
						   lettersonly:true
					   },
					   phonenumber:
					   {
						   required: true,
						   digits: true,
						   maxlength: 10,
						   minlength: 10
					   },
					   email:
					   {
				            required: true,
				            email: true
			            },
				    },
			       messages:
				    {
			            name:
			            {
			          		required: "Please enter your name",
			          		/*maxlength:jQuery.format("Name too long more than (30) characters")*/
			         	},
			         	phonenumber:
			         	{
			          		required: "Please enter phone number"
			         	},
			            email: "Please enter valid email address",
			       },
		    	 submitHandler: submitRunmerForm
		       });
			    /* validation */
			    function submitRunmerForm()
			    {
				   var data = $("#form_inv").serialize();
				   $.ajax({
					   type : 'POST',
					   data : data,
					   url  : '{{ url('admin/user/invite') }}',
					   beforeSend: function()
					   {
						    $("#error").fadeOut();
						    $("#btn-login").html('<span class="glyphicon glyphicon-transfer"></span> &nbsp; sending ...');
					   },
    				   success : function(data)
    			       { 	
    				     	if(data)
    				     	{	
    				     	    $("#getCode").html(data);
	    						$("#getCodeModal").modal('show');
    				     	    $("#lblMessage").text(data);
    				     	    $( "#dialog" ).dialog({
                                  	modal: true,
                                  	buttons: {
                                    Ok: function() {
                                    	$( this ).dialog( "close" );
                                    		window.location.href = "{{ url('/admin/user/list_users') }}";
                                    	}
                                  	}
                                });
    				     	}
    				     	else
    				     	{
    				     		var data = "Invitation sent successfully to runner";
					     		$("#getCode").html(data);
	    						$("#getCodeModal").modal('show');
    						    $("#error").fadeIn(1000, function()
    						    {
    						    	window.location.href = "{{ url('/admin/user/invite') }}";
    				         	});
    			     		}
    			     	}
    			   });
    			   return false;
			  }
		  	});
		</script>
@endsection