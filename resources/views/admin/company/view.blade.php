@extends('includes.layouts.main')
@section('title', 'View Company - ToolBX Admin')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/jquery.dataTables.min.css') }}" type="text/css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" type="text/css">
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
                    <div class="row">
                        <div class="col-sm-4">
                            <h4><img onclick="window.location.assign('{{ url('/admin/company/list_companies') }}')" src="{{ asset('/images/arrow_16.png') }}" style="cursor: pointer;"> <label>VIEW COMPANY</label></h4>
                        </div>
                        <div class="col-sm-6 date" style="text-align: right;background:#EEEEEE;padding: 5px;">
                            <div class="col-sm-6 npl">
                                <div class="input-group from">
                                    <input class="form-control" id="from_date_p" name="fromDate" size="30" type="text" value="{{ date('M d, Y', strtotime('-7 days')) }}"><span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                </div>
                            </div>
                            <div class="col-sm-6 npr">
                                <div class="input-group to">
                                    <input class="form-control" id="to_date_p" name="toDate" size="30" type="text" value="{{ date('M d, Y') }}"><span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2 export">
                            <a href="" id="export" class="form-control">EXPORT</a>
                        </div>
                    </div>
                    <div class="clearfix" style="padding-bottom: 15px"></div>
                    <div class="row">
                        <div class="col-sm-8">
                            <ul class="nav nav-tabs" id="myTab">
                                <li class="active"><a data-toggle="tab" href="#orders">ORDERS</a></li>
                                <li><a data-toggle="tab" href="#employees">EMPLOYEES</a></li>
                                <li><a data-toggle="tab" href="#owner">OWNER</a></li>
                                <li><a data-toggle="tab" href="#payment">PAYMENT</a></li>
                            </ul>
                        </div>
                        <div class="col-sm-offset-2 col-sm-2 selectall">
                            <div class="form-group">
                                <label>SELECT ALL</label>
                                <input type="checkbox" id="checkall">
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="tab-content">
                        <div id="orders" class="tab-pane fade in active">
                            <div class="row">&nbsp;</div>
                            <table class="table" id="tblOrders" style="width: 100% !important">
                                <thead>
                                    <tr>
                                        <th class="clsleftheader">DATE PURCHASED</th>
                                        <th class="clsheader">ORDER NO. - JOB SITE NAME</th>
                                        <th class="clsheader">TOTAL AMOUNT IN $</th>
                                        <th class="clsheader">ORDER DETAILS</th>
                                        <th class="clsheader">VIEW</th>
                                        <th class="clsrightheader">SELECT</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div id="employees" class="tab-pane fade">
                            <div class="row">
                                <div class="col-sm-offset-9 col-sm-3 pull-right" style="text-align: right;margin-bottom: 10px;">
                                    <a href="{{ url('admin/employee/invite') }}" class="pull-right" style="text-decoration: none;margin-right: -6%; color: #000"> + INVITE EMPLOYEE </a>
                                </div>
                            </div>
                            <table class="table" id="tblEmployees" style="width: 100% !important">
                                <thead>
                                    <tr>
                                        <th class="clsleftheader">NAME</th>
                                        <th class="clsheader">PHONE NUMBER</th>
                                        <th class="clsheader">EMAIL</th>
                                        <th class="clsheader">EDIT</th>
                                        <th class="clsrightheader">DELETE</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div id="owner" class="tab-pane fade">
                            <div class="row">
                                <div class="col-sm-offset-9 col-sm-3 pull-right" style="text-align: right;margin-bottom: 10px;">
                                    <a href="{{ url('admin/owner/invite') }}" class="pull-right" style="text-decoration: none;margin-right: -6%; color: #000"> + INVITE OWNER </a>
                                </div>
                            </div>
                            <table class="table" id="tblOwner" style="width: 100% !important">
                                <thead>
                                    <tr>
                                        <th class="clsleftheader">NAME</th>
                                        <th class="clsheader">PHONE NUMBER</th>
                                        <th class="clsheader">EMAIL</th>
                                        <th class="clsheader">EDIT</th>
                                        <th class="clsrightheader">DELETE</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div id="payment" class="tab-pane fade">
                            <div class="row">
                                <div class="col-md-3">

                                </div>
                                <div class="col-md-3">

                                </div>
                                <div class="col-md-3">
                                    <!-- EXPORT -->
                                </div>
                                <div class="col-md-3">
                                    LOGIN TO STRIPE
                                </div>
                            </div>
                            <table class="table" id="tblPayment" style="width: 100% !important">
                                <thead>
                                    <tr>
                                        <th class="clsleftheader">DATE</th>
                                        <th class="clsheader">ORDER NO.</th>
                                        <th class="clsheader">PAYMENT CONFIRMATION NO.</th>
                                        <th class="clsrightheader">TOTAL AMOUNT IN $</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- Modal -->
                <div class="modal" id="myModal-employee" role="dialog" title="Delete Message">
                    Do you want to delete this Employee?
                </div>
                <!-- Modal -->
                <div class="modal" id="myModal-owner" role="dialog" title="Delete Message">
                    Do you want to delete this Owner?
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts-top')
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script type="text/javascript" src="{{ asset('js/download.js') }}"></script>
@endsection
@section('scripts')
    <script>
        $(document).ready(function(){
            var tblOrders = $('#tblOrders').DataTable({
                'ordering': false,
                'searching': false,
                'processing': true,
                'serverSide': true,
                'autoWidth': true,
                'lengthChange': false,
                'pageLength': 5,
                'stateSave': true,
                'stateDuration': -1,
                'stateSaveParams': function (settings, data) {
                    data.search.search = '';
                },
                'ajax': {
                    url: '{{ url('/company/' . Request::route('id') . '/orders') }}',
                    type: 'get',
                    data: function(d) {
                        d.fromDate = $('#from_date').val(),
                        d.toDate = $('#to_date').val()
                    }
                },
                'columns': [
                    {
                        'data': 'OrderDate',
                        'render': function( data, type, row, meta ) {
                            var date = new Date(data);
                            return date.toLocaleDateString('en-CA');
                        }
                    },
                    {
                        'data': 'OrderId',
                        'render': function( data, type, row, meta ) {
                            return data + ' - ' + row['JobSiteName'];
                        }
                    },
                    {'data': 'TotalAmount'},
                    {
                        'data': 'ProductName',
                        'render': function( data, type, row, meta ) {
                            var col = '<div style="height: 75px; overflow-y: scroll;"><ul>';
                            $.each(data.split(','), function(i, v){
                                col += '<li>' + v + '</li>';
                            });
                            col += '</ul></div>'
                            return col;
                        }
                    },
                    {
                        'data': 'OrderId',
                        'render': function( data, type, row, meta ) {
                            return '<a href="{{ url('admin/order') }}/' + data + '/view' + '" ><img src="{{ asset('images/view.png') }}" width="20" height="17"></a>'
                        }
                    },
                    {
                        'data': 'OrderId',
                        'render': function( data, type, row, meta ) {
                            return '<input type="checkbox" id="' + data + '">';
                        }
                    }
                ]
            });
            $('#from_date').datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'M d, yy',
                maxDate: new Date,
                onSelect: function(selectedDate){
                    $('#to_date').datepicker('option', 'minDate', selectedDate);
                    tblOrders.draw();
                    tblPayment.draw();
                }
            });
            $('#to_date').datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'M d, yy',
                onSelect: function() {
                    tblOrders.draw();
                    tblPayment.draw();
                }
            });
            $('#from_date_p').datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'M d, yy',
                maxDate: new Date,
                onSelect: function(selectedDate){
                    $('#to_date_p').datepicker('option', 'minDate', selectedDate);
                    tblOrders.draw();
                    tblPayment.draw();
                }
            });
            $('#to_date_p').datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'M d, yy',
                onSelect: function() {
                    tblOrders.draw();
                    tblPayment.draw();
                }
            });
            $('.input-group-addon').on('click', function() {
                $(this).parent().find('input').datepicker().focus();
            });
            $('#checkall').on('click', function(e) {
                $('tbody').find('input').trigger('click');
            });
            $('#export').on('click', function(e) {
                e.preventDefault();
                // ADD LOGIC FOR SELECTED RECORDS
                var ids = [];
                $.each($('tbody input:checked'), function(i, v) { ids.push( $(v).attr('id') ); });

                var f = $('#from_date').val();
                var t = $('#to_date').val();

                var url = "{{ url('admin/orders/export') }}" + "?from_date=" + f + "&to_date=" + t;
                if(ids.length)
                    url += "&ids=" + ids.join(',');
                url += "&company=" + "{{ Request::route('id') }}"
                $.ajax({
                    type: 'GET',
                    url:  url,
                    dataType: 'json',
                    success: function(data) {
                    download(data.data_text, 'export.csv', 'text/csv');
                    },
                    error: function(error) {
                    console.log(error);
                    }
                });
            });
            $('.nav.nav-tabs li a').on('click', function() {
                if( $(this).html() == 'ORDERS' || $(this).html() == 'PAYMENT' ) {
                    $('.col-sm-6.date').show();
                    $('.col-sm-2.export').show();
                    if( $(this).html() == 'ORDERS' )
                        $('.col-sm-2.selectall').show();
                } else {
                    $('.col-sm-6.date').hide();
                    $('.col-sm-2.export').hide();
                    $('.col-sm-2.selectall').hide();
                }
            });
            $('#tblEmployees').DataTable({
                'searching': false,
                'processing': true,
                'serverSide': true,
                'autoWidth': true,
                'stateSave': true,
                'stateDuration': -1,
                'ajax': {
                    url: '{{ url('/company/' . Request::route('id') . '/employees' ) }}',
                    type: 'get',
                },
                'columns': [
                    {'data': 'RegistrationName'},
                    {'data': 'RegistrationPhoneNo'},
                    {'data': 'RegistrationEmail'},
                    {
                        'data': 'RegistrationId',
                        'render': function( data, type, row, meta ) {
                            return '<a href="/admin/employee/' + data + '/edit"><img src="{{ asset('images/edit-icon.png') }}"></a>';
                        }
                    },
                    {
                        'data': 'RegistrationId',
                        'render': function( data, type, row, meta ) {
                            return '<a id="delete-employee" data-val="' + data + '"><img src="{{ asset('images/delete-icon.png') }}"></a>';
                        }
                    },
                ]
            });

            $('#tblOwner').DataTable({
                'searching': false,
                'processing': true,
                'serverSide': true,
                'autoWidth': true,
                'stateSave': true,
                'stateDuration': -1,
                'ajax': {
                    url: '{{ url('/company/' . Request::route('id') .  '/owners') }}',
                    type: 'get',
                },
                'columns': [
                    {'data': 'RegistrationName'},
                    {'data': 'RegistrationPhoneNo'},
                    {'data': 'RegistrationEmail'},
                    {
                        'data': 'RegistrationId',
                        'render': function( data, type, row, meta ) {
                            return '<a href="/admin/owner/' + data + '/edit"><img src="{{ asset('images/edit-icon.png') }}"></a>';
                        }
                    },
                    {
                        'data': 'RegistrationId',
                        'render': function( data, type, row, meta ) {
                            return '<a id="delete-owner" data-val="' + data + '"><img src="{{ asset('images/delete-icon.png') }}"></a>';
                        }
                    },
                ]
            });

            var tblPayment = $('#tblPayment').DataTable({
                'searching': false,
                'processing': true,
                'serverSide': true,
                'stateSave': true,
                'stateDuration': -1,
                'ajax': {
                    url: '{{ url('/company/' . Request::route('id') . '/payments')  }}',
                    type: 'get',
                    data: function(d) {
                        d.fromDate = $('#from_date_p').val(),
                        d.toDate = $('#to_date_p').val()
                    }
                },
                'columns': [
                    {
                        'data': 'PaymentDate',
                        'render': function( data, type, row, meta ) {
                            var date = new Date(data);
                            return date.toLocaleDateString('en-CA');
                        }
                    },
                    {'data': 'OrderId'},
                    {'data': 'CardStripTokan'},
                    {
                        'data': 'TotalAmount',
                        'render': function( data, type, row, meta ) {
                            return '$' + ( parseFloat(data) / 100 );
                        }
                    }                    
                ]
            });

            $('body').on('click', '#delete-employee', function() {
                var id = $(this).attr('data-val');
                $( "#myModal-employee" ).dialog({
                    modal: true,
                    buttons: {
                        Ok: function() {
                            $( this ).dialog( "close" );
                                window.location.href = "{{ url('/admin/employee' ) }}/" + id + '/delete';
                            },
                        Cancel: function() {
                            $( this ).dialog( 'close' );
                        }
                    }
                });
            });

            $('body').on('click', '#delete-owner', function() {
                var id = $(this).attr('data-val');
                $( "#myModal-owner" ).dialog({
                    modal: true,
                    buttons: {
                        Ok: function() {
                            $( this ).dialog( "close" );
                                window.location.href = "{{ url('/admin/owner' ) }}/" + id + '/delete';
                            },
                        Cancel: function() {
                            $( this ).dialog( 'close' );
                        }
                    }
                });
            });

            // store the currently selected tab in the hash value
            $("ul.nav-tabs > li > a").on("shown.bs.tab", function(e) {
              var id = $(e.target).attr("href").substr(1);
              window.location.hash = id;
            });

            // on load of the page: switch to the currently selected tab
            var hash = window.location.hash;
            $('#myTab a[href="' + hash + '"]').tab('show');
        });
    </script>
@endsection

