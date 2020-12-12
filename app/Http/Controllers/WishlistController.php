<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Models\Product;
use App\Models\ProductVariants;
use App\Models\ProductVariantType;
use Illuminate\Support\Facades\DB;
class WishlistController extends Controller
{
  
    public function getwishlist()
    {
      
        //get current user
        $current_user =  auth()->user();
        $id=$current_user->id;
        $wishlists = Product::orderBy('product_id','DESC')
        ->select('sma_products.*','sma_wishlist.id as wishlist_id','sma_wishlist.quantity as quantity','sma_wishlist.variant_id as variant_id')
        ->join('sma_wishlist', 'sma_products.id', '=', 'sma_wishlist.product_id')
        ->with(['Photos'])
       
        ->where('sma_wishlist.customer_id', $current_user->id)
        ->get();

        //sum total
        $totalsum = 0;
        $totalquantity = 0;
        $grand_total = 0;
        foreach($wishlists as $wishlist)
        {
            $totalsum = $wishlist->price * $wishlist->quantity;
            $totalquantity = $totalquantity+$wishlist->quantity;
            $grand_total = $grand_total+$totalsum;
        }

       $details = array(
           "wishlist_quantity"=>$totalquantity,
           "total"=>$grand_total,
           "noted"=>"dfd"
       );
       $details['data'] = array(
    );
       foreach($wishlists as $wishlist)
       {
            $productvariants = ProductVariantType::orderBy('id','DESC')
            ->select('id','name as type')
            ->with(['data'])
            ->where('product_id','=',$wishlist->id)
            ->get();
            
            $wishlist->variant_option_type = $productvariants;

           if($wishlist->image=="no_image.png"){
               $wishlist->image="";
           }
           if($wishlist->image!="")
           {
               $wishlist->image = $this->server.$wishlist->image;
           }
           $wishlistphoto =  $wishlist->photos;
           for($i = 0;$i<count($wishlistphoto);$i++)
           {
                $wishlistphoto[$i]['photo'] = $this->server.$wishlistphoto[$i]['photo'];
           }
          if($wishlist->variant_id!="")
          {
            $variants = DB::table('sma_product_variants')->select('*')
            ->where('id',$wishlist->variant_id)
            ->get();
              foreach($variants as $variant)
              {
            $product = array(
            'id'=> $wishlist->id,
            'variant_id'=> $variant->id,
            'variant_name'=>$variant->name,
            'variant_price'=>$variant->price,
            'grand_total'=> $variant->price*$wishlist->quantity,
            'variant_option_type'=>$wishlist->variant_option_type,
            'product_id'=> $variant->product_id,
            'product_name'=> $wishlist->name,
            'price'=> $wishlist->price,
            'image'=> $wishlist->image,
            'product_details'=>$wishlist->product_details,
            'quantity'=>$wishlist->quantity,
           
            'category_id'=>$wishlist->category_id,
            'subcategory_id'=>$wishlist->subcategory_id,
            'photos'=> $wishlistphoto,
           
           );
              }
        
          }else
          {
            $product = array(
           'id'=> $wishlist->id,
           'name'=> $wishlist->name,
           'price'=> $wishlist->price,
           'image'=> $wishlist->image,
           'photos'=> $wishlistphoto,
           'variant_id'=>'',
           'variant_name'=>'',
           'variant_price'=>'',
           'variant_option_type'=>$wishlist->variant_option_type,
           'category_id'=>$wishlist->category_id,
           'subcategory_id'=>$wishlist->subcategory_id,
           'product_details'=>$wishlist->product_details,
           'quantity'=>$wishlist->quantity,
           'grand_total'=> $wishlist->price * $wishlist->quantity,
           );
          }
       

           array_push($details['data'],$product);
          
       }

       
        return response()->json($details);
    }
       public function checkProductByID($id)
    {
        $product = Product::orderBy('id','DESC')->where('id',$id)->get();
        $product_id = 0;
        foreach($product as $prod)
        {
            $product_id = $prod->id;
        }
        
         return response()->json(array('product_id'=>$product_id));
    }

