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
                    <div class="col-sm-9" style="background-color:#ffffff;">
                        <div class="row">
                            <div class="col-sm-4 labelalign">
                                <label>CATEGORY</label>
                            </div>
                            <div class="form-group col-sm-7">
                                <select name="category" id="categoryId" class="form-control valid">
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4 labelalign">
                                <label>SUB CATEGORY</label>
                            </div>
                            <div class="form-group col-sm-7">
                                <select name="subcategory" id="subCategoryId" class="form-control" required=""></select>
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
                                <img src="{{ $product_info->ProductImage or '' }}" class="img-responsive" style="width: 75%">
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
                ajax: {
                    url: '{{ url('/categories') }}',
                }
            }).on('change', function() {  $('#subCategoryId').trigger('change'); });
            $('#subCategoryId').select2({
                initSelection: function (element, callback) {
                    callback({id: '{{ $product_info->SubCategoryId }}', 'text': '{{ $product_info->SubCategoryName }}'});
                },
                placeholder: 'Select Sub Category',
                ajax:{
                    url: function() {
                        var categoryId = $('#categoryId').val();
                        return '{{ url('/categories/') }}' + '/' +  categoryId + '/sub_categories';
                    }
                } 
            });
            tinymce.init({
                selector: '#description',
                menubar: false,
                toolbar: 'insert | undo redo |  formatselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',

            });
        });
    </script>
@endsection