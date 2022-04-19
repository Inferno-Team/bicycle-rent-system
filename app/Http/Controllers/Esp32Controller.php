<?php

namespace App\Http\Controllers;

use App\Models\Bicycle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Esp32Controller extends Controller
{
    public function updateLocation(Request $request)
    {
        $user = Auth::user();
        if ($user->type == 'esp32') {
            // request [ lat , long ]
            $bicycle = Bicycle::where('esp32_id', $user->id)->first();
            if (!isset($bicycle)) {
                return response()->json([
                    'code' => 300,
                    'message' => "there is no bicycle assigned to this Esp32 yet."
                ], 200);
            }
            $bicycle->lat = $request->lat;
            $bicycle->long = $request->long;
            $bicycle->save();
            return response()->json([
                'code' => 200,
                'message' => "bicycle data updated."
            ], 200);
        } else {
            return response()->json([
                'code' => 403,
                'message' => "you don't have access to this route."
            ], 200);
        }
    }
}
