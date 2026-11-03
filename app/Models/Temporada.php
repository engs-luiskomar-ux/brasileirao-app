<?php

namespace App\Models;

use Database\Factories\TemporadaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Temporada extends Model
{
    /** @use HasFactory<TemporadaFactory> */
    use HasFactory;

    protected $table = 'temporada';

    protected $primaryKey = 'id_temporada';

    public $timestamps = false;

    protected $fillable = ['ano', 'nome'];

    protected function casts(): array
    {
        return ['ano' => 'integer'];
    }

    public function partidas(): HasMany
    {
        return $this->hasMany(Partida::class, 'temporada_id', 'id_temporada');
    }
}
