<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Province;
use App\Models\Commune;
use App\Models\District;
class LocationController extends Controller
{
    //
    public function getProvince()
    {
       
        $provinces = Province::orderBy('Proid','ASC')->select('*')
        ->get();
       
        return response()->json($provinces);
    }
    public function getDistrict($ProID)
    {
       
        $districts = District::orderBy('DisID','ASC')->select('*')
        ->where('ProId', $ProID)
        ->get();
       
        return response()->json($districts);
    }
    public function getCommune($DisID)
    {
       
        $communes = Commune::orderBy('CommuneId','ASC')->select('*')
        ->where('District', $DisID)
        ->get();
       
        return response()->json($communes);
    }
}
