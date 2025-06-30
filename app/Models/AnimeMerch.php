<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use OpenApi\Annotations as OA;

/**
 * Class AnimeMerch
 * 
 * @author Alvin <alvin.422024017@civitas.ukrida.ac.id>
 * 
 * @OA\Schema(
 *      description="Merch model",
 *      title="Merch model",
 *      required={"nama_item", "dibuat_oleh"},
 *      @OA\Xml(
 *          name="AnimeMerch"
 *      )
 * )
 */
class AnimeMerch extends Model
{
    use SoftDeletes;

    protected $table = 'anime_merch';

    protected $fillable = [
        'nama_item',
        'Producer',
        'tahun_rilis',
        'gambar',
        'description',
        'harga',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Relasi: User yang membuat buku
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    // Relasi: User yang mengubah buku
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    // Relasi: User yang menghapus buku
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by', 'id');
    }
}
