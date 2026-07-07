<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Traits\Admin\Cms\HasCmsActivityTrait;
use App\Traits\Admin\Cms\HasCmsAdminCoreTrait;
use App\Traits\Admin\Cms\HasCmsSortingTrait;
use App\Traits\Admin\Cms\HasCmsTranslationsTrait;

abstract class BaseCmsAdminController extends Controller
{
    use HasCmsAdminCoreTrait;       // базовая логика: локали, baseQuery
    use HasCmsTranslationsTrait;    // работа с переводами
    use HasCmsActivityTrait;        // управление активностью
    use HasCmsSortingTrait;         // управление сортировкой

    /** Модель сущности */
    protected string $modelClass;

    /** Название сущности для сообщений */
    protected string $entityLabel = 'элемент';

    /** Поля переводов */
    protected array $translationFields = [];
}
