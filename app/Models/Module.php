<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    public static $searchable = [
        'name',
        'display_name',
        'description'
    ];

    // Relations

    public function permissions()
    {
        return $this->hasMany('App\Models\Permission', 'module_id');
    }
}
