<?php

namespace App\Http\Resources\Admin\School\SchoolBundlePrice;

use App\Http\Resources\Admin\School\SchoolBundle\SchoolBundleSharedResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchoolBundlePriceSharedResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' =>
                $this->id,

            'school_bundle_id' =>
                $this->school_bundle_id,

            'currency_id' =>
                $this->currency_id,

            /**
             * Набор курсов.
             *
             * Admin Controller заранее загружает:
             *
             * bundle.translations(currentLocale)
             */
            'bundle' =>
                new SchoolBundleSharedResource(
                    $this->whenLoaded(
                        'bundle'
                    )
                ),

            /**
             * Валюта.
             */
            'currency' => $this->whenLoaded(
                'currency',
                fn () => [
                    'id' =>
                        $this->currency->id,

                    'code' =>
                        $this->currency->code,

                    'name' =>
                        $this->currency->name,

                    'symbol' =>
                        $this->currency->symbol,
                ]
            ),

            /**
             * Цены.
             */
            'price' =>
                (string) $this->price,

            'sale_price' =>
                $this->sale_price !== null
                    ? (string) $this->sale_price
                    : null,

            'compare_at_price' =>
                $this->compare_at_price !== null
                    ? (string) $this->compare_at_price
                    : null,

            /**
             * Вычисляемые значения.
             */
            'effective_price' =>
                (string) $this->effective_price,

            'has_discount' =>
                (bool) $this->has_discount,

            'discount_amount' =>
                $this->discount_amount !== null
                    ? (string) $this->discount_amount
                    : null,

            'discount_percent' =>
                $this->discount_percent !== null
                    ? (float) $this->discount_percent
                    : null,

            /**
             * Период действия цены.
             */
            'starts_at' =>
                $this->starts_at?->toISOString(),

            'ends_at' =>
                $this->ends_at?->toISOString(),

            /**
             * Состояние.
             */
            'activity' =>
                (bool) $this->activity,

            'sort' =>
                (int) $this->sort,

            /**
             * Системные даты нужны
             * frontend-сортировке Index.
             */
            'created_at' =>
                $this->created_at?->toISOString(),

            'updated_at' =>
                $this->updated_at?->toISOString(),
        ];
    }
}
