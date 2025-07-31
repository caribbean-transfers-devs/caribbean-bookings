<?php

namespace App\Http\Controllers\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//REPOSITORY
use App\Repositories\Settings\DriverSchedulesRepository;

//TRAIT
use App\Traits\RoleTrait;

//MODELS
use App\Models\DriverSchedule;

class DriverSchedulesController extends Controller
{
    use RoleTrait;

    private $DriverSchedulesRepository;

    public function __construct(DriverSchedulesRepository $DriverSchedulesRepository)
    {
        $this->DriverSchedulesRepository = $DriverSchedulesRepository;
    }

    public function index(Request $request)
    {
        // if(!$this->hasPermission(108)){
        //     abort(403, 'NO TIENE AUTORIZACIÓN.');
        // }
        return $this->DriverSchedulesRepository->index($request);
    }

    public function create(Request $request)
    {
        // if(!$this->hasPermission(109)){
        //     abort(403, 'NO TIENE AUTORIZACIÓN.');
        // }
        return $this->DriverSchedulesRepository->create($request);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        // if(!$this->hasPermission(109)){
        //     abort(403, 'NO TIENE AUTORIZACIÓN.');
        // }
        return $this->DriverSchedulesRepository->store($request);
    }

    public function edit(Request $request, DriverSchedule $schedule)
    {
        // if(!$this->hasPermission(110)){
        //     abort(403, 'NO TIENE AUTORIZACIÓN.');
        // }
        return $this->DriverSchedulesRepository->edit($request, $schedule);
    }

    public function update(Request $request, DriverSchedule $schedule)
    {
        // if(!$this->hasPermission(110)){
        //     abort(403, 'NO TIENE AUTORIZACIÓN.');
        // }
        return $this->DriverSchedulesRepository->update($request, $schedule);
    }

    public function destroy(Request $request, DriverSchedule $schedule)
    {
        // if(!$this->hasPermission(111)){
        //     abort(403, 'NO TIENE AUTORIZACIÓN.');
        // }
        return $this->DriverSchedulesRepository->destroy($request, $schedule);
    }

    
    //ESTO NOS PERMITE ACTUALIZAR CIERTOS VALORES DE MANERA DIRECTA
    public function reloadSchedules(Request $request)
    {
        return $this->DriverSchedulesRepository->reloadSchedules($request);
    }

    public function timeCheckIn(Request $request)
    {
        return $this->DriverSchedulesRepository->timeCheckIn($request);
    }

    public function timeCheckout(Request $request)
    {
        return $this->DriverSchedulesRepository->timeCheckout($request);
    }
    
    public function unit(Request $request)
    {
        return $this->DriverSchedulesRepository->unit($request);
    }

    //ASIGNA CONDUCTOR
    public function setUnit(Request $request)
    {
        return $this->DriverSchedulesRepository->setUnit($request);
    }

    public function driver(Request $request)
    {
        return $this->DriverSchedulesRepository->driver($request);
    }

    //CAMBIA EL ESTATUS DEL CONDUCTOR
    public function statusDriver(Request $request)
    {
        return $this->DriverSchedulesRepository->statusDriver($request);
    }

    public function comments(Request $request)
    {
        return $this->DriverSchedulesRepository->comments($request);
    }

    public function status(Request $request)
    {
        return $this->DriverSchedulesRepository->status($request);
    }

    //ESTA ACCION LA REALIZA EL BOT
    public function botSchedules(Request $request)
    {
        return $this->DriverSchedulesRepository->botSchedules($request);
    }

    public function processSchedulesForToday(Request $request)
    {
        return $this->DriverSchedulesRepository->processSchedulesForToday($request);
    }    
}
