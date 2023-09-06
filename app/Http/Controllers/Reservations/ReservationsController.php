<?php
namespace App\Http\Controllers\Reservations;

use App\Http\Controllers\Controller;
use App\Repositories\Reservations\DetailRepository;
use Illuminate\Http\Request;

class ReservationsController extends Controller
{
    public function detail(Request $request, DetailRepository $detailRepository)
    {
        return $detailRepository->detail($request);
    }
}