<?php
namespace Bytepath\Pendulum\Contracts;

use Bytepath\Pendulum\Contracts\PendulumContract;
use Illuminate\Support\Collection;

interface ImporterContract
{
    /**
     * Import an item into the system
     * @param PendulumContract $item The item you want to import into the system
     * @return integer 1=success, 0=duplicate, -1=failed
     */
    public function processItem($item);
}