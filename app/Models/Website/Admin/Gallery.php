<?php

namespace App\Models\Website\Admin;

use App\Models\WebsiteFiles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


class Gallery extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function websitefiles()
    {
        return $this->hasMany(WebsiteFiles::class, 'reference_id', 'id');
    }
}
