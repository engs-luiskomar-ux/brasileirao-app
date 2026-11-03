<?php

namespace App\Models;

use App\Jogador;
use Database\Factories\ClubeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Clube extends Model
{
    /** @use HasFactory<ClubeFactory> */
    use HasFactory;

    protected $table = 'clube';

    protected $primaryKey = 'id_clube';

    public $timestamps = false;

    protected $fillable = ['nome', 'cidade', 'estado', 'ano_fundacao'];

    protected function casts(): array
    {
        return ['ano_fundacao' => 'integer'];
    }

    public function jogadores(): HasMany
    {
        return $this->hasMany(Jogador::class, 'clube_id', 'id_clube');
    }

    public function partidasComoMandante(): HasMany
    {
        return $this->hasMany(Partida::class, 'mandante_id', 'id_clube');
    }

    public function partidasComoVisitante(): HasMany
    {
        return $this->hasMany(Partida::class, 'visitante_id', 'id_clube');
    }
}
