<?php

namespace App\Http\Controllers;
use App\Models\Delivery;
use App\Models\Transport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class DeliveryController extends Controller
{
    //
     //get user delivery
     function getDelivery()
     {
         //get current user
         $current_user = auth()->user();

         $delivery = Delivery::orderBy('id','DESC')
         ->select('*')
         ->where('customer_id',$current_user->id)
         ->get();
        
         foreach($delivery as $del)
         {
             $del->transport =   Transport::orderBy('id','DESC')
             ->select('*')
             ->where('id',$del->transport)
             ->first();
         }
         return response()->json($delivery);
         
     }
      //get user delivery
      function getDeliveryType()
      {
         
          $tranports = Transport::orderBY('id','desc')->orderBy('id','DESC')
          ->select('*')
          ->get();
         
          return response()->json($transports);
          
      }
     //request delivery
     function addDelivery(Request $request)
     {
         //get current user
         $current_user = auth()->user();
          
         if($request->input('delivery_type')=="province")
         {
             if($request->has('transport')!="")
             {
                
                 $transport = Transport::orderBy('id','desc')->where('id',$request->input('transport'))->first();
                
                 if(DB::table('sma_transports')->where('id',$request->input('transport'))->exists())
                 {
                    $delivery =   Delivery::insert([
                        'customer_id'=>$current_user->id,
                        'pickup_name'=>$request->input('pickup_name'),
                        'pickup_phone'=>$request->input('pickup_phone'),
                        'pickup_location'=>$request->input('pickup_location'),
            
                        'receiver_location'=>$request->input('receiver_location'),
                        'receiver_name'=>$request->input('receiver_name'),
                        'receiver_phone'=>$request->input('receiver_phone'),
            
                        'note'=>$request->input('note'),
                        'packages'=>$request->input('packages'),
                        'weight'=>$request->input('weight'),   
                        'delivery_type' =>$request->input('delivery_type'),
        
                        'transport' => $transport->id,
                        'payment_method'=>$request->input('payment_method')
                            
                    ]); 
                    return response()->json(array('message'=>'Added'),201);
                 }
                 else{
                    return response()->json(array('message'=>'No transport found'),500);
                 }
             }else{
                return response()->json(array('message'=>'Please  provide Transport'),500);
             }
           
        
         }else if($request->input('delivery_type')=="normal")
         {
            $delivery =   Delivery::insert([
                'customer_id'=>$current_user->id,
                'pickup_name'=>$request->input('pickup_name'),
                'pickup_phone'=>$request->input('pickup_phone'),
                'pickup_location'=>$request->input('pickup_location'),
    
                'receiver_location'=>$request->input('receiver_location'),
                'receiver_name'=>$request->input('receiver_name'),
                'receiver_phone'=>$request->input('receiver_phone'),
    
                'note'=>$request->input('note'),
                'packages'=>$request->input('packages'),
                'weight'=>$request->input('weight'),   
                'delivery_type' =>$request->input('delivery_type'),

               
                'payment_method'=>$request->input('payment_method')
                    
            ]); 
         }else if($request->input('delivery_type')=="fast")
         {
            $delivery =   Delivery::insert([
                'customer_id'=>$current_user->id,
                'pickup_name'=>$request->input('pickup_name'),
                'pickup_phone'=>$request->input('pickup_phone'),
                'pickup_location'=>$request->input('pickup_location'),
    
                'receiver_location'=>$request->input('receiver_location'),
                'receiver_name'=>$request->input('receiver_name'),
                'receiver_phone'=>$request->input('receiver_phone'),
    
                'note'=>$request->input('note'),
                'packages'=>$request->input('packages'),
                'weight'=>$request->input('weight'),   
                'delivery_type' =>$request->input('delivery_type'),

                
                'payment_method'=>$request->input('payment_method')
                    
            ]); 
         }else{
            return response()->json(array('message'=>'Incorrect transport type'),500);
         }
         
        
        
        
         
     }
}
