<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DataController extends Controller
{
    public function updateData(Request $request)
    {
        $data = $request->all();

        // Aquí puedes guardar los datos en la base de datos si es necesario

        // Enviar datos al servidor de Socket.IO
        $this->sendToSocketIo($data);

        return response()->json(['success' => true]);
    }

    private function sendToSocketIo($data)
    {
        $url = "http://localhost:3000/updateData";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_exec($ch);
        curl_close($ch);
    } 
}
