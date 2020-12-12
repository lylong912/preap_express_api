<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariants;
use App\Models\ProductVariantType;
use Illuminate\Support\Facades\DB;
class ProductController extends Controller
{
    
    public function getProducts()
    {
        
        if(isset($_GET['category_id']))
        {
            $row_per_page = $_GET['row_per_page'];
            $category_id = $_GET['category_id'];

            $products = Product::orderBy('id','DESC')->with(['Photos'])->select('id','code','name','price','category_id'
            ,'subcategory_id','image','product_details')->where('category_id', $category_id)
            ->paginate($row_per_page);
        }
        else if(isset($_GET['name']))
        {
            $row_per_page = $_GET['row_per_page'];
            $name = $_GET['name'];
            $products = Product::orderBy('id','DESC')->with(['Photos'])->select('id','code','name','price','category_id'
            ,'subcategory_id','image','product_details')->where('name','like', '%'.$name.'%')
            ->paginate($row_per_page);
       
        }
        else if(isset($_GET['subid']))
        {
            $row_per_page = $_GET['row_per_page'];
            $subid = $_GET['subid'];
            $products = Product::orderBy('id','DESC')->with(['Photos'])->select('id','code','name','price','category_id'
            ,'subcategory_id','image','product_details')->where('subcategory_id','=', $subid)
            ->paginate($row_per_page);
       
        }
        else
        {
            $row_per_page = $_GET['row_per_page'];
            $products = Product::with(['Photos' => function ($query) {
        $query->select('id', 'photo');
    }])->orderBy('id','DESC')->select('id','code','name','price','category_id'
            ,'subcategory_id','image','product_details')
            ->paginate($row_per_page);
        }
        foreach($products as $product)
       {
           if($product->image=="no_image.png"){
               $product->image="";
           }
           if($product->image!="")
           {
               $product->image = $this->server.$product->image;
           }
           
          $productphoto =  $product->photos;
           for($i = 0;$i<count($productphoto);$i++)
           {
                $productphoto[$i]['photo'] = $this->server.$productphoto[$i]['photo'];
           }
        
            $productvariants = ProductVariantType::orderBy('id','DESC')
            ->select('id','name as type')
            ->with(['data'])
            ->where('product_id','=',$product->id)
            ->get();
            
            $product->variant_option_type = $productvariants;
             
       }

      
        return response()->json($products);
    }

    function reverse_integer($n)
{
    $reverse = 0;
    while ($n > 0)
      {
        $reverse = $reverse * 10;
        $reverse = $reverse + $n % 10;
        $n = (int)($n/10);
      }
     return $reverse;
}   

    public function getProducts1()
    {
        
    //     if(isset($_GET['category_id']))
    //     {
    //         $row_per_page = $_GET['row_per_page'];
    //         $category_id = $_GET['category_id'];

    //         $products = Product::orderBy('id','DESC')->with(['Photos'])->select('id','code','name','price','category_id'
    //         ,'subcategory_id','image','product_details')->where('category_id', $category_id)
    //         ->paginate($row_per_page);
    //     }
    //     else if(isset($_GET['name']))
    //     {
    //         $row_per_page = $_GET['row_per_page'];
    //         $name = $_GET['name'];
    //         $products = Product::orderBy('id','DESC')->with(['Photos'])->select('id','code','name','price','category_id'
    //         ,'subcategory_id','image','product_details')->where('name','like', '%'.$name.'%')
    //         ->paginate($row_per_page);
       
    //     }
    //     else if(isset($_GET['subid']))
    //     {
    //         $row_per_page = $_GET['row_per_page'];
    //         $subid = $_GET['subid'];
    //         $products = Product::orderBy('id','DESC')->with(['Photos'])->select('id','code','name','price','category_id'
    //         ,'subcategory_id','image','product_details')->where('subcategory_id','=', $subid)
    //         ->paginate($row_per_page);
       
    //     }
    //     else
    //     {
    //         $row_per_page = $_GET['row_per_page'];
    //         $products = Product::with(['Photos' => function ($query) {
    //     $query->select('id', 'photo');
    // }])->orderBy('id','DESC')->select('id','code','name','price','category_id'
    //         ,'subcategory_id','image','product_details')
    //         ->paginate($row_per_page);
    //     }
    //     foreach($products as $product)
    //    {
    //        if($product->image=="no_image.png"){
    //            $product->image="";
    //        }
    //        if($product->image!="")
    //        {
    //            $product->image = $this->server.$product->image;
    //        }
           
    //       $productphoto =  $product->photos;
    //        for($i = 0;$i<count($productphoto);$i++)
    //        {
    //             $productphoto[$i]['photo'] = $this->server.$productphoto[$i]['photo'];
    //        }
        
    //         $productvariants = ProductVariantType::orderBy('id','DESC')
    //         ->select('id','name as type')
    //         ->with(['data'])
    //         // ->join('sma_variant_option','sma_variant_option_type.id','=','sma_variant_option.type_id')
    //         ->where('product_id','=',$product->id)
    //         ->get();
            
    //         $product->variant_option_type = $productvariants;
             
    //    }
    $t = $this->reverse_integer(13);
    $t1 = 13;
     $variant_options = DB::table('sma_product_variants')
                   ->select('*')
                   ->where('variant_option_id','like','%'.$t.'%')
                   ->orWhere('variant_option_id','like','%'.$t1.'%')
                   ->get();
      
        return response()->json($variant_options);
    }

       public function getProductsByID($id)
    {
     
              //get current user
            $current_user =  auth()->user();

            $products = Product::with(['Photos' => function ($query) {
            $query->select('id', 'photo');
            }])->orderBy('id','DESC')->select('id','code','name','price','category_id'
            ,'subcategory_id','image','product_details')
            ->where('id',$id)->get();

            if($current_user)
            {
            //check wishlist
            $productinwishlist = Wishlist::orderBy('id','DESC')->select('*')
            ->where(array('customer_id'=>$current_user->id,'product_id'=>$id));
            if($productinwishlist)
            {
                $products->status = "Added"; 
            }
            }
         
                foreach($products as $product)
            {
                if($product->image=="no_image.png"){
                    $product->image="";
                }
                if($product->image!="")
                {
                    $product->image = $this->server.$product->image;
                }
                    
            }
      
        return response()->json($products);
    }
   
    public function getProductVariants($id)
    {
       
            
        $productvariants = ProductVariants::orderBy('id','DESC')
        ->where('product_id',$id)->get();
        return response()->json($productvariants);
    }
 
}
