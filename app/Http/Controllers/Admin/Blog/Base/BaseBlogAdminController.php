<?php

namespace App\Http\Controllers\Admin\Blog\Base;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Blog\Base\Traits\HasBlogActivityTrait;
use App\Http\Controllers\Admin\Blog\Base\Traits\HasBlogAdminCoreTrait;
use App\Http\Controllers\Admin\Blog\Base\Traits\HasBlogModerationTrait;
use App\Http\Controllers\Admin\Blog\Base\Traits\HasBlogSortingTrait;
use App\Http\Controllers\Admin\Blog\Base\Traits\HasBlogTranslationsTrait;

abstract class BaseBlogAdminController extends Controller
{
    use HasBlogAdminCoreTrait;
    use HasBlogTranslationsTrait;
    use HasBlogActivityTrait;
    use HasBlogSortingTrait;
    use HasBlogModerationTrait;

    /** Модель сущности */
    protected string $modelClass;

    /** Название сущности для сообщений */
    protected string $entityLabel = 'элемент';

    /** Поля переводов */
    protected array $translationFields = [];
}
