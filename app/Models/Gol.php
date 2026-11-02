<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Jogador;

// Thiago - model da tabela gol.
// clube_id = clube que recebeu o gol. Se vier vazio, a trigger preenche com o clube do jogador.
class Gol extends Model
{
    use HasFactory;

    protected $table = 'gol';
    protected $primaryKey = 'id_gol';
    public $timestamps = false;

    protected $fillable = ['minuto', 'partida_id', 'jogador_id', 'clube_id'];

    protected function casts(): array
    {
        return [
            'minuto' => 'integer',
            'partida_id' => 'integer',
            'jogador_id' => 'integer',
            'clube_id' => 'integer',
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

    public function clube(): BelongsTo
    {
        return $this->belongsTo(Clube::class, 'clube_id', 'id_clube');
    }
}