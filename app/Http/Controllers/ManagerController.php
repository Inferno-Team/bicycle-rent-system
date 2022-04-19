<?php

namespace App\Http\Controllers;

use App\Models\Bicycle;
use App\Models\BicycleStyle;
use App\Models\Esp32Chip;
use App\Models\Stand;
use App\Models\User;
use App\Models\UserBan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ManagerController extends Controller
{
    public function createStyle(Request $request)
    {
        $user = Auth::user();
        if ($user->type == 'manager') {
            // request [name , color , size ]
            $style = BicycleStyle::create([
                'name' => $request->name,
                'color' => $request->color,
                'size' => $request->size,
            ]);
            if (isset($style))
                return response()->json([
                    'code' => 200,
                    'message' => "bicycle style created sucesssfully."
                ], 200);
            else
                return response()->json([
                    'code' => 300,
                    'message' => "bicycle style can't be created."
                ], 200);
        } else {
            return response()->json([
                'code' => 403,
                'message' => "you don't have access to this route."
            ], 200);
        }
    }

    public function createStand(Request $request)
    {
        $user = Auth::user();
        if ($user->type == 'manager') {
            // request [name , location , lat , long , bicycles count ]
            $stand = Stand::create([
                'name' => $request->name,
                'location' => $request->location,
                'lat' => $request->lat,
                'long' => $request->long,
                'bicycle_count' => $request->bicycle_count,
            ]);
            if (isset($stand))
                return response()->json([
                    'code' => 200,
                    'message' => 'stand created successfully',
                    'stand' => $stand
                ], 200);
            else return response()->json([
                'code' => 300,
                'message' => "stand can't be created"
            ], 200);
        } else {
            return response()->json([
                'code' => 403,
                'message' => "you don't have access to this route."
            ], 200);
        }
    }

    public function createBicycle(Request $request)
    {

        $user = Auth::user();
        if ($user->type == 'manager') {
            /* request [name , img_url , lat , long , price_per_time,
                                     price_per_distnace , style_id , stand_id , is_sport,
                                     esp32 email , esp32 password ,esp_ip ]*/
            $esp32 = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'type' => 'esp32',
                'ip' => $request->esp_ip
            ]);
            $bicycle = Bicycle::create([
                'name' => $request->name,
                'lat' => $request->lat,
                'long' => $request->long,
                'price_per_time' => $request->price_per_time,
                'price_per_distance' => $request->price_per_distance,
                'style_id' => $request->style_id,
                'stand_id' => $request->stand_id,
                'esp32_id' => $esp32->id,
                'is_sport' => $request->is_sport,
                'is_available' => true,
            ]);

            if ($request->hasFile('image')) {
                $image = $request->image;
                $file_extention = $image->getClientOriginalExtension();
                $file_name = time() . '.' . $file_extention;  // 546165165.jpg
                $path = 'images';
                $image->move($path, $file_name);
                $bicycle->img_url = $path . '/' . $file_name;
                $bicycle->save();
            }
            if (isset($bicycle) && isset($esp32))
                return response()->json([
                    'code' => 200,
                    'message' => "bicycle created successfully.",
                    'esp_account' => $esp32
                ], 200);
            else return response()->json([
                'code' => 300,
                'message' => "bicycle can't be created."
            ], 200);
        } else {
            return response()->json([
                'code' => 403,
                'message' => "you don't have access to this route."
            ], 200);
        }
    }

    public function banUser(Request $request)
    {
        $manager = Auth::user();
        if ($manager->type == 'manager') {
            // request [ user_id , cause ]
            $user = User::where('id', $request->id)->first();
            if (isset($user))
                return response()->json([
                    'code' => 300,
                    'message' => "user not found."
                ], 200);

            $ban = UserBan::create([
                'user_id' => $user->user_id,
                'cause' => $request->cause,
            ]);
            if (isset($ban))
                return response()->json([
                    'code' => 200,
                    'message' => "user band successfully."
                ], 200);
            else return response()->json([
                'code' => 200,
                'message' => "user can't be band now."
            ], 200);
        } else {
            return response()->json([
                'code' => 403,
                'message' => "you don't have access to this route."
            ], 200);
        }
    }
    public function getCurrentUser($id)
    {
        $user = Auth::user();
        if ($user->type == 'manager') {
            $bicycle = Bicycle::where('id', $id)
                ->with(['current_user'])->first();
            if (isset($bicycle)) {
                return response()->json([
                    'code' => 200,
                    'user' => $bicycle->current_user
                ], 200);
            } else {
                return response()->json([
                    'code' => 300,
                    'user' => null
                ], 200);
            }
        } else {
            return response()->json([
                'code' => 403,
                'message' => "you don't have access to this route."
            ], 200);
        }
    }
    public function getBicycleCurrentLocation($id)
    {
        $user = Auth::user();
        if ($user->type == 'manager') {
            $bicycle = Bicycle::where('id', $id)->first();
            if (!isset($bicycle)) {
                return response()->json([
                    'code' => 300,
                    'message' => "there is no bicycle with this id"
                ], 200);
            }
        } else {
            return response()->json([
                'code' => 403,
                'message' => "you don't have access to this route."
            ], 200);
        }
    }
    public function getAllBicycle()
    {
        return response()->json([
            'code' => 200,
            'bis' => Bicycle::with('style')->get()
        ], 200);
    }
    public function getAllStands()
    {
        return response()->json([
            'code' => 200,
            'stands' => Stand::get()
        ], 200);
    }
    public function getStandBicycle($id)
    {

        $bis = Bicycle::where('stand_id', $id)->with('style')->with('esp32')->get();
        return response()->json([
            'code' => 200,
            'bicycles' => $bis
        ], 200);
    }
    public function getAvaliableBicycleInStand($id)
    {
        $bis = Bicycle::where('stand_id', $id)->where('is_available', true)->with('style')->get();
        return response()->json([
            'code' => 200,
            'bicycles' => $bis
        ], 200);
    }
}
