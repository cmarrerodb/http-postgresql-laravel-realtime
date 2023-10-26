<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApiLogs extends Model
// class api_logs extends Model
{
    use SoftDeletes;
    protected $table = 'api_logs';
    protected $fillable = [
        'tabla_origen',
        'data_origen',
        'destino',
        'response',
        'aceptado',
        'status_response',
        'accepted_at',
    ];
    protected $dates = ['deleted_at'];
}
