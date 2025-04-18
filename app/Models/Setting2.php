<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting2 extends Model
{
    use HasFactory;
    protected $table = 'tbsetting2';
    protected $primaryKey = 'set_id';
    protected $fillable = [
        'set_facultytitle',
        'set_facultydep',
        'set_logo',
        'set_amstu',
        'set_enroll',
        'lang'
    ];
}
