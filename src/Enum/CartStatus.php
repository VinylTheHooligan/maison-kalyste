<?php

namespace App\Enum;

enum CartStatus: string
{
    case ACTIVE = 'active';
    case ABANDONNED = 'abandoned';
    case CONVERTED = 'converted';
}