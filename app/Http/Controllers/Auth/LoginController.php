<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as Device;
use App\Traits\RoleTrait;

//MODELS
use App\Models\UserSession;

class LoginController extends Controller
{   
    public function index(){
        return view('auth.login');
    }

    public function check(LoginRequest $request){
        if(!Auth::check())
        {
            $request->authenticate();
            $request->session()->regenerate();
            session(['roles' => RoleTrait::getRolesAndSubmodules()]);

            $dataUser = auth()->user();
            $this->handleLogin($dataUser);
            if( $dataUser->is_commission == 1 ){
                return redirect()->route('callcenters.index');    
            }
            return redirect()->route('dashboard');
        }
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerate();

        return redirect(url('/'));
    }

    public function handleLogin($dataUser)
    {
        UserSession::create([
            'user_id' => $dataUser->id,
            'ip_address' => Device::ip(),
            'user_agent' => Device::userAgent(),
            'device_name' => $this->getDeviceName(Device::userAgent()),
            'last_activity' => now(),
        ]);
    }

    private function getDeviceName($userAgent)
    {
        // Aquí puedes usar un paquete como "jenssegers/agent" para identificar el dispositivo y navegador
        $agent = new \Jenssegers\Agent\Agent();
        $agent->setUserAgent($userAgent);
        return $agent->device() . ' - ' . $agent->browser();
    }

    public function logoutOtherSession($sessionId)
    {
        $session = UserSession::find($sessionId);    
        if ($session && $session->user_id == Auth::id()) {
            // Aquí puedes usar un sistema de cookies o una acción de logout
            $session->delete();
            // Puedes hacer algo aquí para invalidar la sesión en ese dispositivo
        }
        return back();
    }

    public function logoutAllSessions()
    {
        UserSession::where('user_id', Auth::id())->delete();
        Auth::logout();
        return redirect('/login');
    }
}