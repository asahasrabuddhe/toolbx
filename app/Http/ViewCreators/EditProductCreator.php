<?php

namespace App\Http\ViewCreators;

use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class EditProductCreator
{
    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function create(View $view)
    {
        $product = DB::table('tbl_product')
            ->join('tbl_product_category', 'tbl_product_category.CategoryId', '=', 'tbl_product.CategoryId')
            ->join('tbl_product_sub_category', 'tbl_product_sub_category.SubCategoryId', '=', 'tbl_product.SubCategoryId')
            ->where('ProductId', Request::route('id'))->first();
        $view->with('product_info', $product);
    }
}