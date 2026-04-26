<?php

namespace App\Http\Controllers\Admin\Blog\Base;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Blog\Base\Traits\HasBlogActivityTrait;
use App\Http\Controllers\Admin\Blog\Base\Traits\HasBlogAdminCoreTrait;
use App\Http\Controllers\Admin\Blog\Base\Traits\HasBlogModerationTrait;
use App\Http\Controllers\Admin\Blog\Base\Traits\HasBlogSortingTrait;
use App\Http\Controllers\Admin\Blog\Base\Traits\HasBlogTranslationsTrait;
use App\Http\Controllers\Admin\Blog\Base\Traits\HasBlogPlacementTrait;
use App\Http\Controllers\Admin\Blog\Base\Traits\HasBlogImagesTrait;

abstract class BaseBlogAdminController extends Controller
{
    use HasBlogAdminCoreTrait;       // базовая логика: локали, baseQuery, сортировка
    use HasBlogTranslationsTrait;    // работа с переводами (sync translations)
    use HasBlogActivityTrait;        // управление активностью (вкл/выкл)
    use HasBlogSortingTrait;         // управление сортировкой (одиночной и массовой)
    use HasBlogModerationTrait;      // модерация (approve / статус)
    use HasBlogPlacementTrait;       // позиции (left / main / right)
    use HasBlogImagesTrait;          // работа с изображениями (sync / delete)

    /** Модель сущности */
    protected string $modelClass;

    /** Название сущности для сообщений */
    protected string $entityLabel = 'элемент';

    /** Поля переводов */
    protected array $translationFields = [];
}
