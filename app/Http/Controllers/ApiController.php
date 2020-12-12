<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\product;
class ApiController extends Controller
{
    //
    public function createProduct()
    {
        $products = new Product();
    }
    public function getProducts()
    {
        $products = Product::all();
        return response()->json($products);
    }
}