      public function checkVariantByID($id,$pid)
    {
    // $n = $id;
    // $nstr = $n . "";
    // $sum = 0;
    // for ($i = 0; $i < strlen($nstr); ++$i)
    // {
    //     $sum += $nstr[$i];
    // }
    
        
        $variant = DB::table('sma_product_variants')->orderBy('id','DESC')
        ->where(array('product_id'=>$pid,'variant_option_id'=>$id))
        // ->orwhere(array('product_id'=>$pid,'variant_option_id'=>$reverse))
        ->get();
        $variant_id = 0;
        foreach($variant as $va)
        {
            $variant_id = $va->id;
        }
         return response()->json(array('variant_id'=>$variant_id));
    }

    public function getProductandVariantByID($id,$customer_id,$variant_id)
    {
        //get current user
        $current_user =  auth()->user();
        $wishlists = Product::orderBy('product_id','DESC')
        ->select('sma_products.*','sma_wishlist.id as wishlist_id','sma_wishlist.variant_id as variant_id','sma_wishlist.quantity as quantity')
        ->join('sma_wishlist', 'sma_products.id', '=', 'sma_wishlist.product_id')
        ->with(['Photos'])
        ->where('sma_wishlist.customer_id', $current_user->id)
        ->where('sma_wishlist.product_id', $id)
        ->where('sma_wishlist.variant_id', $variant_id)
        ->get();
        
        //sum total
        $totalsum = 0;
        $totalquantity = 0;
        $grand_total = 0;
        $product_id=0;
        $variant_id=0;
        foreach($wishlists as $wishlist)
        {
            $totalsum = $wishlist->price * $wishlist->quantity;
            $totalquantity = $totalquantity+$wishlist->quantity;
            $grand_total = $grand_total+$totalsum;
            $product_id = $wishlist->id;
            $variant_id = $wishlist->variant_id;
        }

       $details = array(
           "product_id"=>$product_id,
            "variant_id"=>$variant_id,
           "wishlist_quantity"=>$totalquantity,
           "total"=>$grand_total,
       );
   
        return response()->json($details);
    }
     public function getProductByID($id,$customer_id)
    {
        //get current user
        $current_user =  auth()->user();
        $wishlists = Product::orderBy('product_id','DESC')
        ->select('sma_products.*','sma_wishlist.id as wishlist_id',
        'sma_wishlist.quantity as quantity')
        ->join('sma_wishlist', 'sma_products.id', '=', 'sma_wishlist.product_id')
        ->with(['Photos'])
        ->where('sma_wishlist.customer_id', $current_user->id)
        ->where('sma_wishlist.product_id', $id)
        
        ->get();
        
        //sum total
        $totalsum = 0;
        $totalquantity = 0;
        $grand_total = 0;
        $product_id=0;
        
        
        foreach($wishlists as $wishlist)
        {
            $totalsum = $wishlist->price * $wishlist->quantity;
            $totalquantity = $totalquantity+$wishlist->quantity;
            $grand_total = $grand_total+$totalsum;
            $product_id = $wishlist->id;
           
           
        }

       $details = array(
           "product_id"=>$product_id,
           "wishlist_quantity"=>$totalquantity,
           "total"=>$grand_total,
       );
   
        return response()->json($details);
    }
    public function addTowishlist(Request $request)
    {
        //get current user
        $current_user =  auth()->user();
        $response = $this->checkProductByID($request->input('product_id'));

          if($response->original['product_id']==0)
        {
             return response()->json(['message'=>'No product found with this id'],500);
        }else
        {
        
           
            $response = $this->checkVariantByID($request->input('variant_option_id'),$request->input('product_id'));
            $reqva = $request->input('variant_option_id');
            //response return product without varian
           
            if($response->original['variant_id']==0 && $reqva!="")
            {
                 return response()->json(array(
                    'message'=>'No variant id found with this product'
                ));
            }
        
            
            $wishlist = Wishlist::updateOrCreate(array('customer_id' => $current_user->id,'product_id' => $request->input('product_id'),
            'variant_id'=>$response->original['variant_id']));
            $wishlist->quantity += $request->input('quantity');
            $wishlist->save();

            $havetoremove=Wishlist::where('quantity','<=',0)->delete();
            if($havetoremove){
                return response()->json(array(
                    'message'=>'Product removed from wishlist'
                ));
            }
        }   
       
        $response = $this->getProductandVariantByID($request->input('product_id'),$current_user->id,$response->original['variant_id']);
        // $response = $this->checkVariantByID($request->input('variant_option_id'),$request->input('product_id'));
        
        
        return response()->json($response->original,201);
    }
     public function updatewishlist(Request $request,$id)
    {
        //get current user
        $current_user =  auth()->user();
        if($request->input('variant_id')!="")
        {
            $variant_id = $request->input('variant_id');
            $wishlist = Wishlist::updateOrCreate(array('customer_id' => $current_user->id,'product_id' => $id,'variant_id'=>$variant_id));
            $wishlist->quantity += $request->input('quantity');
            $wishlist->save();
            $response = $this->getProductandVariantByID($id,$current_user->id,$variant_id);
        }else{
            $wishlist = Wishlist::updateOrCreate(array('customer_id' => $current_user->id,'product_id' => $id));
        $wishlist->quantity += $request->input('quantity');
        $wishlist->save();
         $response = $this->getProductByID($id,$current_user->id);
        }
        
        $havetoremove=Wishlist::where('quantity','<=',0)->delete();
        if($havetoremove){
            return response()->json(array(
                'message'=>'Product removed from wishlist'
            ));
        }
       
           if($response->original['product_id']==0)
        {
             return response()->json('No product with that id',500);
        }
        return response()->json($response->original,201);
    }

