<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Twilio\Rest\Client;
use Laravel\Socialite\Facades\Socialite;
/**
 * @OA\Post(
 * path="/api/login",
 * summary="Sign in",
 * description="Login by email, password",
 * operationId="authLogin",
 * tags={"auth"},
 * @OA\RequestBody(
 *    required=true,
 *    description="Pass user credentials",
 *    @OA\JsonContent(
 *       required={"email","password"},
 *       @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
 *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
 *    ),
 * ),
 * @OA\Response(
 *    response=201,
 *    description="Wrong credentials response",
 *    @OA\JsonContent(
 *       @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
 *        )
 *     )
 * )
 */
    
class UserController extends Controller
{
    
    function index(Request $request)
    {
        $user = User::where('phone', $request->phone)->first();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            $response = array(
                'Message'=>'Íncorrect password or username',
                    
            );
            return response($response,401);
            }
        $token = $user->createToken($request->phone)->plainTextToken;
        $response = array(
                'user'=>$user,
                'token'=>$token
            );
        
        return response($response,200);
    
    }
    function register(Request $request)
    {
        //get verified time
        date_default_timezone_set('Asia/Phnom_Penh');
        $otp_time = date("Y-m-d h:i:sa");

        //close for a period
        
        //generate random otp
        // $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        //     $pin = mt_rand(1, 9)
        // . mt_rand(10, 99)
        // . $characters[rand(0, strlen($characters) - 1)];
        //     $pin = mt_rand(1, 9)
        // . mt_rand(10, 99)
        // . $characters[rand(0, strlen($characters) - 1)];

        //custom pin
        $pin =1234;
        //insert data into db
        $user = User::where('phone', $request->phone)->first();
        if(User::where('phone', $request->phone)->first())
        {
            return response()->json(array('message'=>'User has already registered'),400);
        }
        $user_register =   DB::table('users')->insert([
            'firstname'=>$request->input('firstname'),
            'lastname'=>$request->input('lastname'),
            'name'=>$request->input('name'),
            'email'=>$request->input('email'),
            'phone'=>$request->input('phone'),
            'otp_time'=>$otp_time,
            'verify_status'=>'completed',
            'password'=>Hash::make($request->input('password')),
            'otp'=>Hash::make($pin)
        ]);
       
            
            // $message = 'MX Agriculture - '.$request->input('firstname')." ".$request->input('lastname').' here is your code: '.$pin;
            // $recipients = $request->input('phone');
            // $account_sid = getenv("TWILIO_SID");
            // $auth_token = getenv("TWILIO_AUTH_TOKEN");
            // $twilio_number = getenv("TWILIO_NUMBER");
            // $client = new Client($account_sid, $auth_token);
            // $client->messages->create($recipients, 
            //         ['from' => $twilio_number, 'body' => $message] );
            // return response($pin);
                    // return response(array("message"=>"Register success, please enter the otp that sent to your mobile number"),201);
            
            $token = $user->createToken($request->phone)->plainTextToken;
            $response = array(
                'user'=>$userregister,
                'token'=>$token
            );
        
            return response($response,201);
    
        }

    function verify(Request $request)
    {
        $user =  User::orderBy('id','DESC')->select('otp')->where(array(
            'verify_status'=>'pending',
            'phone'=>$request->input('phone')))->limit(1)->get();
            if($user->isEmpty())
            {
                return response(array("message"=>"User has already verified"),401);
            }
            if (! $user || ! Hash::check($request->input('otp'), $user[0]->otp)) {
                return response(array("message"=>"Verify failed"),401);
            }
    
            $user =  DB::table('users')->orderBy('id','DESC')
            ->where('phone',$request->input('phone'))
            ->update(array(
            'verify_status'=>'completed'));
        
       return response(array("message"=>"Verify success"),201);
    }
    
    function changePassword(Request $request)
    {
       //get current user
       $current_user =  auth()->user();
       $user = User::where('id', $current_user->id)->first();
         
        if (!(Hash::check($request->input('current_password'),$user->password))) {
            // The passwords matches
            return response(array("message"=>"Current password is not correct"),401);
        }
        else if(strcmp($request->input('current_password'), $request->input('new_password')) == 0){
            //Current password and new password are same
            return response(array("message"=>"Current password and new password are the same"),401);
        }else{
            $user_update =   User::orderBy('id','DESC')->where('id',$current_user->id)
            ->update([
                'password'=>Hash::make($request->input('new_password'))
            ]);
           
        }
        //delete all tokens
        $tokens = DB::table('personal_access_tokens')->where('tokenable_id',$current_user->id)->delete();
        // //create token
        $token = $user->createToken($user->phone)->plainTextToken;

        return response(array("message"=>"Password changed","token"=>$token),200);
    }

    function resetPassword(Request $request)
    {
            $user =   DB::table('users')->where('phone',$request->input('phone'))->first();
            if($user->phone=="")
            {
                  return response(array("message"=>"Unknown Phone nb"),500);
            }
            //generate pin
            $pin =1234;
            //date
            date_default_timezone_set('Asia/Phnom_Penh');
            $otp_time = date("Y-m-d h:i:sa");

            $user =   DB::table('users')->where('phone',$request->input('phone'))
            ->update([
                'verify_status'=>'pending',
                'otp'=>Hash::make($pin),
                'otp_time'=>$otp_time
            ]);
            // $user =   DB::table('users')->where('phone',$request->input('phone'))
            // ->update([
                
            //     'password'=>Hash::make($request->input('password'))
            // ]);
                return response(array("message"=>"Please verify otp"),200);
    }

        
        public function sendMessage()
    {
        $message = $_GET['code'];
        $recipients = "+85585318006";
        $account_sid = getenv("TWILIO_SID");
        $auth_token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_number = getenv("TWILIO_NUMBER");
        $client = new Client($account_sid, $auth_token);
        $client->messages->create($recipients, 
                ['from' => $twilio_number, 'body' => $message] );
        return $client;
    }

        function loginWithFB()
    {
        $token="AQDuHjLxtyfxNng0pf1FBmK4g1TrlJcVE2CevqVSYioGr5KUoNOX-3koBni1p7JWYmAsTCTHdbM4u7FeF_DmlGe7d-pFVIm8n_87LINxwS_ki7R8KARmW-xDkRurj1WQRSiL7WFSZDoMruuGnwH3Dnxz1TTCh-UqxEt1xcZayRmcEbzl8yD1l9HxTZjEy9OwrEIHOQNUupbRo0PrmMN52XTtFphS6Jl9JuYaiPlaxDHmrkZv7bYJSD1JF1Te4AlzxR64I6aSKow5wD9TSNnHeNT-k3_glMPtZpWxe_u-MPudFoKxLB41GjLHBTe0Z_JJTh7i3sForZi__qIELUhtOcwK";
        $user = Socialite::driver('facebook')->userFromToken($token);
        return $user;
        // $user->token;
    }

        function redirectToProvider()
    {
        return Socialite::driver('facebook')->redirect();
    }

   
        function handleProviderCallback()
    {
       
            $user = Socialite::driver('facebook')->user();
                
            // OAuth Two Providers
            $token = $user->token;
            $refreshToken = $user->refreshToken; // not always provided
            $expiresIn = $user->expiresIn;

            // OAuth One Providers
            $token = $user->token;
            // All Provider
            $name = $user->getName();
            $email = $user->getEmail();
            // $phone = $user->getPhone();
          
                $user = User::where('email', $email)->first();
                if($user==null)
                {
                    $user =   DB::table('users')->insert([
                        'name'=>$name,
                        'email'=>$email,
                        'login_type'=>'facebook',
                        // 'phone'=>$phone,
                        'verify_status'=>'completed',    
                    ]);

                    $user = User::where('email', $email)->first();
                    $token = $user->createToken($email)->plainTextToken;
                    $response = array(
                        'user'=>$user,
                       
                        'token'=>$token
                    );
                    return response()->json($response,200);
                }
          
            return response()->json("Error",500);
    }

    public function redirectToProviderGmail()
    {
        return Socialite::driver('google')->redirect();
    }
    public function handleProviderCallbackGmailweb()
    {
        try {
            $user = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return response('Error ',403);
        }
        // only allow people with @company.com to login
        // if(explode("@", $user->email)[1] !== 'company.com'){
        //     return redirect()->to('/');
        // }
        // check if they're an existing user
        $existingUser = User::where('email', $user->email)->first();
        if($existingUser){
            // log them in
            auth()->login($existingUser, true);
        } else {
            // create a new user
            $newUser                  = new User;
            $newUser->name            = $user->name;
            $newUser->email           = $user->email;
            // $newUser->google_id       = $user->id;
            // $newUser->avatar          = $user->avatar;
            // $newUser->avatar_original = $user->avatar_original;
            $newUser->save();
            $token = $newUser->createToken($newUser->email)->plainTextToken;
            $response = array(
                'user'=>$newUser,
            
                'token'=>$token
            );
            return response()->json($response,200);
            // auth()->login($newUser, true);
            return response()->json($newUser);
        }
        
    }
    public function handleProviderCallbackGmail()
    {
        $user = Socialite::driver('google')->user();
        $user = Socialite::driver('google')->userFromToken($user->token);
       return response()->json($user);
    }
    public function verifygmail(Request $request)
    {
        $token = $request->input('token');
        $user = Socialite::driver('google')->userFromToken($token);
        //field
        $name=$user->name;
        $email = $user->email;
        $lastname = $user->user['given_name'];
        $firstname = $user->user['family_name'];
        $existingUser = User::where('email', $email)->first();
        if($existingUser){
            $token = $existingUser->createToken($email)->plainTextToken;
            $response = array(
                'user'=>$existingUser,
                'token'=>$token
            );
             return response()->json($response);
        } else {
            // create a new user
            $newUser                  = new User;
            $newUser->name            = $user->name;
            $newUser->email           = $user->email;
            $newUser->firstname      = $firstname;
            $newUser->lastname         = $lastname;
             $newUser->login_type         = 'gmail';
            // $newUser->google_id       = $user->id;
            // $newUser->avatar          = $user->avatar;
            // $newUser->avatar_original = $user->avatar_original;
            $newUser->save();
            $token = $newUser->createToken($newUser->email)->plainTextToken;
            $response = array(
                'user'=>$newUser,
                'token'=>$token
            );
            return response()->json($response);
        }
        return response()->json(array('message'=>'Invalid token'),401);
    }

 public function verifyfacebook(Request $request)
    {
        $token = $request->input('token');
        $user = Socialite::driver('facebook')->userFromToken($token);
        //field
        $name=$user->name;
        $email = $user->email;
        
        $existingUser = User::where('email', $email)->first();
        if($existingUser){
            $token = $existingUser->createToken($email)->plainTextToken;
            $response = array(
                'user'=>$existingUser,
                'token'=>$token
            );
             return response()->json($response);
        } else {
            // create a new user
            $newUser                  = new User;
            $newUser->name            = $user->name;
            $newUser->email           = $user->email;
              $newUser->login_type         = 'facebook';
            // $newUser->google_id       = $user->id;
            // $newUser->avatar          = $user->avatar;
            // $newUser->avatar_original = $user->avatar_original;
            $newUser->save();
            $token = $newUser->createToken($newUser->email)->plainTextToken;
            $response = array(
                'user'=>$newUser,
                'token'=>$token
            );
            return response()->json($response);
        }
        return response()->json(array('message'=>'Invalid token'),401);
    }

    //get user profile
    function getProfile()
    {
        //get current user
        $current_user = auth()->user();
        $user = User::orderBy('id','DESC')
        ->select('*')
        ->where('id',$current_user->id)
        ->first();
        $address = User::orderBy('users.id','DESC')
        ->join('address', 'users.address', '=', 'address.id')
        ->select("address.*")
        ->where('users.id',$current_user->id)
        ->first();
        $user->address = $address;
        return response()->json($user);
        
    }
      //get user profile
    function getLocation()
    {
        //get current user
        $current_user = auth()->user();
        $user = DB::table('address')->orderBy('id','DESC')
        ->select('*')
        ->where('customer_id',$current_user->id)
        ->get();
        return response($user,200);
    }
       //get user profile
    function addLocation(Request $request)
    {   
        //get current user
        $current_user = auth()->user();
        $location = DB::table('address')->where('customer_id',$current_user->id)
        ->get();
        if(DB::table('address')->where('customer_id',$current_user->id)
        ->exists())
        {
             $location =   DB::table('address')->insert([
                        'name'=>$request->input('name'),
                        'customer_id'=>$current_user->id,
                        // 'phone'=>$phone,
                        'lat'=>$request->input('lat'),    
                        'long'=>$request->input('long'),  
                        'description'=>$request->input('description'),
                            
                    ]); 
                    $get = $this->getLocation();
        }else{
                $location =   DB::table('address')->insertGetId([
                        'name'=>$request->input('name'),
                        'customer_id'=>$current_user->id,
                        // 'phone'=>$phone,
                        'lat'=>$request->input('lat'),    
                        'long'=>$request->input('long'),  
                        'description'=>$request->input('description'),
                        'default'=>'t'    
                    ]); 
                    $get = $this->getLocation();

                    //update user location
                     //update to profile
                    $profile = DB::table('users')
                    ->where('id',$current_user->id)
                    ->update(array('address'=>$location));
        }
        
        return response()->json(array('message'=>'Created'),201);
    }
    //get user profile
    function updateProfile(Request $request)
    {
        //get current user
        $current_user = auth()->user();
        $user =   User::orderBy('id','desc')->where('id',$current_user->id)
            ->update([
                'firstname'=>$request->input('firstname'),
                'lastname'=>$request->input('lastname'),
                'name'=>$request->input('name'),
                'address'=>$request->input('address')
            ]);
        $updatedUser = DB::table('users')->where('id',$current_user->id)->get();
        return response()->json($updatedUser,201);
        
    }
    function resendOtp(Request $request)
    {
        //generate random otp
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';

        date_default_timezone_set('Asia/Phnom_Penh');
        $otp_time = date("Y-m-d h:i:sa");

        //generate random pin
        $pin = mt_rand(1, 9)
        . mt_rand(10, 99)
        . $characters[rand(0, strlen($characters) - 1)];

            $users = DB::table('users')->where('phone',$request->input('phone'))->limit(1)->get();
            foreach($users as $user)
            {
                $status = $user->verify_status;
                if($status=='completed')
                    {
                        return response()->json(array('message'=>'User has already verified.'),500);
                    }
            }
     

        //custom pin

        $user =  User::where('phone', $request->input('phone'))
        ->update([
           'otp_time' => $otp_time,
           'otp'=>Hash::make($pin),
        ]);
      
           
            $message = 'MX Agriculture: here is your code: '.$pin;
            $recipients = $request->input('phone');
            $account_sid = getenv("TWILIO_SID");
            $auth_token = getenv("TWILIO_AUTH_TOKEN");
            $twilio_number = getenv("TWILIO_NUMBER");
            $client = new Client($account_sid, $auth_token);
            $client->messages->create($recipients, 
                    ['from' => $twilio_number, 'body' => $message] );
            return response($pin);
            return response(array("message"=>"Register success, please enter the otp that sent to your mobile number"),201);
           
            $user = User::where('phone', $request->phone)->first();
            $token = $user->createToken($request->phone)->plainTextToken;
            $response = array(
                'user'=>$user,
                'token'=>$token
            );
        
            return response($response,201);
    }
     function changePhone(Request $request)
    {
        //update user phone
        $current_user = auth()->user();
        $user =   User::orderBy('id','desc')->where('id',$current_user->id)
            ->update([
                'phone'=>$request->input('phone'),
                'verify_status'=>'pending',
            ]);
        //generate time
        date_default_timezone_set('Asia/Phnom_Penh');
        $otp_time = date("Y-m-d h:i:sa");
        //generate random otp
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pin = mt_rand(1, 9)
        . mt_rand(10, 99)
        . $characters[rand(0, strlen($characters) - 1)];

        //custom pin
        $pin = 1234;
      
        $user =  User::where('phone', $request->input('phone'))
            ->update([
            'otp_time' => $otp_time,
            'otp'=>Hash::make($pin),
            ]);
      
           
            // $message = 'MX Agriculture: here is your code: '.$pin;
            // $recipients = $request->input('phone');
            // $account_sid = getenv("TWILIO_SID");
            // $auth_token = getenv("TWILIO_AUTH_TOKEN");
            // $twilio_number = getenv("TWILIO_NUMBER");
            // $client = new Client($account_sid, $auth_token);
            // $client->messages->create($recipients, 
            //         ['from' => $twilio_number, 'body' => $message] );
            // // return response($pin);
            //         // return response(array("message"=>"Register success, please enter the otp that sent to your mobile number"),201);
            $user = User::where('phone', $request->phone)->first();
            $token = $user->createToken($request->phone)->plainTextToken;
            $response = array(
                'user'=>$user,
                'token'=>$token
            );
        
            return response($response,200);
        
    }
    function selectAddress($id)
    {
        //get current user
        $current_user = auth()->user();
        //check is address exist
        $address = DB::table('address')->select('*')
        ->where(array('customer_id'=>$current_user->id,'id'=>$id))->get();
        
        foreach($address as $adr)
        {
                if($adr->id==null)
            {
                return response()->json(array('messsage'=>'No address found'),500);
            }
        }
        
        //change all to false
        $address = DB::table('address')
        ->where('customer_id',$current_user->id)
        ->update(array('default'=>'f'));

        //change one to true
        $address = DB::table('address')
        ->where(array('customer_id'=>$current_user->id,'id'=>$id))
        ->update(array('default'=>'t'));
        
        //update to profile
         $profile = User::orderBy('id','desc')
        ->where('id',$current_user->id)
        ->update(array('address'=>$id));

        $address = DB::table('address')->select('*')
        ->where('customer_id',$current_user->id)->get();
    
        return response()->json(array('message'=>'Address selected'),201);


    }
}