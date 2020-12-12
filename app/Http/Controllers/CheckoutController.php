<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\Checkout;
use App\Models\Product;
use App\Models\User;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;
use App\Models\Photo_Photo;
class CheckoutController extends Controller
{
    
        function getCheckout()
        {
            $verify = $this->authverify();
            if(!$verify)
            {
                return response()->json(['message'=>'please verified'],401);
            }
            $server = "http://mx.anakutjobs.com/assets/uploads/";
            //get current user
            $current_user =  auth()->user();
            $checkout = DB::table('sma_checkout')->orderBy('id','DESC')->where('customer_id',$current_user->id)->get();
        
            return response()->json($checkout);
         }
         
          function getCheckoutDetails($id)
        {
            $verify = $this->authverify();
            if(!$verify)
            {
                return response()->json(['message'=>'please verified'],401);
            }
            //get current user
            $current_user =  auth()->user();
            $checkout = Checkout::orderBy('id','DESC')->select('*')
            ->with(['data'])
            ->where('customer_id','=', $current_user->id)
             ->where('id','=', $id)
            ->first();
            
            // foreach($checkout as $checkouts)
            // {
            //     for($i = 0;$i<count($checkout[0]->data);$i++)
            //     {
            //         $product_id = $checkout[0]->data[$i]['product_id'];
            //         $products = Product::orderBy('id','DESC')->with(['Photos'])->select('id','code','name','price','category_id'
            //         ,'subcategory_id','image','product_details')->where('id','=', $product_id)
            //         ->get();

                    
            //         foreach($products as $product)
            //         {
            //             //custom data for product
            //             $checkout[0]->data[$i]['grand_total'] = $checkout[0]->data[$i]['price']*$checkout[0]->data[$i]['quantity'];

            //             if($product->image!="")
            //         {
            //             $product->image = $this->server.$product->image;
            //         }
            //         }
                    
            //         // $checkout[0]->data[$i]->product = $products;
            //     }
            // }
               
                
            return response()->json($checkout);
            
           
         }

    public function getCart()
    {
        $verify = $this->authverify();
        if(!$verify)
        {
            return response()->json(['message'=>'please verified'],401);
        }
        //get current user
        $current_user =  auth()->user();
        $id=$current_user->id;
        $carts = Product::orderBy('product_id','DESC')
        ->select('sma_products.*','sma_cart.id as cart_id','sma_cart.quantity as quantity')
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
           "noted"=>"dfd"
       );
       $details['data'] = array(
    );
       foreach($carts as $cart)
       {
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
          
       $product = array(
           'id'=> $cart->id,
           'name'=> $cart->name,
           'price'=> $cart->price,
           'image'=> $cart->image,
           'photos'=> $cartphoto,
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

        public function Checkout(Request $request)
    {
         //get current time
        date_default_timezone_set('Asia/Phnom_Penh');
        $time = date("Y-m-d h:i:sa");

        $verify = $this->authverify();
        if(!$verify)
        {
            return response()->json(['message'=>'please verified'],401);
        }   
        //get current user
        $current_user =  auth()->user();
        // $cart = DB::table('sma_cart')->where('customer_id',$current_user->id)->get();
        $cart = Product::orderBy('product_id','DESC')
        ->select('sma_products.*','sma_cart.id as cart_id','sma_cart.quantity as quantity','sma_cart.variant_id as variant_id')
        ->join('sma_cart', 'sma_products.id', '=', 'sma_cart.product_id')
        ->with(['Photos'])
        ->where('sma_cart.customer_id', $current_user->id)
        ->get();
        $wordCount = $cart->count();
        if($cart->count()<=0)
        {
            return response()->json(array('message'=>'No items in cart'),500);
        }
        //get total
        $cartdetail = $this->getCart();
        
        //check address
        if(request()->has('address'))
        {
             $address = DB::table('address')->where(array('id'=>$request->input('address'),'customer_id'=>$current_user->id))
            ->get();
            //check if address exist
            if($address->count()<=0)
            {
                return response()->json(array('message'=>'No address found'),500);
            }
            else
            {
                 $checkout = DB::table('sma_checkout')
                 ->insertGetId(['customer_id' => $current_user->id, 'total'=>$cartdetail->original['total'],'date'=>$time,'address'=>$address]);
            }
        }
        else
        {
             $address = DB::table('address')->where(array('customer_id'=>$current_user->id,'default'=>'t'))->get();
            $checkout = DB::table('sma_checkout')->insertGetId(['customer_id' => $current_user->id, 'total'=>$cartdetail->original['total'],'date'=>$time,'address'=>$address]);
        }

       
        //get current checkout id
        $insertedId = $checkout;

        //add each items from cart to checkout_items
        foreach($cart as $data){
            //check if product has variant
            if($data->variant_id=="")
            {
                DB::table('sma_checkout_items')->insert(['customer_id'=> $current_user->id, 'checkout_id' => $insertedId, 'product_id'=>$data->id,'product_name'=>$data->name,'quantity'=>$data->quantity]);
            }else{
                $variant = DB::table('sma_product_variants')->where('id',$data->variant_id)->first();
                DB::table('sma_checkout_items')->insert(['customer_id'=> $current_user->id, 'checkout_id' => $insertedId, 'product_id'=>$data->variant_id,'product_name'=>$variant->name,'quantity'=>$data->quantity]);
            }
            
        }
        $havetoremove=Cart::orderBy('product_id','ASC')->where('customer_id',$current_user->id)->delete();
       
        //get checkout history
        $checkout = DB::table('sma_checkout')->orderBy('id','DESC')->where('customer_id',$current_user->id)->get();
        $address = DB::table('address')->orderBy('address.id','DESC')
        ->join('sma_checkout', 'address.id', '=', 'sma_checkout.address')
        ->select("address.*")
        ->where('address.customer_id',$current_user->id)
        ->first();
        foreach($checkout as $check )
        {
            $check->address = $address;
        }

        $user = User::orderBy('id','desc')->where('id',$current_user->id)->first();
        
            $email = $user->email;
            $to= explode(',', 'lylong.lylong912@gmail.com,software.anakutdigital@gmail.com');
             //checkout with email
            Mail::send([], [], function ($message) use ($email,$to,$insertedId) {
            $message->to($to)
                ->subject($email)
                ->from($email,'New Order #'.$insertedId)
                // here comes what you want
                // or:
                ->setBody('<h1>Hi, I have just made an order!</h1><p> Follow this link: https://mx.anakutjobs.com', 'text/html'); // for HTML rich messages
            });
        return response()->json(array('message'=>'Checkout completed'),201);
        // return response()->json($cart,201);
    }

       
       
}
