<?php

namespace App\Http\Controllers\Admin\Form;

use App\Http\Controllers\Controller;
use App\Traits\Admin\Form\HasFormAdminCoreTrait;

abstract class BaseFormAdminController extends Controller
{
    use HasFormAdminCoreTrait;

    /**
     * Основная модель административной сущности.
     */
    protected string $modelClass;

    /**
     * Название сущности для сообщений.
     */
    protected string $entityLabel = 'элемент';

    /**
     * Использует ли сущность прямое ограничение
     * владельца через собственную колонку user_id.
     *
     * По умолчанию прямого owner scope нет.
     *
     * Вложенные сущности должны переопределять
     * baseQuery() и ограничивать доступ
     * через родительскую сущность.
     */
    protected bool $ownerScoped = false;
}
