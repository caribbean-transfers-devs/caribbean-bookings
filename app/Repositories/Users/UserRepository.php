<?php

namespace App\Repositories\Users;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

//MODELS
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Target;
use App\Models\WhitelistIp;

class UserRepository
{
    public function indexUsers($request)
    {
        try {
            return view('users.index', [
                'active_users' => User::where('status', 1)->whereDoesntHave('roles', function ($query) { $query->where('role_id', 4); })->get(),
                'active_users_callcenter' => User::where('status', 1)->with('target')->whereHas('roles', function ($query) { $query->where('role_id', 4); })->whereDoesntHave('roles', function ($query) { $query->where('role_id', '!=', 4); })->get(),
                'inactive_users' => User::where('status', 0)->get(),
                'valid_ips' => WhitelistIp::all(), 
                'breadcrumbs' => [
                    [
                        "route" => "",
                        "name" => "Listado de usuarios",
                        "active" => true                        
                    ]
                ]
            ]);
            
        } catch (Exception $e) {
            $active_users = [];
            $inactive_users = [];
            $valid_ips = WhitelistIp::all();
            return view('users.index', [
                'active_users' => [], 
                'inactive_users' => [],
                'active_users_callcenter' => [], 
                'valid_ips' => []
            ]);
        }
    }
    

    public function createUser($request){
        try {
            return view('users.create_edit',[
                'breadcrumbs' => [
                    [
                        "route" => route('users.index'),
                        "name" => "Listado de usuarios",
                        "active" => false
                    ],
                    [
                        "route" => "",
                        "name" => "Nuevo usuario ".( $request->type === 'callcenter' ? 'de Call Center' : '' ),
                        "active" => true
                    ]
                ],
                'roles' => ( $request->type === 'callcenter' ? Role::where('id', 4)->get() : Role::where('id', '!=', 4)->get() ),
                'v_type' => 1,
                'user' => new User()                
            ]);
        } catch (Exception $e) {
            return back()->withErrors(['danger' => 'Ocurrió un error al cargar la página.']);
        }
    }

    public function editUser($request,$user){
        return view('users.create_edit', [
            'roles' => Role::all(),
            'v_type' => 2,
            'user' => $user
        ]);
    }

    public function storeUser($request){
        try {
            DB::beginTransaction();

            $user = new User();
            $user->name = $request->name;
            $user->email = strtolower($request->email);
            $user->password = bcrypt($request->password);
            $user->restricted = $request->restricted;

            ( isset($request->type_commission) && ($request->type_commission == "target" || $request->type_commission == "percentage") ? $user->is_call_center_agent = 1 : 0 );
            ( isset($request->type_commission) ? $user->type_commission = $request->type_commission : "target" );
            ( isset($request->type_commission) && $request->type_commission == "target" ? $user->target_id = 1 : NULL );
            ( isset($request->percentage) ? $user->percentage = $request->percentage : 0 );
            ( isset($request->daily_goal) ? $user->daily_goal = $request->daily_goal : 0 );

            $user->save();

            // $tar = [
            //     [
            //         "amount" => 85000,
            //         "percentage" => 4
            //     ],
            //     [
            //         "amount" => 110000,
            //         "percentage" => 5
            //     ],
            //     [
            //         "amount" => 135000,
            //         "percentage" => 6
            //     ],
            //     [
            //         "amount" => 155000,
            //         "percentage" => 7
            //     ],
            //     [
            //         "amount" => 175000,
            //         "percentage" => 8
            //     ],
            // ];

            // $target = new Target();
            // $target->object = $tar;
            // $target->save();

            foreach ($request->roles as $role) {
                $user_role = new UserRole();
                $user_role->role_id = $role;
                $user_role->user_id = $user->id;
                $user_role->save();
            }

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Usuario creado correctamente',
                'status' => Response::HTTP_OK
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al crear el usuario',
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ]);
        }
    }

    public function updateUser($request, $user){
        try {
            DB::beginTransaction();

            $user->name = $request->name;
            $user->email = strtolower($request->email);
            $user->restricted = $request->restricted;

            ( isset($request->type_commission) && ($request->type_commission == "target" || $request->type_commission == "percentage") ? $user->is_call_center_agent = 1 : 0 );
            ( isset($request->type_commission) ? $user->type_commission = $request->type_commission : "target" );
            ( isset($request->type_commission) && $request->type_commission == "target" ? $user->target_id = 1 : NULL );
            ( isset($request->percentage) ? $user->percentage = $request->percentage : 0 );
            ( isset($request->daily_goal) ? $user->daily_goal = $request->daily_goal : 0 );

            $user->save();

            $user->roles()->delete();
            foreach ($request->roles as $role) {
                $user_role = new UserRole();
                $user_role->role_id = $role;
                $user_role->user_id = $user->id;
                $user_role->save();
            }

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Usuario actualizado correctamente',
                'status' => Response::HTTP_OK
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false, 
                'message' => 'Error al actualizar el usuario',
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ]);
        }
    }

    public function changePass($request,$user){
        try {
            DB::beginTransaction();

            $user->password = bcrypt($request->password);
            $user->save();

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Contraseña actualizada correctamente',
                'status' => Response::HTTP_OK
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false, 
                'message' => 'Error al actualizar la contraseña',
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ]);
        }
    }

    public function changeStatus($request,$user){
        try {
            DB::beginTransaction();

            $user->status = $request->status;
            $user->save();

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Estado actualizado correctamente',
                'status' => Response::HTTP_OK
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false, 
                'message' => 'Error al actualizar el estado',
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ]);
        }
    }

    public function storeIps($request){
        try {
            DB::beginTransaction();

            $ip = new WhitelistIp();           
            $ip->ip_address = $request->ip;
            $ip->added_by = auth()->user()->id;
            $ip->created_at = now();
            $ip->save();

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'IP guardada correctamente',
                'status' => Response::HTTP_OK
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false, 
                'message' => 'Error al guardar la IP',
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function deleteIps($request,$ip){
        try {
            DB::beginTransaction();

            $ip->delete();

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'IP eliminada correctamente',
                'status' => Response::HTTP_OK
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false, 
                'message' => 'Error al eliminar la IP',
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
