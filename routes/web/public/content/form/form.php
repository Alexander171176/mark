<?php

use App\Http\Controllers\Public\Default\Form\PublicFormSubmissionController;
use Illuminate\Support\Facades\Route;

Route::post(
    '/forms/{formCode}/submit',
    [PublicFormSubmissionController::class, 'store']
)->name('forms.submit');
