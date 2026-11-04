<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Clube;
use App\Models\Gol;

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
    public function clube(): BelongsTo
    {
        return $this->belongsTo(Clube::class, 'clube_id', 'id_clube');
    }

    public function transferencias(): HasMany
    {
        return $this->hasMany(HistoricoTransferencia::class, 'jogador_id', 'id_jogador');
    }

    public function gols(): HasMany
    {
        return $this->hasMany(Gol::class, 'jogador_id', 'id_jogador');
    }
}
