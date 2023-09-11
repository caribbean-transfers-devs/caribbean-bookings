<?php
namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ReservationsItem;

class DashboardController extends Controller
{   
    public function index(){
        //Aquí debemos verificar cual es el Dashboard principal asignado al usuario y lo mostramos...
        $first_day = date('Y-m-01');
        $last_day = date('Y-m-t');
        $first_day_last_month = date('Y-m-01',strtotime("-1 month"));
        $last_day_last_month = date('Y-m-t',strtotime("-1 month"));
        $now = date('Y-m-d');
        $one_services_today = ReservationsItem::whereDate('op_one_pickup',$now)->with('reservations')->get();
        $two_services_today = ReservationsItem::whereDate('op_two_pickup',$now)->with('reservations')->get();
        $general_services = ReservationsItem::whereBetween('op_one_pickup', [$first_day, $last_day])
        ->orWhereBetween('op_two_pickup', [$first_day, $last_day])->count();
        $last_month_general_services = ReservationsItem::whereBetween('op_one_pickup', [$first_day_last_month, $last_day_last_month])
        ->orWhereBetween('op_two_pickup', [$first_day_last_month, $last_day_last_month])->count();
        return view('dashboard.default',compact('one_services_today','two_services_today','general_services','last_month_general_services'));
    }
}