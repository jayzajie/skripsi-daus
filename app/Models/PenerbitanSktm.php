<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenerbitanSktm extends Model
{
    use HasFactory;

    protected $table = 'penerbitan_sktm';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'tanggal_terbit' => 'date',
        ];
    }

    public function permohonanSktm(): BelongsTo
    {
        return $this->belongsTo(PermohonanSktm::class);
    }

    public function penerbit(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diterbitkan_oleh');
    }
}
