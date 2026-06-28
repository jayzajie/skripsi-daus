<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisposisiSurat extends Model
{
    use HasFactory;

    protected $table = 'disposisi_surat';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'tanggal_disposisi' => 'date',
            'batas_waktu' => 'date',
        ];
    }

    public function suratMasuk(): BelongsTo
    {
        return $this->belongsTo(SuratMasuk::class);
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
