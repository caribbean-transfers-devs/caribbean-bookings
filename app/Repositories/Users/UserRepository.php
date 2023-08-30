<?php

namespace App\Repositories\Users;

use App\Models\Role;
use App\Models\User;
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

            return view('users.index', compact('active_users', 'inactive_users'));
            
        } catch (\Throwable $e) {

            $active_users = [];
            $inactive_users = [];

            return view('users.index', compact('active_users', 'inactive_users'));
        }
    }
    

    public function createUser($request){
        return view('users.create_edit', [
            'roles' => Role::all(),
            'v_type' => 1,
            'user' => new User()
        ]);
    }

    public function editUser($request){
        return view('users.create_edit', [
            'roles' => Role::all(),
            'v_type' => 2,
            'user' => User::find($request->id)
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

            $user->roles()->createMany($request->roles);

            DB::commit();

            return response()->json([
                'message' => 'Usuario creado correctamente',
                'status' => Response::HTTP_OK
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error al crear el usuario',
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ]);
        }
    }

    public function updateUser($request){
        try {
            DB::beginTransaction();

            $user = User::find($request->id);
            $user->name = $request->name;
            $user->email = strtolower($request->email);
            $user->restricted = $request->restricted;
            $user->save();

            $user->roles()->delete();
            $user->roles()->createMany($request->roles);

            DB::commit();

            return response()->json([
                'message' => 'Usuario actualizado correctamente',
                'status' => Response::HTTP_OK
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Error al actualizar el usuario',
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ]);
        }
    }
}
