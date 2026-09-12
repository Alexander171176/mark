<?php

namespace App\Traits\Admin\System\Location;

use InvalidArgumentException;

trait HasLocationHierarchyTrait
{
    /**
     * Проверка допустимого родителя.
     *
     * Защищает от:
     * - parent = сама Location;
     * - циклической иерархии.
     */
    protected function ensureValidParent(
        ?int $parentId,
        ?int $locationId = null
    ): void {
        if (!$parentId) {
            return;
        }

        if (
            $locationId
            && $parentId === $locationId
        ) {
            throw new InvalidArgumentException(
                'Локация не может быть родителем самой себе.'
            );
        }

        if (!$locationId) {
            return;
        }

        $currentParentId =
            $parentId;

        while ($currentParentId) {
            if (
                $currentParentId
                === $locationId
            ) {
                throw new InvalidArgumentException(
                    'Нельзя создать циклическую иерархию локаций.'
                );
            }

            $currentParentId =
                $this->baseQuery()
                    ->whereKey(
                        $currentParentId
                    )
                    ->value(
                        'parent_id'
                    );
        }
    }

    /**
     * Запрет удаления Location,
     * имеющей дочерние элементы.
     */
    protected function ensureLocationHasNoChildren(
        object $location
    ): void {
        if (
            $location
                ->children()
                ->exists()
        ) {
            throw new InvalidArgumentException(
                'Нельзя удалить локацию: сначала удалите или переместите дочерние локации.'
            );
        }
    }
}
