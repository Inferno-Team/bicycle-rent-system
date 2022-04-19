<?php

namespace App\Http\Controllers;

use App\Models\Bicycle;
use App\Models\CurrentUser;
use App\Models\UserHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function rentBicycle(Request $request)
    {
        // request [ bi_id ]
        $user = Auth::user();
        if ($user->type == 'customer') {
            $bicycle = Bicycle::where('id', $request->bi_id)->first();
            if (isset($bicycle)) {
                if ($bicycle->is_available) {
                    //check if this user have already have rented a bicycle
                    $oldUserBicycle = CurrentUser::where('user_id', $user->id)->first();
                    if (!isset($oldUserBicycle)) {
                        $cu = CurrentUser::create([
                            'bicycle_id' => $bicycle->id,
                            'user_id' => $user->id,
                        ]);
                        if (isset($cu)) {
                            $bicycle->is_available = false;
                            $bicycle->save();
                            // send to esp 32 order to unlock this bick
                            return response()->json([
                                'code' => 200,
                                'message' => "you rented this bicycle now.",
                                'bicycle' => $bicycle
                            ], 200);
                        } else {
                            return response()->json([
                                'code' => 303,
                                'message' => "you can't rent this bicycle now.",
                                'bicycle' => $bicycle
                            ], 200);
                        }
                    } else {
                        return response()->json([
                            'code' => 302,
                            'message' => "you can't rent this bicycle because you have already rent a bicycle"
                        ], 200);
                    }
                } else {
                    return response()->json([
                        'code' => 301,
                        'message' => "this bicycle not available right now"
                    ], 200);
                }
            } else {
                return response()->json([
                    'code' => 300,
                    'message' => "bicycler not found"
                ], 200);
            }
        } else {
            return response()->json([
                'code' => 403,
                'message' => "you don't have access to this route."
            ], 200);
        }
    }

    public function returnBicycle(Request $request)
    {
        // request [ step_count , time_count , last stand ]
        // step count came as m and bicycle step price saved as km so we need to convert m => km
        // time count came as min and bicycle time price saved as hour so we need to convert min => hour
        $user = Auth::user();
        if ($user->type == 'customer') {
            $cu = CurrentUser::where('user_id', $user->id)->first();
            if (!isset($cu)) {
                return response()->json([
                    'code' => 300,
                    'message' => "you don't have any rented bicycle to return."
                ], 200);
            } else {
                $bicycle = Bicycle::where("id", $cu->bicycle_id)->first();
                $timePrice = ($request->time_count / 60) * $bicycle->price_per_time; // min => hour
                $dictancePrice = ($request->step_count / 1000) * $bicycle->price_per_distance; // m => km
                $price = $timePrice + $dictancePrice;
                $history = UserHistory::create([
                    'user_id' => $user->id,
                    'bicycle_id' => $bicycle->id,
                    'price' => $price,
                    'distence' => $request->step_count,
                    'time' => $request->time_count,
                    'old_stand' => $bicycle->stand_id,
                    'last_stand' => $request->last_stand,
                ]);
                if (!isset($history)) {
                    return response()->json([
                        'code' => 301,
                        'message' => "something wrong happend please try again later.",
                    ], 200);
                }
                $cu->delete();
                $bicycle->is_available = true;
                $bicycle->stand_id = $request->last_stand;
                $bicycle->save();
                return response()->json([
                    'code' => 200,
                    'message' => "bicycle returned successfully",
                    'data' => $history
                ], 200);
            }
        } else {
            return response()->json([
                'code' => 403,
                'message' => "you don't have access to this route."
            ], 200);
        }
    }

    public function updateLocation(Request $request)
    {
        $user = Auth::user();
        if ($user->type == 'customer') {
            // request [ lat , long ]
            $cu = CurrentUser::where('user_id', $user->id)->first();
            if (!isset($$cu)) {
                return response()->json([
                    'code' => 300,
                    'message' => "there is no bicycle assigned to this Esp32 yet."
                ], 200);
            }
            $cu->lat = $request->lat;
            $cu->long = $request->long;
            $cu->save();
            return response()->json([
                'code' => 200,
                'message' => "current location data updated."
            ], 200);
        } else {
            return response()->json([
                'code' => 403,
                'message' => "you don't have access to this route."
            ], 200);
        }
    }
}
