<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArsipDokumen extends Model
{
    use HasFactory;

    protected $table = 'arsip_dokumen';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'tanggal_dokumen' => 'date',
        ];
    }

    public function pembuat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
