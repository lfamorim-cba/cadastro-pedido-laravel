<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cliente extends Model
{
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    protected $casts = [
        'data' => 'datetime: h:m:s - d/m/Y'
    ];

    protected $fillable = [
        'nome',  'data'
    ];

    public static function numeroClientes()
    {
        $numero = DB::table('clientes')
                    ->select(DB::raw('
                        count(nome) as nome
                    '))
                    ->get();

        return $numero;
    }

    public static function nomeClientes()
    {
        $cliente = DB::table('clientes')->get();

        return $cliente;
    }
}
