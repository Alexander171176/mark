<?php
// Страница конструктора (Index.vue)

use App\Http\Controllers\Admin\Constructor\HomePage\HomePageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomePageController::class, 'index'])->name('index');
