<?php

namespace App\Enums;

enum WebsiteFilesType: int
{
    case IMAGE = 1;
    case VIDEO = 2;
    case PDF = 3;
    case EXCEL = 4;
    case CSV = 5;
    case OTHER = 6;
}
