<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RsdTitle extends Model
{
    use HasFactory;

    protected $table = 'tbrsd_title';
    protected $primaryKey = 'rsdt_id';

    protected $fillable = [
        'rsdt_title',
        'rsdt_text',
        'rsdt_order',
        'display',
        'active',
        'rsdt_type',
        'rsdt_code'
    ];
    public function desc()
    {
        return $this->hasMany(RsdDesc::class, 'rsdd_rsdtile', 'rsdt_id')->select(['rsdd_id', 'rsdd_rsdtile', 'rsdd_details']);
    }
    public function project()
    {
        return $this->hasMany(RsdProject::class, 'rsdp_rsdtile', 'rsdt_id')->select(['rsdp_id', 'rsdp_rsdtile', 'rsdp_detail']);
    }

    public function rsd()
    {
        return $this->hasMany(Rsd::class, 'rsdt_text', 'rsdt_id')->select(['rsd_id', 'rsdt_text']);
    }


}
