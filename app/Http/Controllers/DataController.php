<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\Data;
class DataController extends Controller
{
    public function datadb(Request $request) {
        $data = $request->all();
        if ($data['tabla'] == 'tabla_base_a') {
            $data['origen'] = 'EL ORIGIEN DE LOS DATOS ES tabla_base_a';
            $nuevoRegistro = new Data();
            $nuevoRegistro->procedencia = 'Recibido de la BD a través de la API';
            $nuevoRegistro->tabla = $request->tabla;
            $nuevoRegistro->valor = $request->texto;
            $nuevoRegistro->save();
        } else {
            $data['origen'] = 'LA DATA PROVIENE DESDE OTRA FUENTE';
        }
        // info($request->all());
        $response = Http::post("http://127.0.0.1:5000", [$data->texto]);
        return response()->json(200);
        return response()->json(['mensaje' => 'Se ha recibido el dato'], 200);
    }    
}
