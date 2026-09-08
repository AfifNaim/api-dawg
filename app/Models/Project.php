<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = ['name', 'base_url', 'token', 'proxy_target', 'description'];

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class)->orderBy('sort_order');
    }

    public function apis(): HasMany
    {
        return $this->hasMany(Api::class)->orderBy('sort_order');
    }
}
