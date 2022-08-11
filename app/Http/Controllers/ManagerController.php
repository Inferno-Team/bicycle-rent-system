<?php

namespace App\Http\Controllers;

use App\Models\Bicycle;
use App\Models\BicycleStyle;
use App\Models\CurrentUser;
use App\Models\Esp32Chip;
use App\Models\Stand;
use App\Models\User;
use App\Models\UserBan;
use App\Models\UserHistory;
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
                    'message' => "bicycle style created sucesssfully.",
                    'style' => $style
                ], 200);
            else
                return response()->json([
                    'code' => 300,
                    'message' => "bicycle style can't be created.",
                    'style' => null
                ], 200);
        } else {
            return response()->json([
                'code' => 403,
                'message' => "you don't have access to this route.",
                'style' => null
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
            /* request [ file(image) , price_per_time, price_per_distance ,
                 style_id , stand_id , is_sport,esp_ip ]*/

            $esp32 = User::create([
                'email' => '',
                'password' => Hash::make(12345678),
                'type' => 'esp32',
                'ip' => $request->esp_ip
            ]);
            $generatedEmail = "esp32.$esp32->id@bi.com";
            $esp32->email = $generatedEmail;
            $esp32->save();
            $stand = Stand::find($request->stand_id);
            $style = BicycleStyle::find($request->style_id);
            $name = "bicycle$style->size-$style->color";
            $bicycle = Bicycle::create([
                'name' => $name,
                'lat' => $stand->lat,
                'long' => $stand->long,
                'price_per_time' => $request->price_per_time,
                'price_per_distance' => $request->price_per_distance,
                'style_id' => $request->style_id,
                'stand_id' => $request->stand_id,
                'esp32_id' => $esp32->id,
                'is_sport' => false,
                'is_available' => true,
            ]);

            if ($request->hasFile('image')) {
                $image = $request->file("image");
                $file_extention = $image->getClientOriginalExtension();
                $file_name = time() . '.' . $file_extention;  // 546165165.jpg
                $path = '/public/images';
                $image->storeAs($path, $file_name);
                $bicycle->img_url =  '/storage/images/' . $file_name;
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
            // request [ id , cause ]

            $user = User::where('id', $request->id)->first();
            if (!isset($user))
                return response()->json([
                    'code' => 300,
                    'message' => "user not found."
                ], 200);

            $ban = UserBan::create([
                'user_id' => $user->id,
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
        return response()->json(Bicycle::with('style', 'esp32')->get(), 200);
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

    public function recentEvents()
    {
        $user = Auth::user();
        if ($user->type == 'manager') {
            $current = CurrentUser::with('user', 'bicycle.style', 'bicycle.esp32', 'bicycle.stand')->get()
             ->map->format();
            $history = UserHistory::with([
                'user', 'bicycle.style', 'bicycle.esp32',
                'bicycle.stand',
                'old_stand', 'last_stand'
            ])->get()->map->format();
            return response()->json([
                'current' => $current,
                'history' => $history
            ], 200);
        } else {
            return response()->json([
                'current' => [],
                'history' => []
            ], 200);
        }
    }
    public function getUserHistory(Request $request)
    {

        $user = Auth::user();
        if ($user->type == 'manager') {
            $history = UserHistory::where('user_id', $request->userId)->with([
                'user', 'bicycle.style', 'bicycle.esp32',
                'bicycle.stand',
                'old_stand', 'last_stand'
            ])->get()->map->format();
            return response()->json($history, 200);
        } else return response()->json([], 403);
    }
    public function getStyles()
    {
        $user = Auth::user();
        if ($user->type == 'manager') {
            return response()->json(BicycleStyle::all(), 200);
        } else return response()->json([], 403);
    }
    public function deleteBick(Request $request)
    {
        $user = Auth::user();
        if ($user->type == 'manager') {
            $bicycle = Bicycle::where('id', $request->bid)->first();
            
            if (!isset($bicycle))
                return response()->json([
                    'code' => 300,
                    'message' => "bicycle not found."
                ], 200);
            $bicycle->delete();
            return response()->json([
                'code' => 200,
                'message' => "bicycle deleted successfully."
            ], 200);
        } else return response()->json([], 403);
    }
    public function getAllBannedUsers(){
        $user = Auth::user();
        if ($user->type == 'manager') {
            $users = UserBan::with('user')->get()->map->format();
            return response()->json($users, 200);
        } else return response()->json([], 403);
    }
}
