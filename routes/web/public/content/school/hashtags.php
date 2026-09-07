<?php
// Хештеги школы

use App\Http\Controllers\Public\PublicTemplateDispatcherController;
use Illuminate\Support\Facades\Route;

Route::get('/school/hashtags/{slug}', [PublicTemplateDispatcherController::class, 'dispatch'])
    ->defaults('_templateController', 'School\\SchoolHashtag\\SchoolHashtagController')
    ->defaults('_templateAction', 'show')
    ->name('public.schoolHashtags.show');
