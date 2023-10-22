<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Data;
class DataController extends Controller
{
    public function datadb(Request $request) {
        info($request->all());
        switch ($request->tabla) {
            case 'tabla_base_a':
                info(1);
                $url='http://127.0.0.1:5000';
                $envio = ['texto' => $request->texto];
                break;
            case 'tabla_base_c':
                info(2);
                $url = 'http://127.0.0.1:5001';
                $envio = ['campo1' => $request->campo1, 'campo2' => $request->campo2];
                break;
        }
        $response = Http::post($url, $envio);
        info($response);
        return response()->json(['mensaje' => 1], 200);

    }
    public function rec_send_subscriptions(Request $request) {
        info($request->all());
        $tabla = $request->tabla;
        $urls = DB::table('suscriptions')
                    ->where('table', $tabla)
                    ->pluck('url');
        info($urls);
        foreach ($urls as $url) {
            $envio = $request->except('tabla');
            $response = Http::post($url, $envio);
            info($response);
        }    
        return response()->json(['mensaje' => 1], 200);       
    }
}
