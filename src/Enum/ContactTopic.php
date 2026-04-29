<?php

namespace App\Enum;

enum ContactTopic: string {
    case ORDER = 'order';
    case PRODUCT = 'product';
    case DELIVERY = 'delivery';
    case RETURN = 'return';
    case OTHER = 'other';
}
