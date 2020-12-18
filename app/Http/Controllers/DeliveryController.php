<?php

namespace App\Http\Controllers;
use App\Models\Delivery;
use Illuminate\Http\Request;

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
        
         
         return response()->json($delivery);
         
     }
      //get user delivery
      function getDeliveryType()
      {
         
          $tranports = Table::table('sma_tranports')->orderBy('id','DESC')
          ->select('*')
          ->get();
         
          return response()->json($transports);
          
      }
     //request delivery
     function addDelivery(Request $request)
     {
         //get current user
         $current_user = auth()->user();
          
         if($request->input('delivery_type')=="fast"||$request->input('delivery_type')=="normal"||$request->input('delivery_type')=="province")
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
                'delivery_type' =>$request->input('delivery_type')
                    
            ]); 
        
         }else{
             return response()->json(array('message'=>'Incorrect transport type'),500);
         }
         
        
        
         return response()->json(array('message'=>'Added'),201);
         
     }
}
