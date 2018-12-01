<?php
namespace Bytepath\Pendulum\Contracts;

use Bytepath\Pendulum\Contracts\PendulumContract;
use Illuminate\Support\Collection;

interface ImporterContract
{
    // Successfully imported item
    const IMPORT_SUCCESS = 1;

    // Failed to import item
    const IMPORT_FAILED = -1;

    // Item was already imported
    const ALREADY_IMPORTED = 0;

    /**
     * Import an item into the system
     * @param PendulumContract $item The item you want to import into the system
     * @return integer 1=success, 0=duplicate, -1=failed
     */
    public function processItem(&$item);
}