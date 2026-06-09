<?php

namespace App\Http\Controllers\Admin\Blog;

use App\Http\Controllers\Controller;
use App\Traits\Admin\Blog\HasBlogActivityTrait;
use App\Traits\Admin\Blog\HasBlogAdminCoreTrait;
use App\Traits\Admin\Blog\HasBlogImagesTrait;
use App\Traits\Admin\Blog\HasBlogModerationTrait;
use App\Traits\Admin\Blog\HasBlogPlacementTrait;
use App\Traits\Admin\Blog\HasBlogSortingTrait;
use App\Traits\Admin\Blog\HasBlogTranslationsTrait;

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
