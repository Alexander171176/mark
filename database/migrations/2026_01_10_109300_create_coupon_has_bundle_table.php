<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    // Привязка купона к конкретным бандлам
    public function up(): void
    {
        Schema::create('coupon_has_bundle', function (Blueprint $t) {
            $t->unsignedBigInteger('coupon_id');
            $t->unsignedBigInteger('bundle_id');

            $t->foreign('coupon_id')->references('id')->on('coupons')->cascadeOnDelete();
            $t->foreign('bundle_id')->references('id')->on('bundles')->cascadeOnDelete();

            $t->primary(['coupon_id', 'bundle_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_has_bundle');
    }
};
