<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekamMedis extends Model
{
    protected $table = 'rekam_medis';
    protected $guarded = ['id'];

    public function pasienTerdaftar()
    {
        return $this->belongsTo(PasienTerdaftar::class);
    }

    // ----- SUBJECTIVE ----- //
    public function dataKesehatanRM()
    {
        return $this->hasOne(DataKesehatanRM::class);
    }

    public function dataEstetikaRM()
    {
        return $this->hasOne(DataEstetikaRM::class);
    }

    // ----- OBJECTIVE ----- //
    public function tandaVitalRM()
    {
        return $this->hasOne(TandaVitalRM::class);
    }

    public function pemeriksaanFisikRM()
    {
        return $this->hasOne(PemeriksaanFisikRM::class);
    }

    public function pemeriksaanKulitRM()
    {
        return $this->hasOne(PemeriksaanKulitRM::class);
    }

    // ----- ASSESSMENT ----- //
    public function diagnosaRM()
    {
        return $this->hasOne(DiagnosaRM::class);
    }

    public function icdRM()
    {
        return $this->hasMany(IcdRM::class);
    }

    // ----- PLAN ----- //
    public function rencanaLayananRM()
    {
        return $this->hasMany(RencanaLayananRM::class);
    }

    public function rencanaTreatmentRM()
    {
        return $this->hasMany(RencanaTreatmentRM::class);
    }

    public function rencanaProdukRM()
    {
        return $this->hasMany(RencanaProdukRM::class);
    }

    public function rencanaBundlingRM()
    {
        return $this->hasMany(RencananaBundlingRM::class);
    }

    public function obatNonRacikanRM()
    {
        return $this->hasMany(ObatNonRacikanRM::class);
    }

    public function obatRacikanRM()
    {
        return $this->hasMany(ObatRacikanRM::class);
    }

}
