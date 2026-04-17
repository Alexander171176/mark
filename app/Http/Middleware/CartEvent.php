<?php

namespace App\Http\Middleware;

final class CartEvent
{
    // lifecycle
    public const CREATED = 'created';
    public const RESTORED = 'restored';
    public const MERGED = 'merged';
    public const CLEARED = 'cleared';

    // items
    public const ITEM_ADDED = 'item_added';
    public const ITEM_REMOVED = 'item_removed';
    public const ITEM_CHECKED_CHANGED = 'item_checked_changed';
    public const ITEM_QTY_CHANGED = 'item_qty_changed';

    // pricing
    public const PRICE_SNAPSHOT_SET = 'price_snapshot_set';
    public const PRICE_RECALCULATED = 'price_recalculated';

    // state
    public const ABANDONED_MARKED = 'abandoned_marked';
    public const CONVERTED_TO_ORDER = 'converted_to_order';
    public const EXPIRED = 'expired';

    // misc
    public const NOTE = 'note';
}
