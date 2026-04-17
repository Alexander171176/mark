<?php

namespace App\Services\Market;

use App\Models\Admin\Market\Cart\Cart;
use App\Models\Admin\Market\CartEvent\CartEvent;
use App\Models\Admin\Market\CartItem\CartItem;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function addItem(int $cartId, array $data, ?int $userId = null): void
    {
        // $data: product_id, product_variant_id?, qty, price_snapshot?, currency_id?
        DB::transaction(function () use ($cartId, $data, $userId) {

            $cart = Cart::query()->lockForUpdate()->findOrFail($cartId);

            // upsert item (по variant если есть, иначе по product)
            $itemQuery = CartItem::query()->where('cart_id', $cart->id);

            if (!empty($data['product_variant_id'])) {
                $itemQuery->where('product_variant_id', $data['product_variant_id']);
            } else {
                $itemQuery->whereNull('product_variant_id')
                    ->where('product_id', $data['product_id']);
            }

            $item = $itemQuery->first();

            if ($item) {
                $from = (string) $item->qty;
                $item->qty = (int)$item->qty + (int)($data['qty'] ?? 1);
                $item->save();

                $this->event($cart, 'item_qty_changed', $userId, [
                    'cart_item_id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'field' => 'qty',
                    'from_value' => $from,
                    'to_value' => (string)$item->qty,
                    'payload' => ['delta' => (int)($data['qty'] ?? 1)],
                ]);
            } else {
                $item = CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $data['product_id'],
                    'product_variant_id' => $data['product_variant_id'] ?? null,
                    'qty' => (int)($data['qty'] ?? 1),
                    'checked' => true,
                    'price_snapshot' => $data['price_snapshot'] ?? null,
                    'currency_id' => $data['currency_id'] ?? null,
                    'activity' => true,
                    'sort' => 0,
                ]);

                $this->event($cart, 'item_added', $userId, [
                    'cart_item_id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'payload' => ['qty' => $item->qty],
                ]);
            }

            $cart->last_activity_at = now();
            $cart->save();
        });
    }

    public function changeQty(int $cartId, int $itemId, int $qty, ?int $userId = null): void
    {
        DB::transaction(function () use ($cartId, $itemId, $qty, $userId) {
            $cart = Cart::query()->lockForUpdate()->findOrFail($cartId);

            $item = CartItem::query()
                ->where('cart_id', $cart->id)
                ->findOrFail($itemId);

            $from = (string)$item->qty;

            if ($qty <= 0) {
                $item->delete();

                $this->event($cart, 'item_removed', $userId, [
                    'cart_item_id' => $itemId,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'payload' => ['reason' => 'qty<=0', 'from_qty' => $from],
                ]);
            } else {
                $item->qty = $qty;
                $item->save();

                $this->event($cart, 'item_qty_changed', $userId, [
                    'cart_item_id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'field' => 'qty',
                    'from_value' => $from,
                    'to_value' => (string)$qty,
                ]);
            }

            $cart->last_activity_at = now();
            $cart->save();
        });
    }

    public function toggleChecked(int $cartId, int $itemId, bool $checked, ?int $userId = null): void
    {
        DB::transaction(function () use ($cartId, $itemId, $checked, $userId) {
            $cart = Cart::query()->lockForUpdate()->findOrFail($cartId);

            $item = CartItem::query()
                ->where('cart_id', $cart->id)
                ->findOrFail($itemId);

            $from = $item->checked ? '1' : '0';

            $item->checked = $checked;
            $item->save();

            $this->event($cart, 'item_checked_changed', $userId, [
                'cart_item_id' => $item->id,
                'product_id' => $item->product_id,
                'product_variant_id' => $item->product_variant_id,
                'field' => 'checked',
                'from_value' => $from,
                'to_value' => $checked ? '1' : '0',
            ]);

            $cart->last_activity_at = now();
            $cart->save();
        });
    }

    private function event($cart, string $event, ?int $userId, array $data = []): void
    {
        CartEvent::create([
            'cart_id' => $cart->id,
            'storefront_id' => $cart->storefront_id,
            'user_id' => $userId,
            'event' => $event,
            'cart_item_id' => $data['cart_item_id'] ?? null,
            'product_id' => $data['product_id'] ?? null,
            'product_variant_id' => $data['product_variant_id'] ?? null,
            'field' => $data['field'] ?? null,
            'from_value' => $data['from_value'] ?? null,
            'to_value' => $data['to_value'] ?? null,
            'price_snapshot' => $data['price_snapshot'] ?? null,
            'currency_id' => $data['currency_id'] ?? null,
            'ip' => request()?->ip(),
            'user_agent' => substr((string)request()?->userAgent(), 0, 255),
            'payload' => $data['payload'] ?? null,
            'event_at' => now(),
        ]);
    }
}
