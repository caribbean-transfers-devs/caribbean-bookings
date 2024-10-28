<?php

namespace App\Http\Requests;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "email"    => ["required", "email", "string"],
            "password" => ["required", "string"]
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            "email.required"    => "El campo email es requerido",
            "email.email"       => "El campo email debe ser un email válido",
            "password.required" => "El campo contraseña es requerido",
        ];
    }

    public function authenticate() {
        
        $this->ensureIsNotRateLimited();   

        //RECAPTCHA FIRST
        $cu = curl_init();        
        curl_setopt($cu, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($cu, CURLOPT_POST, true);
        curl_setopt($cu, CURLOPT_POSTFIELDS, http_build_query(array("secret" => config('services.gcaptcha.secret'), "response" => $this["g-recaptcha-response"])));
        curl_setopt($cu, CURLOPT_RETURNTRANSFER, true);
        //MIGHT NEED TO DELETE THIS WHEN IN PROD.
        curl_setopt($cu, CURLOPT_SSL_VERIFYPEER, FALSE);   
        $captchaResp = curl_exec($cu);
        curl_close($cu);
 
        $captchaResp = json_decode($captchaResp, true);

        //if($captchaResp["success"] == true && $captchaResp["score"] > 0.5){
            $restricted_user = DB::table("users")->where("email", $this->email )->value("restricted");
            $remember = ($this->boolean('remember-me')) ? true : false;
            if($restricted_user){
                $clientIP = $this->getIP();
                // Asegurarse de que $clientIP sea un arreglo
                $clientIPs = is_array($clientIP) ? $clientIP : [$clientIP];

                // $ip_match = DB::table('whitelist_ips')->whereIn('ip_address',$clientIPs)->value('ip_address');
                $whitelistIPs = DB::table('whitelist_ips')->where('status',1)->whereIn('ip_address', $clientIPs)->pluck('ip_address')->toArray();
                // dd($clientIPs, $whitelistIPs);

                // Validar si hay alguna coincidencia
                $isIPMatched = false;
                if( !empty($whitelistIPs) ){
                    foreach ($clientIPs as $ip) {
                        if (in_array($ip, $whitelistIPs)) {
                            $isIPMatched = true;
                            break;
                        }
                    }
                }

                // Resultado final
                if ($isIPMatched) {
                    // Hay coincidencia
                    if (! Auth::attempt([ 'email' => $this->email, 'password' => $this->password , 'status' => 1], $remember)) {
                        RateLimiter::hit($this->throttleKey());
                        
                        throw ValidationException::withMessages([
                            "email" => 'Las credenciales no son válidas, por favor intente de nuevo.',
                        ]);
                    }
                } else {
                    // No hay coincidencia
                    RateLimiter::hit($this->throttleKey());
                    // abort(401);
                    
                    throw ValidationException::withMessages([
                        "email" => 'No cuenta con acceso a nuestra plataforma desde la ip: '.$clientIP,
                    ]);
                }

                // if($ip_match == $clientIP){
                //     if (! Auth::attempt([ 'email' => $this->email, 'password' => $this->password , 'status' => 1], $remember)) {
                    
                //         RateLimiter::hit($this->throttleKey());
                        
                //         throw ValidationException::withMessages([
                //             "email" => 'Las credenciales no son válidas, por favor intente de nuevo.',
                //         ]);
                //     }
                // }else{
                //     RateLimiter::hit($this->throttleKey());
                //     // abort(401);
                    
                //     throw ValidationException::withMessages([
                //         "email" => 'No cuenta con acceso a nuestra plataforma desde la ip: '.$clientIP,
                //     ]);
                // }
            }else{
                if (! Auth::attempt([ 'email' => $this->email, 'password' => $this->password , 'status' => 1], $remember)) {
                    RateLimiter::hit($this->throttleKey());
                    
                    throw ValidationException::withMessages([
                        "email" => 'Las credenciales no son válidas, por favor intente de nuevo.',
                    ]);
                }
            }

            RateLimiter::clear($this->throttleKey());
        //}
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited()
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            "email" => trans("auth.throttle", [
                "seconds" => $seconds,
                "minutes" => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey()
    {
        return Str::transliterate(Str::lower($this->input("email"))."|".$this->ip());
    }

    // public function getIP() 
    // {
    //     $ipAddress = '';                   
    //     if (!empty($_SERVER['HTTP_CLIENT_IP'])) {                           
    //         $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
    //     }else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    //         $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    //         // Obtén solo la primera IP en caso de que haya varias separadas por comas
    //         // $ipAddress = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    //     } else {                      
    //         $ipAddress = $_SERVER['REMOTE_ADDR'];
    //     }
    //     return $ipAddress;
    // }
    public function getIP() 
    {
        $ipAddress = '';
        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) { // Cloudflare IP
            $ipAddress = $_SERVER['HTTP_CF_CONNECTING_IP'];
        } else if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
            // $ipAddress = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]; // Primera IP
        } else {
            $ipAddress = $_SERVER['REMOTE_ADDR'];
        }
        return $ipAddress;
    }    
        
}
