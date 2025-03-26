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
use App\Models\WhitelistIp;

class UserRepository
{
    public function indexUsers($request)
    {
        try {
            $users = User::whereIn('status', [0, 1])
            ->with(['target','roles.role'])
            ->get();

            // $active_users = User::where('status', 1)
            // ->with(['target', 'roles.role'])
            // ->paginate(20, ['*'], 'active_page');

            // $inactive_users = User::where('status', 0)
            //     ->with(['target', 'roles.role'])
            //     ->paginate(20, ['*'], 'inactive_page');

            return view('settings.users.index', [
                'active_users' =>  $users->where('status', 1),
                'inactive_users' => $users->where('status', 0),
                // 'active_users' =>  $active_users,
                // 'inactive_users' => $inactive_users,
                'valid_ips' => WhitelistIp::with(['user'])->get(),
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
                'valid_ips' => []
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
                'v_type' => 1,
                'roles' => cache()->remember('roles_list', 60, fn() => Role::all()),                
                'user' => new User()                
            ]);
        } catch (Exception $e) {
            return back()->withErrors(['danger' => $e->getMessage()]);
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
                'breadcrumbs' => [
                    [
                        "route" => route('users.index'),
                        "name" => "Listado de usuarios",
                        "active" => false                        
                    ],
                    [
                        "route" => "",
                        "name" => "Editar usuario: ".$user->name,
                        "active" => true                        
                    ],                
                ],
                'v_type' => 2,
                'roles' => cache()->remember('roles_list', 60, fn() => Role::all()),                
                'user' => $user,
                'currentSession' => $currentSession
            ]);
        } catch (Exception $e) {
            return back()->withErrors(['danger' => $e->getMessage()]);
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

            $user_roles = collect($request->roles)->map(fn($role) => [
                'user_id' => $user->id,
                'role_id' => $role
            ])->toArray();
            
            UserRole::insert($user_roles);

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Usuario creado correctamente',
                'status' => Response::HTTP_OK
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
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

            $user->roles2()->sync($request->roles);

            DB::commit();
            return response()->json([
                'success' => true, 
                'status' => 'success',
                'message' => 'Usuario actualizado correctamente',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
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
                'status' => 'success',
                'message' => 'Contraseña actualizada correctamente',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
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
                'status' => 'success', 
                'message' => 'Estado actualizado correctamente',
            ], Response::HTTP_OK);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
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
                'status' => 'success',
                'message' => 'IP guardada correctamente',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteIps($request,$ip){
        try {
            DB::beginTransaction();

            $ip->delete();

            DB::commit();
            return response()->json([
                'success' => true, 
                'status' => 'success',
                'message' => 'IP eliminada correctamente',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false, 
                'message' => 'Error al eliminar la IP',
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
