<?php
namespace Bytepath\Pendulum\Contracts;

interface ImporterContract
{
    // Successfully imported item
    const IMPORT_SUCCESS = 1;

    // Failed to import item
    const IMPORT_FAILED = -1;

    // Item was already imported
    const ALREADY_IMPORTED = 0;

    /**
     * Import a single piece of data into your application
     * Returns one of the following constants
     * ImporterContract::IMPORT_SUCCESS -- We imported the data sucessfully
     * ImporterContract::IMPORT_FAILED -- We failed to import this item
     * ImporterContract::ALREADY_IMPORTED -- This is a duplicate item
     *
     * @param PendulumContract $item The item you want to import into the system
     * @return integer returns one of the constants listed in the ImporterContract Interface
     */
    public function processItem(&$item);
}