<?php

namespace App\Enums;

enum WebsiteFilesBelongsTo: int
{
    case BANNERS = 1;
    case GALLERY = 2;
    case TESTIMONIALS = 3;
    case POST = 4;

}
