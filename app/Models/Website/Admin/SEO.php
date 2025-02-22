<?php

namespace App\Models\Website\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class SEO extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function menu()
    {
        return $this->belongsTo(Navigation::class, 'menu', 'id');
    }

}
