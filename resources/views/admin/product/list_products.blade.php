@extends('includes.layouts.main')
@section('title', 'List Products - ToolBX Admin')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/jquery.dataTables.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" type="text/css">
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
                            <h4><label>PRODUCTS</label></h4>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-group custom-search">
                                <span class="input-group-addon">SEARCH <i class="glyphicon glyphicon-search"></i></span>
                                <input id="search" type="text" class="form-control" name="search" placeholder="">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-9">
                            <div class="row">
                                <div class="col-sm-6">
                                    <select class="form-control" id="categoryId" name="categoryId">
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <select class="form-control" id="subCategoryId" name="subCategoryId">
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3" style="text-align: right;margin-bottom: 10px;">
                            <a href="{{  url('/admin/product/add')  }}" style="text-decoration: none;margin-right: -6%;">+ ADD PRODUCT</a>
                        </div>
                    </div>
                    <div class="row">
                    	<div class="col-sm-3 col-sm-offset-9" style="text-align: right;margin-bottom: 10px;">
                            <a id="publish_products" style="text-decoration: none;margin-right: -6%;">PUBLISH PRODUCTS</a>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <table class="table" id="users">
                        <thead>
                            <tr>
                                <th class="clsleftheader">PRODUCT NAME</th>
                                <th class="clsheader">DESCRIPTION</th>
                                <th class="clsheader">IMAGE</th>
                                <th class="clsheader">PRICE</th>
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
                    Do you want to delete this Product?
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts-top')
    <script type="text/javascript" src="{{ asset('js/select2.full.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
@endsection
@section('scripts')
    <script>
        $(document).ready(function(){
            var table = $('#users').DataTable({
                'ordering': false,
                'processing': true,
                'serverSide': true,
                'autoWidth': true,
                'lengthChange': false,
                'pageLength': 5,
                'stateSave': true,
                'stateDuration': -1,
                'dom': '<l<t>ip>',
                'stateSaveParams': function (settings, data) {
                    data.search.search = '';
                },
                'ajax': {
                    url: '/products',
                    type: 'get',
                    data: {
                        'categoryId': function() {
                            return $('#categoryId').val();
                        },
                        'subCategoryId': function() {
                            return $('#subCategoryId').val();
                        }
                    }
                },
                'columns': [
                    {'data': 'ProductName'},
                    {
                        'data': 'ProductDetails',
                        'render': function( data, type, row, meta ) {
                            return '<div style="height: 75px; overflow-y: scroll">' + data + '</div>';
                        }
                    },
                    {
                        'data': 'ProductImage',
                        'render': function( data, type, row, meta ) {
                            if( _.includes(data, 'http') == false )
                                return '<img src="{{ asset('images/placeholder.png') }}" class="img-responsive">';
                            else
                                return '<img src="' + data + '" class="img-responsive">';
                        }
                    },
                    {
                        'data': 'Rate',
                        'render': function( data, type, row, meta ) {
                            return '$'+data;
                        }
                    },
                    {
                        'data': 'ProductId',
                        'render': function( data, type, row, meta ) {
                            return '<a href="/admin/product/' + data + '/view"><img src="{{ asset('images/view.png') }}" width="20" height="17"></a>';
                        }
                    },
                    {
                        'data': 'ProductId',
                        'render': function( data, type, row, meta ) {
                            return '<a href="/admin/product/' + data + '/edit"><img src="{{ asset('images/edit-icon.png') }}"></a>';
                        }
                    },
                    {
                        'data': 'ProductId',
                        'render': function( data, type, row, meta ) {
                            return '<a id="delete" data-val="' + data + '"><img src="{{ asset('images/delete-icon.png') }}"></a>';
                        }
                    },
                ]
            });
            $('#categoryId').select2({
                placeholder: 'Select a Category',
                initSelection: function (element, callback) {
                    callback({id: '0', 'text': 'All'});
                },
                ajax: {
                    url: '{{ url('/categories') }}',
                }
            }).on('change', function() {  $('#subCategoryId').val(0); $('#subCategoryId').trigger('change'); });
            $('#subCategoryId').select2({
                placeholder: 'Select a Sub Category',
                initSelection: function (element, callback) {
                    callback({id: '0', 'text': 'All'});
                },
                ajax:{
                    url: function() {
                        var categoryId = $('#categoryId').val();
                        return '{{ url('/categories/') }}' + '/' +  categoryId + '/sub_categories';
                    }
                } 
            }).on('change', function() { table.draw(); });
            $('#search').keyup(function() {
                table.search($(this).val()).draw();
            });
            $('#publish_products').on('click', function() {
            	$.ajax({
            		url: 'http://app.toolbx.com/api/user/productjson',
            		type: 'get',
            		success: function(data) {
            			alert(data.message_text);
            		},
            		error: function(error) {
            			alert('Unable to publish products. Please try again later');
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
                                window.location.href = "{{ url('/admin/product' ) }}/" + id + '/delete';
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