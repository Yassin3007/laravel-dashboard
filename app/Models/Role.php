<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
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


    public function permissions()
    {
        return $this->belongsToMany(\App\Models\Permission::class);
    }
}
