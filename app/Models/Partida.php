<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Partida extends Model
{
    use HasFactory;

    protected $table = 'partida';
    protected $primaryKey = 'id_partida';
    public $timestamps = false;

    protected $fillable = ['rodada', 'data_partida', 'temporada_id', 'mandante_id', 'visitante_id'];

    public function casts(): array{

        return [
            'data_partida' => 'datetime',
            'rodada' => 'integer',
            'temporada_id' => 'integer',
            'mandante_id' => 'integer',
            'visitante_id' => 'integer',
        ];
    }

    public function temporada(): BelongsTo
    {
        return $this->belongsTo(Temporada::class, 'temporada_id', 'id_temporada');
    }

    public function mandante(): BelongsTo
    {
        return $this->belongsTo(Clube::class, 'mandante_id', 'id_clube');
    }

    public function visitante(): BelongsTo
    {
        return $this->belongsTo(Clube::class, 'visitante_id', 'id_clube');
    }

    public function gols(): HasMany
    {
        return $this->hasMany(Gol::class, 'partida_id', 'id_partida');
    }

    public function cartoes(): HasMany
    {
        return $this->hasMany(Cartao::class, 'partida_id', 'id_partida');
    }

    public function realizada(): bool
    {
        return $this->data_partida->isPast();
    }
}
