<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller
{
  // Login using social account like Facebook
  public function redirect($service)
  {
    return Socialite::driver($service)->redirect();
  }

  //Redirect after Login using social account like Facebook
  public function callback($service, Request $request)
  {
    $user = Socialite::driver($service)->stateless()->user();
    return response()->json($user);
  }
}
