<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
class SubCategoryController extends Controller
{
    //
    public function getSubCategories($id)
    {
      
        $categories = Category::orderBy('id','DESC')->select('*')->where('parent_id',$id)
        ->get();
        $details = array(
        );
        $details['data'] = $categories;
        return response()->json($details);
    }
}
