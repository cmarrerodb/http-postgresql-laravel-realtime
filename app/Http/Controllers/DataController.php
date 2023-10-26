<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ApiLogs;
use App\Models\Data;
class DataController extends Controller
{
    public function rec_send_subscriptions(Request $request) {
        info($request->all());
        $tabla = $request->tabla;
        $urls = DB::table('suscriptions')
            ->where('table', $tabla)
            ->pluck('url');
        info($urls);
        foreach ($urls as $url) {
            info($url);
            $envio = $request->except('tabla');
            try {
                $response = Http::post($url, $envio);
                info($response);
                ApiLogs::create([
                    'tabla_origen' => $request->tabla,
                    'data_origen' => json_encode($request->all()),
                    'destino' => $url,
                    'response' => $response->body(),
                    'aceptado' => $response->status() === 200 ? true : false,
                    'status_response' => $response->status(),
                    'accepted_at' => now(),
                ]);
            } catch (\Exception $e) {
                info($e->getMessage());
                ApiLogs::create([
                    'tabla_origen' => $request->tabla,
                    'data_origen' => json_encode($request->all()),
                    'destino' => $url,
                    'response' => '',
                    'aceptado' => false,
                    'status_response' => 404,
                ]);
            }
        }
        return response()->json(['mensaje' => 1], 200);
    }

}
