@extends('includes.layouts.main')
@section('title', 'View Runner - ToolBX Admin')
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
                    <h4><img onclick="window.location.assign('{{ url('/admin/user/list_users') }}')" src="{{ asset('/images/arrow_16.png') }}" style="cursor: pointer;"> <label>VIEW USER</label></h4>
                    <div class="top-form" id="top_form1">
                        <div class="col-sm-9" style="background-color:#ffffff;">
                            <div class="row">
                                <div class="col-sm-4 labelalign">
                                    <label>NAME</label>
                                </div>
                                <div class="form-group col-sm-5">
                                    <input class="form-control" placeholder="" readonly required="" style="background:#FFFFFF" type="text" value="{{ $user_info->RegistrationName or '' }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4 labelalign">
                                    <label>PHONE NUMBER</label>
                                </div>
                                <div class="form-group col-sm-5">
                                    <input class="form-control" placeholder="" readonly required="" style="background:#FFFFFF" type="text" value="{{ $user_info->RegistrationPhoneNo or '' }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4 labelalign">
                                    <label>EMAIL</label>
                                </div>
                                <div class="form-group col-sm-5">
                                    <input class="form-control" placeholder="" readonly required="" style="background:#FFFFFF" type="text" value="{{ $user_info->RegistrationEmail or '' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="table" id="users">
                        <thead>
                            <tr>
                                <th class="clsleftheader">DATE PURCHASED</th>
                                <th class="clsheader">ORDER NO. - JOB SITE NAME</th>
                                <th class="clsheader">TOTAL AMOUNT IN $</th>
                                <th class="clsheader">ORDER DETAILS</th>
                                <th class="clsheader">VIEW</th>
                                <th class="clsrightheader"><input type="checkbox" id="checkall"></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
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
                'ordering': false,
                'searching': false,
                'processing': true,
                'serverSide': true,
                'autoWidth': true,
                'lengthChange': false,
                'pageLength': 5,
                'ajax': {
                    url: '{{ url('/runners/' . Request::route('id')) }}',
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
        });
    </script>
@endsection

