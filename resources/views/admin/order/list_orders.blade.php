@extends('includes.layouts.main')
@section('title', 'List Users - ToolBX Admin')
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
                        <div class="col-sm-6">
                            <h4><label>ORDER HISTORY</label></h4>
                        </div>
                        <div class="col-sm-6">
                        </div>           
                    </div>
                    <div class="row">
                        <div class="col-sm-6"></div>
                        <div class="col-sm-4" style="text-align: right;background:#EEEEEE;padding: 5px;">
                            <input type="text" id="from_date" size="30" class="col-sm-6" name="fromDate" value="{{ date('M d, Y') }}">
                            <input type="text" id="to_date" size="30" class="col-sm-6" name="toDate" value="{{ date('M d, Y', strtotime('+7 days')) }}">
                        </div>
                        <div class="col-sm-2" style="text-align: right;margin-bottom: 10px;">
                            <a id="export_csv" style="text-decoration: none;"> + EXPORT </a>
                        </div>
                    </div>    
                    <table class="table" id="users">
                    <thead>      
                        <tr>
                            <th class="clsleftheader">ORDER NO</th>
                            <th class="clsheader">JOBSITE</th>
                            <th class="clsheader">TOTAL AMOUNT</th>
                            <th class="clsheader">DATE</th>
                            <th class="clsheader">TIME</th>
                            <th class="clsheader">RUNNER</th>
                            <th class="clsheader">STATUS</th>
                            <th class="clsrightheader"><input type="checkbox" id="checkall"></th>
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
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script type="text/javascript" src="{{ asset('js/download.js') }}"></script>
@endsection
@section('scripts')
    <script>
        $(document).ready(function(){
            var tblOrders = $('#users').DataTable({
                'processing': true,
                'serverSide': true,
                'ajax': {
                    url: '/orders',
                    type: 'get',
                    data: function(d) {
                        d.fromDate = $('#from_date').val(),
                        d.toDate = $('#to_date').val()
                    }
                },
                'columns': [
                    {
                        'data': 'OrderId',
                        'render': function( data, type, row, meta ) {
                            return '<a href="' + '{{ url('admin/order') }}' + '/' + data + '/view">' + data + '</a>';
                        }
                    },
                    {'data': 'JobSiteName'},
                    {'data': 'TotalAmount'},
                    {
                        'data': 'OrderDate',
                        'render': function( data, type, row, meta ) {
                            var date = new Date(data);
                            return date.toLocaleDateString('en-CA');
                        }
                    },
                    {
                        'data': 'OrderDate',
                        'render': function( data, type, row, meta ) {
                            var date = new Date(data);
                            return date.toLocaleTimeString('en-CA');
                        }
                    },
                    {'data': 'RegistrationName'},
                    {
                        'data': 'status',
                        'render': function( data, type, row, meta ) {
                            if( data !== null )
                                return data;
                            else
                                return '-';
                        }
                    },
                    {
                        'data': 'OrderId',
                        'render': function( data, type, row, meta ) {
                            return '<input type="checkbox"  id="' + data + '">'
                        }
                    },
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
                }
            });
            $('#to_date').datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'M d, yy',
                onSelect: function() {
                    tblOrders.draw();
                }
            });
            $('#export_csv').on('click', function(e) {
                e.preventDefault();
                // ADD LOGIC FOR SELECTED RECORDS
                var ids = [];
                $.each($('#orders input:checked'), function(i, v) { ids.push( $(v).attr('id') ); });

                var f = $('#from_date').datepicker('getDate').toISOString();
                var t = $('#to_date').datepicker('getDate').toISOString();

                var url = "{{ url('admin/orders/export') }}" + "?from_date=" + f + "&to_date=" + t;
                if(ids.length)
                    url += "&ids=" + ids.join(',');
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
        });
    </script>
@endsection