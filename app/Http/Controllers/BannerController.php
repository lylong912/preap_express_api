<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
class BannerController extends Controller
{
    
    public function getBanners()
    {
        
        $banners = Banner::orderBy('id','DESC')->select('*')->where('status','Show')
        ->get();
        for($i=0;$i<count($banners);$i++)
        {
            $banners[$i]['image'] = $this->server.$banners[$i]['image'];  
        }
       
        $details = array(
        );
        $details['data'] = $banners;
        return response()->json($details);
    }
}
