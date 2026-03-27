<?php

namespace App\Repositories\Settings;

use Exception;
use Illuminate\Http\Response;

//MODELS
use App\Models\ScheduleRestriction;

//FACADES
use Illuminate\Support\Facades\DB;

class ScheduleRestrictionsRepository
{
    public function index($request)
    {
        try {
            return view('settings.schedule_restrictions.index', [
                'breadcrumbs' => [
                    [
                        'route'  => '',
                        'name'   => 'Listado de restricciones de horarios',
                        'active' => true,
                    ]
                ],
                'restrictions' => ScheduleRestriction::orderBy('start_at', 'desc')->get()
            ]);
        } catch (Exception $e) {
        }
    }

    public function create($request)
    {
        try {
            return view('settings.schedule_restrictions.new', [
                'breadcrumbs' => [
                    [
                        'route'  => route('config.schedule-restrictions.index'),
                        'name'   => 'Listado de restricciones de horarios',
                        'active' => false,
                    ],
                    [
                        'route'  => '',
                        'name'   => 'Nueva restricción de horario',
                        'active' => true,
                    ]
                ],
            ]);
        } catch (Exception $e) {
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $restriction = new ScheduleRestriction();
            $restriction->name      = $request->name;
            $restriction->is_active = $request->is_active;
            $restriction->start_at  = $request->start_at;
            $restriction->end_at    = $request->end_at;
            $restriction->save();

            DB::commit();

            return redirect()->route('config.schedule-restrictions.index')->with('success', 'Restricción de horario creada correctamente.');

        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('config.schedule-restrictions.create')->with('danger', 'Error al crear la restricción de horario.');
        }
    }

    public function edit($request, $id)
    {
        try {
            $restriction = ScheduleRestriction::find($id);

            return view('settings.schedule_restrictions.new', [
                'breadcrumbs' => [
                    [
                        'route'  => route('config.schedule-restrictions.index'),
                        'name'   => 'Listado de restricciones de horarios',
                        'active' => false,
                    ],
                    [
                        'route'  => '',
                        'name'   => 'Editar restricción: ' . $restriction->name,
                        'active' => true,
                    ]
                ],
                'restriction' => $restriction,
            ]);
        } catch (Exception $e) {
        }
    }

    public function update($request, $id)
    {
        try {
            DB::beginTransaction();

            $restriction = ScheduleRestriction::find($id);
            $restriction->name      = $request->name;
            $restriction->is_active = $request->is_active;
            $restriction->start_at  = $request->start_at;
            $restriction->end_at    = $request->end_at;
            $restriction->save();

            DB::commit();

            return redirect()->route('config.schedule-restrictions.index')->with('success', 'Restricción de horario actualizada correctamente.');

        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('config.schedule-restrictions.edit', $id)->with('danger', 'Error al actualizar la restricción de horario.');
        }
    }

    public function destroy($request, $id)
    {
        try {
            $restriction = ScheduleRestriction::find($id);
            $restriction->delete();

            return redirect()->route('config.schedule-restrictions.index')->with('success', 'Restricción de horario eliminada correctamente.');
        } catch (Exception $e) {
        }
    }
}
