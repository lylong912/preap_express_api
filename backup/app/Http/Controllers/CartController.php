<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
 
class CartController extends Controller
{
    //
    public function getCart()
    {
       
        $carts = Product::orderBy('product_id','DESC')
        ->select('sma_products.*','sma_cart.id as cart_id','sma_cart.quantity as quantity')
        ->join('sma_cart', 'sma_products.id', '=', 'sma_cart.product_id')
        ->with(['Photos'])
        ->where('sma_cart.customer_id', '1')
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
           "noted"=>"dfd"
       );
       $details['data'] = array(
    );
       foreach($carts as $cart)
       {
       $product = array(
           'id'=> $cart->id,
           'name'=> $cart->name,
           'price'=> $cart->price,
           'image'=> $cart->image,
           'photos'=> $cart->photos,
           'category_id'=>$cart->category_id,
           'subcategory_id'=>$cart->subcategory_id,
           'product_details'=>$cart->product_details,
           'quantity'=>$cart->quantity,
           'grand_total'=> $cart->price * $cart->quantity,
           );

           array_push($details['data'],$product);
          
       }

       
        return response()->json($details);
    }
    public function getProductByID($id,$customer_id)
    {
       
        $carts = Product::orderBy('product_id','DESC')
        ->select('sma_products.*','sma_cart.id as cart_id','sma_cart.quantity as quantity')
        ->join('sma_cart', 'sma_products.id', '=', 'sma_cart.product_id')
        ->with(['Photos'])
        ->where('sma_cart.customer_id', $customer_id)
        ->where('sma_cart.product_id', $id)
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
   
        return response()->json($details);
    }
    public function addToCart(Request $request)
    {
    
        $cart = Cart::updateOrCreate(array('customer_id' => $request->input('customer_id'),'product_id' => $request->input('product_id')));
        $cart->quantity += $request->input('quantity');
        $cart->save();
        $response = $this->getProductByID($request->input('product_id'),$request->input('customer_id'));
        return response()->json($response->original);
    }

    public function editCart(Request $request,$id)
    {
    
        $cart =  Cart::orderBy('product_id','ASC')->where(array('product_id'=> $id,'customer_id'=> $request->input('customer_id')))->update(array('quantity'=>$request->input('quantity')));
        $response = $this->getProductByID($id,$request->input('customer_id'));
        
        $havetoremove=Cart::where('quantity','<',0)->delete();
        if($havetoremove){
            return response()->json(array(
                'message'=>'Product removed from cart'
            ));
        }

            return response()->json($response->original);
        
       
    }

}
