<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasInventaris;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventaris extends Model
{
    use HasInventaris;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inventariss';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'categories',
        'equipments',
        'loan_requests',
        'kategori_id',
        'stok',
        'sinopsis',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tahun_terbit' => 'integer',
        'stok' => 'integer',
    ];

    /**
     * Relasi Many-to-One (Invers): Inventaris terikat pada satu Kategori.
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }
}
