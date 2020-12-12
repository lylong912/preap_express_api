<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
class CategoryController extends Controller
{
    
    public function getCategories()
    {
     
        $categories = Category::orderBy('id','DESC')->select('*')
        ->where('parent_id',0)
        ->get();
        $details = array(
        );
         for($i=0;$i<count($categories);$i++)
        {
            $categories[$i]['image'] = $this->server.$categories[$i]['image'];  
        }
        $details['data'] = $categories;
        return response()->json($details);
    }
}
