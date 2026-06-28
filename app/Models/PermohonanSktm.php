<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PermohonanSktm extends Model
{
    use HasFactory;

    public const STATUS_MENUNGGU = 'menunggu';

    public const STATUS_DIVERIFIKASI = 'diverifikasi';

    public const STATUS_DISETUJUI = 'disetujui';

    public const STATUS_DITOLAK = 'ditolak';

    public const STATUS_DITERBITKAN = 'diterbitkan';

    protected $table = 'permohonan_sktm';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'tanggal_pengajuan' => 'date',
        ];
    }

    public function masyarakat(): BelongsTo
    {
        return $this->belongsTo(Masyarakat::class);
    }

    public function dokumen(): HasMany
    {
        return $this->hasMany(DokumenSktm::class);
    }

    public function penerbitan(): HasOne
    {
        return $this->hasOne(PenerbitanSktm::class);
    }
}
