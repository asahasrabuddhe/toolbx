<div class="sidebar-nav" style="height:100%">
    <div class="navbar navbar-default sidenav-bg" role="navigation" >
        <div class="navbar-header set-nav">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span> 
                <span class="icon-bar"></span>
            </button>
            <!-- <span class="visible-xs navbar-brand">Sidebar menu</span> -->
        </div>
        <div class="navbar-collapse collapse sidebar-navbar-collapse">
        <img src="{{ asset('images/toolboxlogo_temp.png') }}" class="img-responsive" style="margin:auto; width: 60% !important; margin-top: 20%; margin-bottom: 15%;"> 
            <ul class="sidenav-menu set-sidenav">
                <li style="text-align:center; padding-left: 10px;" class="{{ (request()->is('admin/user/*') == true) ? 'active' : '' }}"><a href="{{ url('admin/user/list_users') }}">RUNNERS</a></li>
                <li style="text-align:center; padding-left: 10px;" class="{{ (request()->is('admin/company/*') == true) ? 'active' : '' }}"><a href="{{ url('admin/company/list_companies') }}">COMPANY</a></li>
                <li style="text-align:center; padding-left: 10px;" class="{{ (request()->is('admin/product/*') == true) ? 'active' : '' }}"><a href="{{ url('admin/product/list_products') }}">PRODUCTS</a></li> 
                <li style="text-align:center; padding-left: 10px;" class="{{ (request()->is('admin/order/*') == true) ? 'active' : '' }}"><a href="{{ url('admin/order/list_orders') }}">ORDERS</a></li>
            </ul>
        </div>
    </div>
</div>