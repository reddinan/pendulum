<?php
namespace Bytepath\Pendulum\Contracts;

use Illuminate\Support\Collection;

interface RepositoryContract
{
    /**
     * Return a collection of items you want to import into your application
     * @return Collection
     */
    public function getImportableItems();
}