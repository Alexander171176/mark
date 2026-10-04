<?php

use App\Http\Controllers\Public\Default\Form\FormSubmissionController;
use Illuminate\Support\Facades\Route;

Route::post(
    '/forms/{formCode}/submit',
    [FormSubmissionController::class, 'store']
)->name('forms.submit');
