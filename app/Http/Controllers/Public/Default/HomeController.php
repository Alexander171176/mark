<?php

namespace App\Http\Controllers\Public\Default;

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

        return Inertia::render('Public/Default/Index', [

        ]);
    }
}
