@extends('includes.layouts.main')
@if(Request::is('admin/owner/invite'))
	@section('title', 'Invite Owner - ToolBX Admin')
@elseif(Request::is('admin/employee/invite'))
	@section('title', 'Invite Employee - ToolBX Admin')
@endif
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/jquery.dataTables.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" type="text/css">
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
            		@if(Request::is('admin/owner/invite'))
    					<h4><label> INVITE OWNER </label></h4>
    				@elseif(Request::is('admin/employee/invite'))
    					<h4><label> INVITE EMPLOPYEE </label></h4>
    				@endif
					<div class="top-form" id="top_form2">
						@if(Request::is('admin/owner/invite'))
	    					<form action="{{ url('admin/owner/invite') }}" method="post" id="form_inv" novalidate="novalidate">
	    				@elseif(Request::is('admin/employee/invite'))
	    					<form action="{{ url('admin/employee/invite') }}" method="post" id="form_inv" novalidate="novalidate">
	    					<input type="hidden" name="type" value="employee">
	    				@endif
							{{ csrf_field() }}
							<div class="col-sm-9" style="background-color:#ffffff;">
							<div class="row">
				    			<div class="col-sm-5 labelalign">
				    				@if(Request::is('admin/owner/invite'))
				    					<label>ENTER OWNER NAME</label>
				    				@elseif(Request::is('admin/employee/invite'))
				    					<label>ENTER EMPLOYEE NAME</label>
				    				@endif
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
							<div class="row">
							    <div class="col-sm-5 labelalign">
							        <label>ENTER COMPANY</label>
							    </div>
								<div class="form-group col-sm-7">
									@if(Request::is('admin/owner/invite'))
				    					<input type="text" name="company" class="form-control" value="" placeholder="">
				    				@elseif(Request::is('admin/employee/invite'))
				    					<select name="company" class="form-control" id="companyId"></select>
				    				@endif
								</div>
							</div>
							<div class="col-sm-offset-5 col-sm-7">
								<div class="col-sm-6">
								    <button style="margin-left: -15px;width: 135px;" class="form-control btn-default btn-gray" onclick="window.location.assign('{{ url('/admin/company/list_companies') }}')" type="reset">CANCEL</button>
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
		              				<a href="{{ url('/admin/company/list_companies') }}" id="button123" class="btn btn-info" role="button" style="width: 100%;background-color: transparent; border: medium; color:#333;text-align: right;">OK</a>                                                  
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
@section('scripts-top')
	<script type="text/javascript" src="{{ asset('js/select2.full.min.js') }}"></script>
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
			            company:
					   {
						   required: true,
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
					   @if(Request::is('admin/owner/invite'))
					   	url  : '{{ url('admin/owner/invite') }}',
					   @elseif(Request::is('admin/employee/invite'))
					   	url  : '{{ url('admin/employee/invite') }}',
					   @endif
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
	    						var data = "Invitation sent successfully to owner";
    				     	    $("#lblMessage").text(data);
    				     	    $( "#dialog" ).dialog({
                                  	modal: true,
                                  	buttons: {
                                    Ok: function() {
                                    	$( this ).dialog( "close" );
                                    		window.location.href = "{{ url('/admin/company/list_companies') }}";
                                    	}
                                  	}
                                });
    				     	}
    				     	else
    				     	{
    				     		var data = "Invitation sent successfully to owner";
					     		$("#getCode").html(data);
	    						$("#getCodeModal").modal('show');
    						    $("#error").fadeIn(1000, function()
    						    {
    						    	window.location.href = "{{ url('/admin/owner/invite') }}";
    				         	});
    			     		}
    			     	}
    			   });
    			   return false;
			  }
			@if(Request::is('admin/employee/invite'))
				$('#companyId').select2({
	                placeholder: 'Select Company',
	                ajax: {
	                    url: '{{ url('/companies') }}',
	                    processResults: function (data) {
				            return {
				                results: $.map(data.data, function (item) {
				                    return {
				                        text: item.CompanyName,
				                        id: item.CompanyId
				                    }
				                })
				            };
				        }
				    }
	            });
			@endif
		  	});
		</script>
@endsection