<?php

namespace App\Http\Controllers\Public\Pulsar;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{

    /**
     * Главная страница
     */
    public function index(): Response
    {

        return Inertia::render('Public/Pulsar/Index');
    }
}
