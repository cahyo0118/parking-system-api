<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    public static $searchable = [
        'name',
        'display_name',
        'description'
    ];

    public function permissions()
    {
        return $this->belongsToMany(
            'App\Models\Permission',
            'role_permission',
            'role_id',
            'permission_id'
        )->withPivot('module_id');
    }
}
