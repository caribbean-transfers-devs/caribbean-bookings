<?php

namespace App\Repositories\Settings;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

//MODELS
use App\Models\Role;
use App\Models\User;
use App\Models\UserSession;
use App\Models\UserRole;

class UserRepository
{
    public function indexUsers($request)
    {
        try {
            return view('settings.users.index', [
                'active_users' =>  User::where('status', 1)->with(['target', 'roles.role'])->get(),
                'inactive_users' => User::where('status', 0)->with(['roles.role'])->get(),
                'breadcrumbs' => [
                    [
                        "route" => "",
                        "name" => "Listado de usuarios",
                        "active" => true                        
                    ]
                ]
            ]);            
        } catch (Exception $e) {
            return view('settings.users.index', [
                'active_users' => [], 
                'inactive_users' => [],
            ]);
        }
    }

    public function createUser($request){
        try {
            return view('settings.users.new',[
                'breadcrumbs' => [
                    [
                        "route" => route('users.index'),
                        "name" => "Listado de usuarios",
                        "active" => false
                    ],
                    [
                        "route" => "",
                        "name" => "Nuevo usuario",
                        "active" => true
                    ]
                ],
                // 'roles' => Role::all(),
                'roles' => cache()->remember('roles_list', 60, fn() => Role::all()),
                'v_type' => 1,
                'user' => new User()
            ]);
        } catch (Exception $e) {
            return back()->withErrors(['danger' => 'Ocurrió un error al cargar la página.']);
        }        
    }

    public function editUser($request,$user){
        try {
            // Comparar el dispositivo actual con las sesiones guardadas
            $currentSession = UserSession::where('user_id', $user->id)
                ->where('ip_address', $request->ip())
                ->where('user_agent', $request->userAgent())
                ->first();
            return view('settings.users.new', [
                // 'roles' => Role::all(),
                'roles' => cache()->remember('roles_list', 60, fn() => Role::all()),
                'v_type' => 2,
                'user' => $user,
                'currentSession' => $currentSession
            ]);
        } catch (Exception $e) {
            return back()->withErrors(['danger' => 'Ocurrió un error al cargar la página.']);
        }
    }

    public function storeUser($request){
        try {
            DB::beginTransaction();

            $user = new User();
            $user->name = $request->name;
            $user->email = strtolower($request->email);
            $user->password = bcrypt($request->password);
            $user->restricted = $request->restricted;
            $user->is_commission = $request->is_commission;
            ( isset($request->type_commission) ? $user->type_commission = $request->type_commission : "target" );
            ( isset($request->type_commission) && $request->type_commission == "target" ? $user->target_id = 1 : NULL );
            ( isset($request->percentage) ? $user->percentage = $request->percentage : 0 );
            ( isset($request->daily_goal) ? $user->daily_goal = $request->daily_goal : 0 );
            $user->is_external = $request->is_external;
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
                'message' => $e->getMessage(),
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
            $user->is_commission = $request->is_commission;
            ( isset($request->type_commission) ? $user->type_commission = $request->type_commission : "target" );
            ( isset($request->type_commission) && $request->type_commission == "target" ? $user->target_id = 1 : NULL );
            ( isset($request->percentage) ? $user->percentage = $request->percentage : 0 );
            ( isset($request->daily_goal) ? $user->daily_goal = $request->daily_goal : 0 );
            $user->is_external = $request->is_external;
            $user->save();

            $user->roles()->sync($request->roles);
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
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage(),
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ]);
        }        
    }

    public function changePass($request,$user){
    }

    public function changeStatus($request,$user){
    }

    public function storeIps($request){
    }

    public function deleteIps($request,$ip){
    }
}
