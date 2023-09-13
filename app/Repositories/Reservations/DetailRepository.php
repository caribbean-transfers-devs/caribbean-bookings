<?php

namespace App\Repositories\Reservations;

use App\Models\Reservation;
use App\Models\SalesType;
use App\Models\User;
use App\Models\UserRole;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class DetailRepository
{
    public function detail($request,$id)
    {
        $users_ids = UserRole::where('role_id', 3)->orWhere('role_id',4)->pluck('user_id');
        $sellers = User::whereIn('id', $users_ids)->get();
        
        $sales_types = SalesType::all();

        $reservation = Reservation::with('destination','items','sales','payments','followUps','site')->find($id);
        return view('reservations.detail', compact('reservation','sellers','sales_types'));
    }
}