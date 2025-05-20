<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'guard_name'];

    /**
     * Get the table associated with the model.
     *
     * @return string
     */


    public function roles()
    {
        return $this->belongsToMany(\App\Models\Role::class);
    }
}
