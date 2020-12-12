<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\ProductVariantType;
use Illuminate\Support\Facades\DB;
use App\Models\ProductVariants;
use App\Models\Product;


class CartController extends Controller
{
  
    
    public function getCart()
    {

        //get current user
        $current_user =  auth()->user();
        $id=$current_user->id;
        $carts = Product::orderBy('product_id','DESC')
        ->select('sma_products.*','sma_cart.id as cart_id','sma_cart.quantity as quantity','sma_cart.variant_id as variant_id')
        ->join('sma_cart', 'sma_products.id', '=', 'sma_cart.product_id')
        ->with(['Photos'])
        ->where('sma_cart.customer_id', $current_user->id)
        ->get();

        //sum total
        $totalsum = 0;
        $totalquantity = 0;
        $grand_total = 0;
        foreach($carts as $cart)
        {
            $totalsum = $cart->price * $cart->quantity;
            $totalquantity = $totalquantity+$cart->quantity;
            $grand_total = $grand_total+$totalsum;
        }

       $details = array(
           "cart_quantity"=>$totalquantity,
           "total"=>$grand_total,
       );
       $details['data'] = array();
       foreach($carts as $cart)
       {
            $productvariants = ProductVariantType::orderBy('id','DESC')
            ->select('id','name as type')
            ->with(['data'])
            ->where('product_id','=',$cart->id)
            ->get();
            
            $cart->variant_option_type = $productvariants;

           if($cart->image=="no_image.png"){
               $cart->image="";
           }
           if($cart->image!="")
           {
               $cart->image = $this->server.$cart->image;
           }
           $cartphoto =  $cart->photos;
           for($i = 0;$i<count($cartphoto);$i++)
           {
                $cartphoto[$i]['photo'] = $this->server.$cartphoto[$i]['photo'];
           }
          if($cart->variant_id!="")
          {
            $variants = DB::table('sma_product_variants')->select('*')
            ->where('id',$cart->variant_id)
            ->get();
              foreach($variants as $variant)
              {
            $product = array(
            'id'=> $cart->id,
            'variant_id'=> $variant->id,
            'variant_name'=>$variant->name,
            'variant_price'=>$variant->price,
            'grand_total'=> $variant->price*$cart->quantity,
            'variant_option_type'=>$cart->variant_option_type,
            'product_id'=> $variant->product_id,
            'product_name'=> $cart->name,
            'price'=> $cart->price,
            'image'=> $cart->image,
            'product_details'=>$cart->product_details,
            'quantity'=>$cart->quantity,
           
            'category_id'=>$cart->category_id,
            'subcategory_id'=>$cart->subcategory_id,
            'photos'=> $cartphoto,
           
           );
              }
        
          }else
          {
            $product = array(
           'id'=> $cart->id,
           'name'=> $cart->name,
           'price'=> $cart->price,
           'image'=> $cart->image,
           'photos'=> $cartphoto,
           'variant_id'=>'',
           'variant_name'=>'',
           'variant_price'=>'',
           'variant_option_type'=>$cart->variant_option_type,
           'category_id'=>$cart->category_id,
           'subcategory_id'=>$cart->subcategory_id,
           'product_details'=>$cart->product_details,
           'quantity'=>$cart->quantity,
           'grand_total'=> $cart->price * $cart->quantity,
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
        $carts = Product::orderBy('product_id','DESC')
        ->select('sma_products.*','sma_cart.id as cart_id','sma_cart.variant_id as variant_id','sma_cart.quantity as quantity')
        ->join('sma_cart', 'sma_products.id', '=', 'sma_cart.product_id')
        ->with(['Photos'])
        ->where('sma_cart.customer_id', $current_user->id)
        ->where('sma_cart.product_id', $id)
        ->where('sma_cart.variant_id', $variant_id)
        ->get();
        
        //sum total
        $totalsum = 0;
        $totalquantity = 0;
        $grand_total = 0;
        $product_id=0;
        $variant_id=0;
        foreach($carts as $cart)
        {
            $totalsum = $cart->price * $cart->quantity;
            $totalquantity = $totalquantity+$cart->quantity;
            $grand_total = $grand_total+$totalsum;
            $product_id = $cart->id;
            $variant_id = $cart->variant_id;
        }

       $details = array(
           "product_id"=>$product_id,
            "variant_id"=>$variant_id,
           "cart_quantity"=>$totalquantity,
           "total"=>$grand_total,
       );
   
        return response()->json($details);
    }
     public function getProductByID($id,$customer_id)
    {
        //get current user
        $current_user =  auth()->user();
        $carts = Product::orderBy('product_id','DESC')
        ->select('sma_products.*','sma_cart.id as cart_id',
        'sma_cart.quantity as quantity')
        ->join('sma_cart', 'sma_products.id', '=', 'sma_cart.product_id')
        ->with(['Photos'])
        ->where('sma_cart.customer_id', $current_user->id)
        ->where('sma_cart.product_id', $id)
        
        ->get();
        
        //sum total
        $totalsum = 0;
        $totalquantity = 0;
        $grand_total = 0;
        $product_id=0;
        
        
        foreach($carts as $cart)
        {
            $totalsum = $cart->price * $cart->quantity;
            $totalquantity = $totalquantity+$cart->quantity;
            $grand_total = $grand_total+$totalsum;
            $product_id = $cart->id;
           
           
        }

       $details = array(
           "product_id"=>$product_id,
           "cart_quantity"=>$totalquantity,
           "total"=>$grand_total,
       );
   
        return response()->json($details);
    }
    public function addToCart(Request $request)
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
            //response return product without variant
           
            if($response->original['variant_id']==0 && $reqva!="")
            {
                 return response()->json(array(
                    'message'=>'No variant id found with this product'
                ));
            }
        
            
            $cart = Cart::updateOrCreate(array('customer_id' => $current_user->id,'product_id' => $request->input('product_id'),
            'variant_id'=>$response->original['variant_id']));
            $cart->quantity += $request->input('quantity');
            $cart->save();

            $havetoremove=Cart::where('quantity','<=',0)->delete();
            if($havetoremove){
                return response()->json(array(
                    'message'=>'Product removed from Cart'
                ));
            }
        }   
       
        $response = $this->getProductandVariantByID($request->input('product_id'),$current_user->id,$response->original['variant_id']);
        // $response = $this->checkVariantByID($request->input('variant_option_id'),$request->input('product_id'));
          
        return response()->json($response->original,201);
    }
     public function updateCart(Request $request,$id)
    {
        //get current user
        $current_user =  auth()->user();
        if($request->has('variant_id'))
        {
            $variant_id = $request->input('variant_id');
            $cart = Cart::updateOrCreate(array('customer_id' => $current_user->id,'product_id' => $id,'variant_id'=>$variant_id));
            $cart->quantity += $request->input('quantity');
            $cart->save();
            $response = $this->getProductandVariantByID($id,$current_user->id,$variant_id);
            
        }else{
            $cart = Cart::updateOrCreate(array('customer_id' => $current_user->id,'product_id' => $id));
            $cart->quantity += $request->input('quantity');
            $cart->save();
            $response = $this->getProductByID($id,$current_user->id);
            //remove if <=0
            $havetoremove=Cart::where('quantity','<=',0)->delete();
            if($havetoremove){
                return response()->json(array(
                    'message'=>'Product removed from Cart'
                ));
            }
        }
        if($response->original['product_id']==0)
        {
             return response()->json('No product with that id',500);
        }
        //remove if <=0
        $havetoremove=Cart::where('quantity','<=',0)->delete();
        if($havetoremove){
                return response()->json(array(
                    'message'=>'Product removed from Cart'
                ));
            }
        return response()->json($response->original,201);
    }

    public function editCart(Request $request,$id)
    {
         //get current user
         $current_user =  auth()->user();
    
        $cart =  Cart::orderBy('product_id','ASC')->where(array('product_id'=> $id,'customer_id'=>  $current_user->id))->update(array('quantity'=>$request->input('quantity')));
        $response = $this->getProductByID($id,$current_user->id);
        
        $havetoremove=Cart::where('quantity','<',0)->delete();
        if($havetoremove){
            return response()->json(array(
                'message'=>'Product removed from cart'
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
         if(isset($_GET['variant_id']))
         {
               $response = $this->getProductByID($id,$current_user->id,$_GET['variant_id']);
                if($response->original['product_id']==0)
                {
                    return response()->json('No product with that id',500);
                }else
                {
                    $cart =  Cart::orderBy('product_id','ASC')->where(array('product_id'=> $id,'customer_id'=>  $current_user->id,'variant_id'=>$_GET['variant_id']))->delete();
                  return response()->json(array("message"=>"Product removed"),200);
                }

         }else{
              $cart =  Cart::orderBy('product_id','ASC')->where(array('product_id'=> $id,'customer_id'=>  $current_user->id))->delete();
                 return response()->json(array("message"=>"Product removed"),200);
         }
           return response()->json(array("message"=>"Error"),500);
       
       
        
       
    }

}
