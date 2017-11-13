@extends('includes.layouts.main')
@section('title', 'List Users - ToolBX Admin')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/jquery.dataTables.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}" type="text/css">
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
                        <label style="margin-left:;">
                            <h4>
                                <img style="cursor: pointer;" onclick="window.location.assign('{{ url('admin/product/list_products') }}')" src="{{ asset('images/arrow_16.png') }}"> &nbsp; <label> ADD PRODUCT </label>
                            </h4>           
                        </label>
                    </div>
                    <form class="col-sm-9" style="background-color:#ffffff;" action=" {{ url('product/' . Request::route('id') . '/update') }}" method="POST">
                        {{ csrf_field() }}
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-sm-4 labelalign">
                                <label>CATEGORY</label>
                            </div>
                            <div class="form-group col-sm-7">
                                <select name="category" id="categoryId" class="form-control valid" readonly>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4 labelalign">
                                <label>SUB CATEGORY</label>
                            </div>
                            <div class="form-group col-sm-7">
                                <select name="subcategory" id="subCategoryId" class="form-control" readonly></select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4 labelalign">
                                <label>PRODUCT NAME</label>
                            </div>
                            <div class="form-group col-sm-7">
                                <input type="text" name="productname" class="form-control" value="{{ $product_info->ProductName or '' }}" placeholder="">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4 labeltextareaalign">
                                <label>DESCRIPTION</label>
                            </div>
                            <div class="form-group col-sm-7">
                                <textarea id="description" name="description" class="form-control" cols="10" rows="5">{{ $product_info->ProductDetails or '' }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <label>IMAGE</label>
                            </div>
                            <div class="col-sm-7">
                                <img src="{{ $product_info->ProductImage or '' }}" class="img-responsive productImage" style="width: 75%">
                                <input type="file" name="newImage" id="newImage">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4 labelalign">
                                <label>PRICE ($)</label>
                            </div>
                            <div class="form-group col-sm-7">
                                <input type="text" name="price" class="form-control" value="{{ $product_info->Rate or '' }}" placeholder="">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-offset-4 col-sm-7">
                                <div class="col-sm-6">
                                    <button style="margin-left: -15px;width: 135px;" class="form-control btn-default btn-gray" onclick="window.location.assign('{{ url('/admin/product/list_products') }}')" type="reset">CANCEL</button>
                                </div>
                                <div class="col-sm-6">
                                    <button style="margin-left: -15px;width: 135px;" class="form-control btn-default btn-blue common" name="submit" type="submit">SAVE</button>
                                </div>
                            </div>
                        </div>
                    </form>
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
    <script type="text/javascript" src="{{ asset('tinymce/tinymce.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/select2.full.min.js') }}"></script>
@endsection
@section('scripts')
    <script>
        $(document).ready(function(){
            $('#categoryId').select2({
                 initSelection: function (element, callback) {
                    callback({id: '{{ $product_info->CategoryId }}', 'text': '{{ $product_info->CategoryName }}'});
                },
                placeholder: 'Select Category',
            }).on('change', function() {  $('#subCategoryId').trigger('change'); });
            $('#subCategoryId').select2({
                initSelection: function (element, callback) {
                    callback({id: '{{ $product_info->SubCategoryId }}', 'text': '{{ $product_info->SubCategoryName }}'});
                },
                placeholder: 'Select Sub Category',
                readonly: true,
            });
            tinymce.init({
                selector: '#description',
                menubar: false,
                toolbar: 'insert | undo redo |  formatselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',

            });
        });
    </script>
@endsection