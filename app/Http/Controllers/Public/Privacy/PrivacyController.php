<?php

namespace App\Http\Controllers\Public\Privacy;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class PrivacyController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Public/Privacy/Index');
    }
}
