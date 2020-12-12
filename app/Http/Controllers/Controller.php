<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\User;
/**
 * @OA\Info(
 *    title="Your super  ApplicationAPI",
 *    version="1.0.0",
 * )
 */
class Controller extends BaseController
{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    //delcare variable
    protected $server="http://feelez.anakutjobs.com/assets/uploads/";

    function authverify()
    {
        $current_user =  auth()->user();
        $user = User::where('id', $current_user->id)->first();
        if($user->verify_status=="pending")
        {
            return false;
        }
         return true;
    }
}
