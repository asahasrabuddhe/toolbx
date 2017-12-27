@extends('includes.layouts.main')
@section('title', 'List Companies - ToolBX Admin')
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
                @if(Session::get('success_msg'))
                <div class="alert alert-success">
                    {{ Session::get('success_msg') }}
                </div>@elseif(Session::get('error_msg'))
                <div class="alert alert-danger">
                    {{ Session::get('error_msg') }}
                </div>@endif
                <div class="data-table table-responsive">
                    <div class="row">
                        <div class="col-sm-6">
                            <h4><label>COMPANY</label></h4>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-group custom-search">
                                <span class="input-group-addon">SEARCH <i class="glyphicon glyphicon-search"></i></span>
                                <input id="search" type="text" class="form-control" name="search" placeholder="">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-offset-7 col-sm-3" style="text-align: right;margin-bottom: 10px;">
                            <a class="pull-right" href="{{ url('admin/owner/invite') }}" style="text-decoration: none;margin-right: -6%;">+ INVITE OWNER</a>
                        </div>
                        <div class="col-sm-2" style="text-align: right;margin-bottom: 10px;">
                            <label><span class="export_csv" style="float: right;background:#EEEEEE;padding: 5px;margin-right: -18px;"><a id="export_csv" style="text-decoration: none;">EXPORT</a></span></label>
                        </div>
                    </div>
                    <table class="table" id="users">
                        <thead>
                            <tr>
                                <th class="clsleftheader">COMPANY NAME</th>
                                <th class="clsheader">VIEW</th>
                                <th class="clsheader">EDIT</th>
                                <th class="clsrightheader">DELETE</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <!-- Modal -->
                <div class="modal" id="myModal" role="dialog" title="Delete Message">
                    Do you want to delete this Company?
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts-top')
    <script type="text/javascript" src="{{ asset('js/download.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
@endsection
@section('scripts')
    <script>
        $(document).ready(function(){
            var table = $('#users').DataTable({
                'processing': true,
                'serverSide': true,
                'dom': '<l<t>ip>',
                'stateSave': true,
                'stateDuration': -1,
                'stateSaveParams': function (settings, data) {
                    data.search.search = '';
                },
                'ajax': {
                    url: '/companies',
                    type: 'get'
                },
                'columns': [
                    {'data': 'CompanyName'},
                    {
                        'data': 'CompanyId',
                        'render': function( data, type, row, meta ) {
                            return '<a href="/admin/company/' + data + '/view"><img src="{{ asset('images/view.png') }}" width="20" height="17"></a>';
                        },
                        'orderable': false,
                        'searchable': false
                    },
                    {
                        'data': 'CompanyId',
                        'render': function( data, type, row, meta ) {
                            return '<a href="/admin/company/' + data + '/edit"><img src="{{ asset('images/edit-icon.png') }}"></a>';
                        },
                        'orderable': false,
                        'searchable': false
                    },
                    {
                        'data': 'CompanyId',
                        'render': function( data, type, row, meta ) {
                            return '<a id="delete" data-val="' + data + '"><img src="{{ asset('images/delete-icon.png') }}"></a>';
                        },
                        'orderable': false,
                        'searchable': false
                    },
                ]
            });

            $('#search').keyup(function() {
                table.search($(this).val()).draw();
            });

            $('#export_csv').on('click', function(e) {
                e.preventDefault();
                var url = "{{ url('admin/companies/export') }}";
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

            $('body').on('click', '#delete', function() {
                var id = $(this).attr('data-val');
                $( "#myModal" ).dialog({
                    modal: true,
                    buttons: {
                        Ok: function() {
                            $( this ).dialog( "close" );
                                window.location.href = "{{ url('/admin/company' ) }}/" + id + '/delete';
                            },
                        Cancel: function() {
                            $( this ).dialog( 'close' );
                        }
                    }
                });
            });
        });
    </script>
@endsection