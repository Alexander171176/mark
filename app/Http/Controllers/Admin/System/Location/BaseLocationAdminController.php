<?php

namespace App\Http\Controllers\Admin\System\Location;

use App\Http\Controllers\Controller;
use App\Traits\Admin\System\Location\HasLocationActivityTrait;
use App\Traits\Admin\System\Location\HasLocationAdminCoreTrait;
use App\Traits\Admin\System\Location\HasLocationDefaultTrait;
use App\Traits\Admin\System\Location\HasLocationHierarchyTrait;
use App\Traits\Admin\System\Location\HasLocationSortingTrait;
use App\Traits\Admin\System\Location\HasLocationTranslationsTrait;

abstract class BaseLocationAdminController extends Controller
{
    use HasLocationAdminCoreTrait;
    use HasLocationTranslationsTrait;
    use HasLocationActivityTrait;
    use HasLocationSortingTrait;
    use HasLocationHierarchyTrait;
    use HasLocationDefaultTrait;

    /**
     * Модель сущности.
     */
    protected string $modelClass;

    /**
     * Название сущности для сообщений.
     */
    protected string $entityLabel = 'локации';

    /**
     * Поля переводов.
     */
    protected array $translationFields = [];
}
