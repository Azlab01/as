<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Presence;
use App\User;

class PresenceController extends Controller
{
    public function index (Request $request){
        if ($request->user_code) {
            $user_code = htmlspecialchars($request->user_code);
            $user =  User::where("code", $user_code)->first();
            if ($user) {

                $code = "POOLNUMERIQUE".date('y-m-d');
                
                if ($request->qr_code === $code) {
                    $date = date('y-m-d');
                    $userAlreadyLogin = Presence::where('user_id', $user->id)
                                            ->whereRaw("DATE(created_at) = '$date' ")
                                            ->first();

                    if($userAlreadyLogin){
                        $userAlreadyLogin->updated_at = now();
                        $userAlreadyLogin->save();
                        return response()->json(["res"=>"Au revoir"]);
                    }
                    $presence = new Presence;
                    $presence->user_id = $user->id;
                    $presence->save();
                    return response()->json(["res"=>"Bienvenue"]);
                }
                return response()->json(["res"=>"Qr Code erreur" ]);
            }
            return response()->json(["res"=>"Code d'enregistrement invalide"]);
        }
        return response()->json(["res"=>"echec"]);
    }
}
