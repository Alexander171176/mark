<?php

namespace App\Http\Controllers\Admin\School\Base;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\School\Base\Traits\HasSchoolActivityTrait;
use App\Http\Controllers\Admin\School\Base\Traits\HasSchoolAdminCoreTrait;
use App\Http\Controllers\Admin\School\Base\Traits\HasSchoolImagesTrait;
use App\Http\Controllers\Admin\School\Base\Traits\HasSchoolPlacementTrait;
use App\Http\Controllers\Admin\School\Base\Traits\HasSchoolSortingTrait;
use App\Http\Controllers\Admin\School\Base\Traits\HasSchoolTranslationsTrait;

abstract class BaseSchoolAdminController extends Controller
{
    use HasSchoolAdminCoreTrait;    // базовая логика: локали, сортировка
    use HasSchoolTranslationsTrait; // работа с переводами (sync translations)
    use HasSchoolActivityTrait;     // управление активностью (вкл/выкл)
    use HasSchoolSortingTrait;      // управление сортировкой (одиночной и массовой)
    use HasSchoolPlacementTrait;    // позиции (left / main / right)
    use HasSchoolImagesTrait;       // работа с изображениями (sync / delete)

    /** Модель сущности */
    protected string $modelClass;

    /** Название сущности для сообщений */
    protected string $entityLabel = 'элемент';

    /** Поля переводов */
    protected array $translationFields = [];


    /** Коллекция Spatie Media Library */
    protected string $imageMediaCollection = 'images';
}
