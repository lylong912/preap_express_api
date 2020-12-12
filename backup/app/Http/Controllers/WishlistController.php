<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Models\Product;
class WishlistController extends Controller
{
    //
    public function getWishlist()
    {
       
        $wishlists = Product::orderBy('product_id','DESC')
        ->select('sma_products.*','sma_wishlist.id as wishlist_id','sma_wishlist.quantity as quantity')
        ->join('sma_wishlist', 'sma_products.id', '=', 'sma_wishlist.product_id')
        ->with(['Photos'])
        ->where('sma_wishlist.customer_id', '1')
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
        $product = array(
            'id'=> $wishlist->id,
            'name'=> $wishlist->name,
            'price'=> $wishlist->price,
            'image'=> $wishlist->image,
            'photos'=> $wishlist->photos,
            'category_id'=>$wishlist->category_id,
            'subcategory_id'=>$wishlist->subcategory_id,
            'product_details'=>$wishlist->product_details,
            'quantity'=>$wishlist->quantity,
            'grand_total'=> $wishlist->price * $wishlist->quantity,
            );
 
            array_push($details['data'],$product);
           
        }
 
       
        return response()->json($details);
    }
}
