<?php

namespace App\Models\Website\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Navigation extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function menu(){
        return $this->hasOne(Banner::class,'id','menu');
    }

    public function seo()
    {
        return $this->hasOne(SEO::class, 'id', 'menu');
    }

}
