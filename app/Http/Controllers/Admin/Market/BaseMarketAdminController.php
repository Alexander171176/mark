<?php

namespace App\Http\Controllers\Admin\Market;

use App\Http\Controllers\Controller;
use App\Traits\Admin\Market\HasMarketActivityTrait;
use App\Traits\Admin\Market\HasMarketAdminCoreTrait;
use App\Traits\Admin\Market\HasMarketImagesTrait;
use App\Traits\Admin\Market\HasMarketModerationTrait;
use App\Traits\Admin\Market\HasMarketPlacementTrait;
use App\Traits\Admin\Market\HasMarketSortingTrait;
use App\Traits\Admin\Market\HasMarketTranslationsTrait;

abstract class BaseMarketAdminController extends Controller
{
    use HasMarketAdminCoreTrait;       // базовая логика: локали, baseQuery, сортировка
    use HasMarketTranslationsTrait;    // работа с переводами (sync translations)
    use HasMarketActivityTrait;        // управление активностью (вкл/выкл)
    use HasMarketSortingTrait;         // управление сортировкой (одиночной и массовой)
    use HasMarketModerationTrait;      // модерация (approve / статус)
    use HasMarketPlacementTrait;       // позиции (left / main / right)
    use HasMarketImagesTrait;          // работа с изображениями (sync / delete)

    /** Модель сущности */
    protected string $modelClass;

    /** Название сущности для сообщений */
    protected string $entityLabel = 'элемент';

    /** Поля переводов */
    protected array $translationFields = [];
}
