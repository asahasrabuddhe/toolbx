<div class="top-header">
	<div class="col-sm-6"> 
		<h4 class="content_head">ADMINISTRATOR</h4>
	</div>
	<div class="col-sm-6 profile-image ">
	  	<div class="pull-right">
		  <label class="dropdown-toggle" type="button" data-toggle="dropdown">
		  {{ Session::get('user_data')->admin_name }}&nbsp;<img src="http://toolbx.applabb.ca/assets/images/profile-avatar.png" class="img-circle defaultprofile"></label>
		  <ul class="dropdown-menu popupBasic">
				<li><a href="{{ url('admin/account') }}">ACCOUNT</a></li>
				<li style="border:solid 1px #eee"></li>
				<li><a href="{{ url('admin/change_password') }}">CHANGE PASSWORD</a></li>
				<li style="border:solid 1px #eee"></li>
				<li><a href="{{ url('admin/logout') }}">LOGOUT</a></li>
		  </ul>
	  </div> 
	</div>
	<div class="clearfix"></div>
</div>