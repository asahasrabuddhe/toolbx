@extends('includes.layouts.main')
@section('title', 'View Company - ToolBX Admin')
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
                    <h4><img onclick="window.location.assign('{{ url('/admin/company/list_companies') }}')" src="{{ asset('/images/arrow_16.png') }}" style="cursor: pointer;"> <label>VIEW COMPANY</label></h4>
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#orders">ORDERS</a></li>
                        <li><a data-toggle="tab" href="#employees">EMPLOYEES</a></li>
                        <li><a data-toggle="tab" href="#owner">OWNER</a></li>
                        <li><a data-toggle="tab" href="#payment">PAYMENT</a></li>
                    </ul>
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
                                        <th class="clsrightheader">VIEW</th>
                                        <th class="clsrightheader"><input type="checkbox" id="ordersSelectAll"></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div id="employees" class="tab-pane fade">
                            <div class="row">
                                <div class="col-sm-offset-9 col-sm-3 pull-right" style="text-align: right;margin-bottom: 10px;">
                                    <a href="{{ url('admin/employee/invite') }}" class="pull-right" style="text-decoration: none;margin-right: -6%;"> + INVITE EMPLOYEE </a>
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
                            <div class="row">&nbsp;</div>
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
                                    EXPORT
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
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function(){
            $('#tblOrders').DataTable({
                'ordering': false,
                'searching': false,
                'processing': true,
                'serverSide': true,
                'autoWidth': true,
                'lengthChange': false,
                'pageLength': 5,
                'ajax': {
                    url: '{{ url('/company/' . Request::route('id') . '/orders') }}',
                    type: 'get',
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
            $('#tblEmployees').DataTable({
                'searching': false,
                'processing': true,
                'serverSide': true,
                'autoWidth': true,
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
                            return '<a href="/admin/employee/' + data + '/delete"><img src="{{ asset('images/delete-icon.png') }}"></a>';
                        }
                    },
                ]
            });
            $('#tblOwner').DataTable({
                'searching': false,
                'processing': true,
                'serverSide': true,
                'autoWidth': true,
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
                            return '<a href="/admin/owner/' + data + '/delete"><img src="{{ asset('images/delete-icon.png') }}"></a>';
                        }
                    },
                ]
            });
            $('#tblPayment').DataTable({
                'searching': false,
                'processing': true,
                'serverSide': true,
                'ajax': {
                    url: '{{ url('/company/' . Request::route('id') . '/payments')  }}',
                    type: 'get',
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
                    {'data': 'TotalAmount'}                    
                ]
            });
        });
    </script>
@endsection

