<?php

namespace App\Enum;

enum InventoryMovementReason: string
{
    case SALE = 'sale';
    case REFUND = 'refund';
    case CANCELLED_ORDER = 'cancelled_order';
    case RETURN = 'return';

    case RESTOCK = 'restock';
    case MANUAL_ADJUSTMENT = 'manual_adjustment';
    case INVENTORY_COUNT = 'inventory_count';
    case DAMAGE = 'damage';
    case LOST = 'lost';
    case STOLEN = 'stolen';

    case INITIAL_STOCK = 'initial_stock';
    case MIGRATION = 'migration';
}
