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
        $details = array(
        );
        $details['data'] = $provinces;
        return response()->json($details);
    }
    public function getDistrict()
    {
       $ProID = $_GET['ProID'];
        $districts = District::orderBy('DisID','ASC')->select('*')
        ->where('ProId', $ProID)
        ->get();
        $details = array(
        );
        $details['data'] = $districts;
        return response()->json($details);
    }
    public function getCommune()
    {
       $DisID = $_GET['DisID'];
        $communes = Commune::orderBy('CommuneId','ASC')->select('*')
        ->where('District', $DisID)
        ->get();
       
        $details = array(
        );
        $details['data'] = $communes;
        return response()->json($details);
    }
}
