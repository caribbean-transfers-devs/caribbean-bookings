<?php

namespace App\Repositories\Users;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use App\Models\WhitelistIp;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    public function indexUsers($request)
    {
        try {
            
            $active_users = User::where('status', 1)->get();
            $inactive_users = User::where('status', 0)->get();
            $valid_ips = WhitelistIp::all();

            $breadcrumbs = array(
                array(
                    "route" => "",
                    "name" => "Listado de usuarios",
                    "active" => true
                ),
            );

            return view('users.index', compact('active_users', 'inactive_users', 'valid_ips', 'breadcrumbs'));
            
        } catch (\Throwable $e) {

            $active_users = [];
            $inactive_users = [];
            $valid_ips = WhitelistIp::all();

            return view('users.index', compact('active_users', 'inactive_users', 'valid_ips'));
        }
    }
    

    public function createUser($request){
        return view('users.create_edit', [
            'roles' => Role::all(),
            'v_type' => 1,
            'user' => new User()
        ]);
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
            $user->save();

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
