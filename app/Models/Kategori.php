<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasInventaris;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    use HasInventaris;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kategoris';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kode_kategori',
        'nama_kategori',
    ];

    /**
     * Relasi One-to-Many: Satu kategori memiliki banyak buku.
     */
    public function inventariss(): HasMany
    {
        return $this->hasMany(Inventaris::class, 'kategori_id');
    }
}
