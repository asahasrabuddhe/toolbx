@extends('includes.layouts.main')
@section('title', 'List Users - ToolBX Admin')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/jquery.dataTables.min.css') }}" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" type="text/css">
@endsection
@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-3 npr">
            @if(isset($mode) && $mode != 'pdf')
                @include('includes.sidenav')
            @endif
        </div>
        <div class="col-sm-9 npl">
            @if(isset($mode) && $mode != 'pdf')
                @include('includes.header')
            @endif
            <div class="clearfix"></div>
            <div class="content" id="myDiv">
                <div class="data-table table-responsive">
                    <div class="row">
                        <div class="col-sm-6">
                            <label style="margin-left:;">
                            <h4>
                                <img style="cursor: pointer;" onclick="window.location.assign('{{ url('admin/order/list_orders') }}')" src="{{ asset('images/arrow_16.png') }}"> &nbsp; <label> VIEW ORDER </label>
                            </h4>           
                        </label>
                        </div>
                        <div class="col-sm-6">
                        </div>           
                    </div>
                    <div class="row">
                        <div class="col-sm-offset-10 col-sm-2" style="text-align: right;margin-bottom: 10px;">
                            <a href="{{ url('/admin/order/' . Request::route('id') . '/export') }}" target="_blank" style="text-decoration: none;"> + EXPORT </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <label>ORDER NUMBER </label><span> {{ $order_details->OrderId }}</span>
                        </div>
                        <div class="col-sm-3">
                            <label>JOB SITE ADDRESS </label><span> {{ $order_details->Address or '-' }}</span>
                        </div>
                        <div class="col-sm-3">
                            <label>DATE PURCHASED </label><span> {{ date('m/d/y', strtotime($order_details->OrderDate)) }}</span>
                        </div>
                        <div class="col-sm-3">
                            <label>DELIVERY STATUS </label><span> {{ $order_details->status or '-' }}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <label>TOTAL AMOUNNT </label><span> ${{ $order_details->TotalAmount }}</span>
                        </div>
                        <div class="col-sm-3">
                            <label>HST </label><span> ${{ $order_details->TaxAmount }}</span>
                        </div>
                        <div class="col-sm-3">
                            <label>DELIVERY FEE </label><span> ${{ $order_details->DeliveryCharges }}</span>
                        </div>
                        <div class="col-sm-3">
                            <label>TOTAL </label><span> ${{ $order_details->TotalAmount + $order_details->TaxAmount + $order_details->DeliveryCharges }}</span>
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
                <tbody>
            </tbody>
        </table>
        <!-- Modal -->
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
                                <button type="button" class="btn btn-default btn-block" data-dismiss="modal" style="width: 100%;background-color: transparent; border: medium; border-width: 0px 0px 0px 1px;">NO</button>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-6" style="height:42px;margin-top:-10px">                                               
                            <a href="" id="button123" class="btn btn-info" role="button" style="width: 100%;background-color: transparent; border: medium; color:#333;">YES</a>                                                  
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
                    {'data':'Amount'}
                ]
            });
        });
    </script>
@endsection