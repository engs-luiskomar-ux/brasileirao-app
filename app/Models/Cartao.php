<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Thiago - model da tabela cartao.
// o cadastro pela aplicação passa pela procedure sp_registrar_cartao.
class Cartao extends Model
{
    use HasFactory;

    public const TIPOS = ['amarelo', 'vermelho'];

    protected $table = 'cartao';
    protected $primaryKey = 'id_cartao';
    public $timestamps = false;

    protected $fillable = ['minuto', 'tipo', 'partida_id', 'jogador_id'];

    protected function casts(): array
    {
        return [
            'minuto' => 'integer',
            'partida_id' => 'integer',
            'jogador_id' => 'integer',
        ];
    }

    public function partida(): BelongsTo
    {
        return $this->belongsTo(Partida::class, 'partida_id', 'id_partida');
    }

    public function jogador(): BelongsTo
    {
        return $this->belongsTo(Jogador::class, 'jogador_id', 'id_jogador');
    }
}