<?php
use App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These 
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


//******auth routes******
//login route
Route::post('login', 'App\Http\Controllers\UserController@index');

//login route 3rd party
Route::group(['middleware' => ['web']], function () {
// login with fb routes here
Route::get('login/facebook', 'App\Http\Controllers\UserController@redirectToProvider');
Route::get('login/facebook/callback', 'App\Http\Controllers\UserController@handleProviderCallback');

// login with gmail routes here
Route::get('login/gmail', 'App\Http\Controllers\UserController@redirectToProviderGmail');
Route::get('login/gmail/callback', 'App\Http\Controllers\UserController@handleProviderCallbackGmail');
});

//register route
Route::post('register', 'App\Http\Controllers\UserController@register');
//reset route
Route::post('reset', 'App\Http\Controllers\UserController@resetPassword');

//verify route
Route::post('verify', 'App\Http\Controllers\UserController@verify');
Route::post('login/gmail/verify', 'App\Http\Controllers\UserController@verifygmail');
Route::post('login/facebook/verify', 'App\Http\Controllers\UserController@verifyfacebook');

//resend otp route
Route::post('auth/resend', 'App\Http\Controllers\UserController@resendOtp');

//******public routes******

//location route
Route::get('provinces', 'App\Http\Controllers\LocationController@getProvince');
Route::get('communes/', 'App\Http\Controllers\LocationController@getCommune');
Route::get('districts/', 'App\Http\Controllers\LocationController@getDistrict');   
//product route
Route::get('products', 'App\Http\Controllers\ProductController@getProducts');
Route::get('products/{id}', 'App\Http\Controllers\ProductController@getProductsByID');
//productvariant route
Route::get('product/variants/{id}', 'App\Http\Controllers\ProductController@getProductVariants');
//Banner route
Route::get('banners', 'App\Http\Controllers\BannerController@getBanners');
//Category route
Route::get('categories', 'App\Http\Controllers\CategoryController@getCategories');
//Category route
Route::get('subcategories/{id}', 'App\Http\Controllers\SubCategoryController@getSubCategories');

//******user routes******
Route::group(['middleware'=>'auth:sanctum'],function()
{
//change phone or update phone
Route::put('auth/phone', 'App\Http\Controllers\UserController@changePhone');

//change password route
Route::put('auth/change', 'App\Http\Controllers\UserController@changePassword');
//user location route
Route::get('profile/location', 'App\Http\Controllers\UserController@getLocation');
Route::delete('profile/location/delete/{id}', 'App\Http\Controllers\UserController@removeLocation');
Route::post('profile/location/add', 'App\Http\Controllers\UserController@addLocation');

//pick address
Route::post('profile/location/{id}', 'App\Http\Controllers\UserController@selectAddress');

//user route
Route::get('profile', 'App\Http\Controllers\UserController@getProfile');
Route::put('profile/update', 'App\Http\Controllers\UserController@updateProfile');
//checkout route
Route::post('checkout', 'App\Http\Controllers\CheckoutController@Checkout');
Route::get('checkout/history', 'App\Http\Controllers\CheckoutController@getCheckout');
Route::get('checkout/history/detail/{id}', 'App\Http\Controllers\CheckoutController@getCheckoutDetails');

//cart route
Route::put('cart/update/{id}', 'App\Http\Controllers\CartController@updateCart');
Route::get('cart', 'App\Http\Controllers\CartController@getCart');
Route::post('cart/add', 'App\Http\Controllers\CartController@addToCart');
Route::put('cart/edit/{id}', 'App\Http\Controllers\CartController@editCart');
Route::delete('cart/remove/{id}', 'App\Http\Controllers\CartController@removeProduct');

//wishlist route
Route::put('wishlist/update/{id}', 'App\Http\Controllers\WishlistController@updateWishlist');
Route::get('wishlist', 'App\Http\Controllers\WishlistController@getWishlist');
Route::post('wishlist/add', 'App\Http\Controllers\WishlistController@addToWishlist');
Route::put('wishlist/edit/{id}', 'App\Http\Controllers\WishlistController@editWishlist');
Route::delete('wishlist/remove/{id}', 'App\Http\Controllers\WishlistController@removeProduct');
Route::post('wishlist/cart', 'App\Http\Controllers\WishlistController@moveToCart');


//delivery
Route::get('delivery/history', 'App\Http\Controllers\DeliveryController@getDelivery');
Route::get('delivery', 'App\Http\Controllers\DeliveryController@getDeliveryType');
Route::post('delivery/add', 'App\Http\Controllers\DeliveryController@addDelivery');

Route::apiResource("menber",'App\Http\Controllers\MenberController@index');

});

