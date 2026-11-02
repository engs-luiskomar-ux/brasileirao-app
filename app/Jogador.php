<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jogador extends Model
{
    /** @use HasFactory<\Database\Factories\JogadorFactory> */
    use HasFactory;

    public const POSICOES = ['Goleiro', 'Zagueiro', 'Lateral', 'Meio-campo', 'Atacante'];
    protected $table = 'jogador';
    protected $primaryKey = 'id_jogador';
    public $timestamps = false;
    protected $fillable = ['nome', 'posicao', 'data_nascimento', 'numero_camisa', 'clube_id'];

    protected function casts(): array
    {
        return [
            'data_nascimento' => 'date',
            'numero_camisa' => 'integer',
            'clube_id' => 'integer',
        ];
    }
}
