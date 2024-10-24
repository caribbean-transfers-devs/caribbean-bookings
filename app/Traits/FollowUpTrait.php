<?php

namespace App\Traits;
use Illuminate\Http\Response;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

//MODELS
use App\Models\ReservationFollowUp;

trait FollowUpTrait
{
    public function follow_ups($request)
    {
        $check = $this->create_followUps($request->reservation_id, $request->text, $request->type, $request->name);
        if($check){
            return response()->json(['message' => 'Follow up created successfully','success' => true], Response::HTTP_OK);
        }else{
            return response()->json(['message' => 'Error creating follow up','success' => false], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function create_followUps($reservation_id, $text, $type, $name = null)
    {
        $follow_up = new ReservationFollowUp();
        $follow_up->reservation_id = $reservation_id;
        $follow_up->text = $text;
        $follow_up->type = $type;
        $follow_up->name = $name;
        $follow_up->save();

        return $follow_up->id;
    }    
}