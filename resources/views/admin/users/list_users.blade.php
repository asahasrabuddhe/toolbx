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
            @include('includes.sidenav')
        </div>
        <div class="col-sm-9 npl">
            @include('includes.header')
            <div class="clearfix"></div>
            <div class="content" id="myDiv">
                @if(Session::get('success_msg'))
                    <div class="alert alert-success">{{ Session::get('success_msg') }}</div>
                @elseif(Session::get('error_msg'))
                    <div class="alert alert-danger">{{ Session::get('error_msg') }}</div>
                @endif
                <div class="data-table table-responsive">
                    <div class="row">
                        <div class="col-sm-6">
                            <h4><label>RUNNERS</label></h4>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-group custom-search">
                                <span class="input-group-addon">SEARCH <i class="glyphicon glyphicon-search"></i></span>
                                <input id="search" type="text" class="form-control" name="search" placeholder="">
                            </div>
                        </div>           
                    </div>
                    <div class="row">
                        <div class="col-sm-offset-9 col-sm-3" style="text-align: right;margin-bottom: 10px;">
                            <a href="{{ url('admin/user/invite') }}" style="text-decoration: none;margin-right: -6%; color: #000"> + INVITE RUNNER </a>
                        </div>
                    </div>    
                    <table class="table" id="users">
                        <thead>      
                            <tr>
                                <th class="clsleftheader">NAME</th>
                                <th class="clsheader">PHONE NUMBER</th>
                                <th class="clsheader">EMAIL</th>
                                <th class="clsheader">VIEW</th>
                                <th class="clsheader">EDIT</th>
                                <th class="clsrightheader">DELETE</th>
                            </tr>
                        </thead>            
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <!-- Modal -->
                <div class="modal" id="myModal" role="dialog" title="Delete Message">
                    Do you want to delete this Runner?
                </div>
            </div> 
        </div>
    </div>
</div>
@endsection
@section('scripts-top')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
@endsection
@section('scripts')
    <script>
        $(document).ready(function(){
            var table = $('#users').DataTable({
                'dom': '<l<t>ip>',
                'processing': true,
                'serverSide': true,
                'stateSave': true,
                'stateDuration': -1,
                'stateSaveParams': function (settings, data) {
                    data.search.search = '';
                },
                'ajax': {
                    url: '/runners',
                    type: 'get'
                },
                'columns': [
                    {'data': 'RegistrationName'},
                    {'data': 'RegistrationPhoneNo'},
                    {'data': 'RegistrationEmail'},
                    {
                        'data': 'RegistrationId',
                        'render': function( data, type, row, meta ) {
                            return '<a href="/admin/user/' + data + '/view"><img src="{{ asset('images/view.png') }}" width="20" height="17"></a>';
                        }
                    },
                    {
                        'data': 'RegistrationId',
                        'render': function( data, type, row, meta ) {
                            return '<a href="/admin/user/' + data + '/edit"><img src="{{ asset('images/edit-icon.png') }}"></a>';
                        }
                    },
                    {
                        'data': 'RegistrationId',
                        'render': function( data, type, row, meta ) {
                            return '<a id="delete" data-val="' + data + '"><img src="{{ asset('images/delete-icon.png') }}"></a>';
                        }
                    },
                ]
            });
            $('#search').keyup(function() {
                table.search($(this).val()).draw();
            });
            $('body').on('click', '#delete', function() {
                var id = $(this).attr('data-val');
                $( "#myModal" ).dialog({
                    modal: true,
                    buttons: {
                        Ok: function() {
                            $( this ).dialog( "close" );
                                window.location.href = "{{ url('/admin/user' ) }}/" + id + '/delete';
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