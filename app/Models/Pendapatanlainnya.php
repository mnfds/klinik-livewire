<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pendapatanlainnya extends Model
{
    use HasFactory;

    protected $table = 'pendapatanlainnyas';
    protected $guarded = ['id'];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    // id "akar" grup tagihan ini
    public function getRootIdAttribute()
    {
        return $this->parent_id ?? $this->id;
    }

    // scope untuk ambil semua row dalam 1 grup (akar + anak-anaknya)
    public function scopeGrup($query, $rootId)
    {
        return $query->where(fn ($q) => $q->where('id', $rootId)->orWhere('parent_id', $rootId));
    }

    public function getTotalDibayarkanGroupAttribute()
    {
        return static::grup($this->root_id)->sum('total_dibayarkan');
    }

    public function getSisaTagihanAttribute()
    {
        return max(0, $this->total_tagihan - $this->total_dibayarkan_group);
    }

    // true kalau grup ini SUDAH lunas (dipakai buat sembunyikan tombol pelunasan)
    public function getIsLunasGroupAttribute(): bool
    {
        return static::grup($this->root_id)->where('status', 'lunas')->exists();
    }
}
