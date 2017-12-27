@extends('includes.layouts.main')
@section('title', 'Order Detail - ToolBX Admin')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/jquery.dataTables.min.css') }}" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" type="text/css">
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
                        <div class="col-sm-6">
                            <label style="margin-left:;"></label>
                            <h4><label style="margin-left:;"><img onclick="window.location.assign('{{ url('admin/order/list_orders') }}')" src="{{ asset('images/arrow_16.png') }}" style="cursor: pointer;"> &nbsp; <label>VIEW ORDER</label></label></h4>
                        </div>
                        <div class="col-sm-6"></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-offset-10 col-sm-2" style="text-align: right;margin-bottom: 10px;">
                            <a href="{{ url('/admin/order/' . Request::route('id') . '/export') }}" style="text-decoration: none;" target="_blank">+ EXPORT</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <label>ORDER NUMBER</label> <span>{{ $order_details->OrderId }}</span>
                        </div>
                        <div class="col-sm-3">
                            <label>JOB SITE ADDRESS</label> <span>{{ $order_details->Address or '-' }}</span>
                        </div>
                        <div class="col-sm-3">
                            <label>DATE PURCHASED</label> <span>{{ date('m/d/y', strtotime($order_details->OrderDate)) }}</span>
                        </div>
                        <div class="col-sm-3">
                            <label>DELIVERY STATUS</label> <span>{{ $order_details->status or '-' }}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <label>SUB TOTAL</label> <span>${{ number_format ($order_details->TotalAmount + ($order_details->TotalAmount * 0.1), 2) }}</span>
                        </div>
                        <div class="col-sm-3">
                            <label>HST</label> <span>${{ number_format (( ($order_details->TotalAmount + ($order_details->TotalAmount * 0.1)) * 0.13), 2) }}</span>
                        </div>
                        <div class="col-sm-3">
                            <label>DELIVERY FEE</label> <span>${{ $order_details->DeliveryCharges }}</span>
                        </div>
                        <div class="col-sm-3">
                            <label>TOTAL</label> <span>${{ number_format($order_details->TotalAmount + ($order_details->TotalAmount * 0.1) + number_format (( ($order_details->TotalAmount + ($order_details->TotalAmount * 0.1)) * 0.13), 2) +  $order_details->DeliveryCharges, 2) }}</span>
                        </div>
                    </div>
                    <table class="table" id="users">
                        <thead>
                            <tr>
                                <th class="clsleftheader">ORDER DETAILS</th>
                                <th class="clsheader">QUANTITY</th>
                                <th class="clsrightheader">AMOUNT IN $</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table><!-- Modal -->
                    <div class="modal fade" id="myModal" role="dialog">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <p style="text-align: center;"><b>DELETE MESSAGE</b></p>
                                </div>
                                <div class="modal-body">
                                    <p style="font-family:verdana;text-align: center;">DO YOU WANT TO DELETE THIS RUNNER?</p>
                                </div>
                                <div class="modal-footer" style="margin-bottom:-19px">
                                    <div class="col-sm-6 col-xs-6" style="border-right:solid 1px #EBEBEB;height:100%;margin-top:-19px">
                                        <div class="col-sm-6 col-xs-6" style="height:42px;margin-top:10px">
                                            <button class="btn btn-default btn-block" data-dismiss="modal" style="width: 100%;background-color: transparent; border: medium; border-width: 0px 0px 0px 1px;" type="button">NO</button>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xs-6" style="height:42px;margin-top:-10px">
                                        <a class="btn btn-info" href="" id="button123" role="button" style="width: 100%;background-color: transparent; border: medium; color:#333;">YES</a>
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
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
@endsection
@section('scripts')
    <script>
        $(document).ready(function(){
            var tblOrders = $('#users').DataTable({
                'ordering': false,
                'searching': false,
                'lengthChange': false,
                'processing': true,
                'serverSide': true,
                'ajax': {
                    url: '/orders/' + {{ Request::route('id') }} + '/details',
                    type: 'get',
                    data: function(d) {
                        d.fromDate = $('#from_date').val(),
                        d.toDate = $('#to_date').val()
                    }
                },
                'columns': [
                    {'data':'ProductName'},
                    {'data':'Quantity'},
                    {
                        'data':'Amount',
                        'render': function(data, type, row, meta) {
                            data = (data * 1) + (data * 0.1);
                            return '$' + data.toFixed(2);
                        }
                    }
                ]
            });
        });
    </script>
@endsection