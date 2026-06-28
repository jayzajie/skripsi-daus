<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumenSktm extends Model
{
    use HasFactory;

    protected $table = 'dokumen_sktm';

    protected $guarded = [];

    public function permohonanSktm(): BelongsTo
    {
        return $this->belongsTo(PermohonanSktm::class);
    }
}
