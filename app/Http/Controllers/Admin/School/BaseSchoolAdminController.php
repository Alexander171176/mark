<?php

namespace App\Http\Controllers\Admin\School;

use App\Http\Controllers\Controller;
use App\Traits\Admin\School\HasSchoolActivityTrait;
use App\Traits\Admin\School\HasSchoolAdminCoreTrait;
use App\Traits\Admin\School\HasSchoolImagesTrait;
use App\Traits\Admin\School\HasSchoolPlacementTrait;
use App\Traits\Admin\School\HasSchoolSortingTrait;
use App\Traits\Admin\School\HasSchoolTranslationsTrait;

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
