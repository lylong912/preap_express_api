<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
class ProductController extends Controller
{
    //
    public function createProduct()
    {
        $products = new Product();
    }
    public function getProducts()
    {
        if(isset($_GET['category_id']))
        {
            $row_per_page = $_GET['row_per_page'];
            $category_id = $_GET['category_id'];

            $products = Product::orderBy('id','DESC')->with(['Photos'])->where('category_id', $category_id)
            ->paginate($row_per_page);
        }
        else if(isset($_GET['name']))
        {
            $row_per_page = $_GET['row_per_page'];
            $name = $_GET['name'];
            $products = Product::orderBy('id','DESC')->with(['Photos'])->where('name','like', '%'.$name.'%')
            ->paginate($row_per_page);
       
        }else
        {
            $row_per_page = $_GET['row_per_page'];
            $products = Product::orderBy('id','DESC')->with(['Photos'])->select('*')
            ->paginate($row_per_page);
        }
        
        return response()->json($products);
    }
   

 
}
