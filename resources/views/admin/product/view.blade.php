@extends('includes.layouts.main')
@section('title', 'View Product - ToolBX Admin')
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
                        <label style="margin-left:;"></label>
                        <h4><label style="margin-left:;"><img onclick="window.location.assign('{{ url('admin/product/list_products') }}')" src="{{  asset('images/arrow_16.png')  }}" style="cursor: pointer;"> &nbsp; <label>VIEW PRODUCT</label></label></h4>
                    </div>
                    <div class="col-sm-9" style="background-color:#ffffff;">
                        <div class="row">
                            <div class="col-sm-4 labelalign">
                                <label>CATEGORY</label>
                            </div>
                            <div class="form-group col-sm-7">
                                <input type="text" class="form-control" readonly value="{{ $product_info->CategoryName }}" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4 labelalign">
                                <label>SUB CATEGORY</label>
                            </div>
                            <div class="form-group col-sm-7">
                                <input type="text" class="form-control" readonly value="{{ $product_info->SubCategoryName }}" />
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4 labelalign">
                                <label>PRODUCT NAME</label>
                            </div>
                            <div class="form-group col-sm-7">
                                <input class="form-control" readonly name="productname" placeholder="" type="text" value="{{ $product_info->ProductName or '' }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4 labeltextareaalign">
                                <label>DESCRIPTION</label>
                            </div>
                            <div class="form-group col-sm-7">
                                <div style="border: 1px solid #ccd0d2; padding: 5px;">
                                    {!! $product_info->ProductDetails or '' !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <label>IMAGE</label>
                            </div>
                            <div class="col-sm-7"><img class="img-responsive" src="{{  $product_info->ProductImage  or  ''  }}" style="width: 75%"></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4 labelalign">
                                <label>PRICE ($)</label>
                            </div>
                            <div class="form-group col-sm-7">
                                <input class="form-control" readonly name="price" placeholder="" type="text" value="{{ $product_info->Rate or '' }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4 labelalign">
                                <label>MANUFACTURER</label>
                            </div>
                            <div class="form-group col-sm-7">
                                <input class="form-control" readonly name="manufacturer" placeholder="" type="text" value="{{ $product_info->manufacturer or '' }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4 labelalign">
                                <label>MODEL</label>
                            </div>
                            <div class="form-group col-sm-7">
                                <input class="form-control" readonly name="model" placeholder="" type="text" value="{{ $product_info->model or '' }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4 labelalign">
                                <label>SKU</label>
                            </div>
                            <div class="form-group col-sm-7">
                                <input class="form-control" readonly name="sku" placeholder="" type="text" value="{{ $product_info->sku or '' }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection