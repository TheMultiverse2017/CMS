<?php

namespace App\Models;

use App\Models\Website\Admin\Banner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebsiteFiles extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function banner()
    {
        return $this->belongsTo(Banner::class, 'reference_id', 'id');
    }
}
