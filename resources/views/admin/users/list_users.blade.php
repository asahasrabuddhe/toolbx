@extends('includes.layouts.main')
@section('title', 'List Users - ToolBX Admin')
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
                        </div>           
                    </div>
                    <div class="row">
                        <div class="col-sm-offset-9 col-sm-3" style="text-align: right;margin-bottom: 10px;">
                            <a href="{{ url('admin/user/invite') }}" style="text-decoration: none;margin-right: -6%;"> + INVITE RUNNER </a>
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
    </div>
</div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function(){
            $('#users').DataTable({
                'processing': true,
                'serverSide': true,
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
                            return '<a href="/admin/user/' + data + '/delete"><img src="{{ asset('images/delete-icon.png') }}"></a>';
                        }
                    },
                ]
            });
        });
    </script>
@endsection