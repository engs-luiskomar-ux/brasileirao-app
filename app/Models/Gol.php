<?php

namespace App\Models;

use App\Jogador;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Thiago - model da tabela gol.
// clube_id é o clube creditado no placar; a trigger considera também o gol contra.
class Gol extends Model
{
    use HasFactory;

    protected $table = 'gol';

    protected $primaryKey = 'id_gol';

    public $timestamps = false;

    protected $fillable = ['minuto', 'partida_id', 'jogador_id', 'clube_id', 'contra'];

    protected function casts(): array
    {
        return [
            'minuto' => 'integer',
            'partida_id' => 'integer',
            'jogador_id' => 'integer',
            'clube_id' => 'integer',
            'contra' => 'boolean',
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
