<?php

namespace App;

use App\Models\Clube;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoricoTransferencia extends Model
{
    protected $table = 'historico_transferencia';

    protected $primaryKey = 'id_transferencia';

    public $timestamps = false;

    protected $guarded = ['*'];

    protected function casts(): array
    {
        return ['data_transferencia' => 'datetime'];
    }

    public function jogador(): BelongsTo
    {
        return $this->belongsTo(Jogador::class, 'jogador_id', 'id_jogador');
    }

    public function origem(): BelongsTo
    {
        return $this->belongsTo(Clube::class, 'clube_origem_id', 'id_clube');
    }

    public function destino(): BelongsTo
    {
        return $this->belongsTo(Clube::class, 'clube_destino_id', 'id_clube');
    }
}
