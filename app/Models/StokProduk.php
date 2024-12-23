<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class StokProduk extends Model
{
    use HasFactory;

    protected $fillable = [
        'statusenabled',
        'product_id',
        'keterangan',
        'stokawal',
        'qtyin',
        'qtyout',
        'stok_akhir',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
