<?php
namespace Bytepath\Pendulum;

use Bytepath\Pendulum\Contracts\ImporterContract;
use Bytepath\Pendulum\Contracts\PendulumContract;
use Bytepath\Pendulum\Contracts\RepositoryContract;
use Illuminate\Console\Command;

class Pendulum
{
    /**
     * The order import class
     * @var ImporterContract
     */
    protected $importer = null;

    /**
     * The repository that interacts with the data store
     * @var RepositoryContract
     */
    protected $repository = null;

    /**
     * Set to true to send a notification when completed
     * @var bool
     */
    protected $shouldNotifyWhenComplete = true;

    /**
     * Should import warnings be displayed
     * @var bool
     */
    protected $showWarnings = true;

    /**
     * Should import successes be displayed
     */
    protected $showSuccess = true;

    /**
     * Should import failures be displayed
     */
    protected $showFailures = true;

    /**
     * The number of items we have processed
     * @var int
     */
    protected $count = 0;

    /**
     * Requires you to pass in one or more classes. Read full description
     * This constructor requires you to pass in objects that implement Bytepath\Pendulum\Contracts\ImporterContract and
     * Bytepath\Pendulum\Contracts\RepositoryContract. You can pass in one object that implements both of these
     * contracts, or two different objects each implement one of these contracts. If you pass in two (or more)
     * objects that both implement the same interface, the last one will be the one that this class will use.
     *
     * @param array ...$importerAndRepository one or more classes implementing the interfaces mentioned above
     */
    public function __construct(...$importerAndRepository)
    {
        //Import the repository and importer that are passed in. This can be one class or multiple
        $this->setRepositoryAndImporter($importerAndRepository);
    }

    /**
     * Sets repository and importer classes. This can be one object or multiple
     * @param array $importerAndRepository an array containing one or more classes
     */
    protected function setRepositoryAndImporter($importerAndRepository)
    {
        foreach($importerAndRepository as $theObject){
            $implements = class_implements($theObject);

            // Check if this class is the importer
            if(array_key_exists(ImporterContract::class, $implements)){
                $this->importer = $theObject;
            }

            // Check if this class is the repository
            if(array_key_exists(\Iterator::class, $implements)){
                $this->repository = $theObject;
            }
        }
    }

    /**
     * Start the import process.
     *
     * @return mixed
     */
    public function import()
    {
        // Process any options passed in
        $this->processOptions();

        // Retreive the code that will perform the import
        $this->import = $this->getImporter();

        try {
            foreach($this->getRepository() as $data){
                $this->processItem($data);
            }
        }
        catch(\Exception $e) {
            $this->error($e->getMessage());
        }

        // Send notification that import has completed
        if($this->shouldNotifyWhenComplete){
            $this->importer->notifyImportComplete();
        }
    }

    /**
     * @return RepositoryContract
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return ImporterContract
     */
    public function getImporter()
    {
        return $this->importer;
    }

    /**
     * Process the list of orders
     * @param PendulumContract $item
     */
    protected function processItem($item)
    {
        $this->count++;
        $result = $this->importer->processItem($item);
        if($result == ImporterContract::IMPORT_SUCCESS)
        {
            if($this->showSuccess){
                $this->info($this->signature . " " . $item->pendulumSuccess());
            }
        }
        else if($result == ImporterContract::IMPORT_FAILED)
        {
            if($this->showFailures){
                $this->error($this->signature . " " . $item->pendulumFailed());
            }
        }
        else if($result == ImporterContract::ALREADY_IMPORTED)
        {
            if($this->showWarnings){
                $this->warn($this->signature . " " . $item->pendulumDuplicate());
            }
        }
    }
}