    public function editwishlist(Request $request,$id)
    {
         //get current user
         $current_user =  auth()->user();
    
        $wishlist =  Wishlist::orderBy('product_id','ASC')->where(array('product_id'=> $id,'customer_id'=>  $current_user->id))->update(array('quantity'=>$request->input('quantity')));
        $response = $this->getProductByID($id,$current_user->id);
        
        $havetoremove=Wishlist::where('quantity','<',0)->delete();
        if($havetoremove){
            return response()->json(array(
                'message'=>'Product removed from wishlist'
            ));
        }
    if($response->original['product_id']==0)
        {
             return response()->json('No product with that id',500);
        }
            return response()->json($response->original);
        
       
    }

    public function removeProduct(Request $request,$id)
    {
         //get current user
         $current_user =  auth()->user();
         $response = $this->getProductByID($id,$current_user->id,$request->input('variant_id'));
        if($response->original['product_id']==0)
        {
             return response()->json('No product with that id',500);
        }
        $wishlist =  Wishlist::orderBy('product_id','ASC')->where(array('product_id'=> $id,'customer_id'=>  $current_user->id))->delete();
        
            return response()->json(array("message"=>"Product removed"),200);
        
       
    }
    public function moveTowishlist(Request $request)
    {
        //get current user
        $current_user =  auth()->user();
        $datas = DB::table('sma_wishlist')->get();
        
        foreach($datas as $data){
            DB::table('sma_wishlist')->where('customer_id',$current_user->id)->insert(['customer_id' => $data->customer_id, 'product_id'=>$data->product_id,'quantity'=>$data->quantity]);
        }
        $havetoremove=Wishlist::orderBy('product_id','ASC')->delete();
        $response = $this->getProductByID($request->input('product_id'),$current_user->id);
           if($response->original['product_id']==0)
        {
             return response()->json('No product with that id',500);
        }
        return response()->json($response->original,201);
    }
}
