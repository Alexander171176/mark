<?php

namespace App\Services\Admin;

class ProcessingModeService
{
    public function shouldUseServer(string $mode, int $total, int $threshold = 300): bool
    {
        return match ($mode) {
            'server' => true,
            'frontend' => false,
            'auto' => $total > $threshold,
            default => false,
        };
    }
}
