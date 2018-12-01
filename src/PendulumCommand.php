<?php
namespace Bytepath\Pendulum;

use Bytepath\Pendulum\Contracts\ImporterContract;
use Bytepath\Pendulum\Contracts\PendulumContract;
use Bytepath\Pendulum\Contracts\RepositoryContract;
use Illuminate\Console\Command;

abstract class PendulumCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
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
            $this->import->notifyImportComplete();
        }
    }

    /**
     * Process the list of orders
     * @param PendulumContract $item
     */
    protected function processItem($item)
    {
        $this->count++;
        $result = $this->import->processItem($item);
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

    /**
     * Get the respository class that will handle the data store
     * @return \Iterator
     */
    abstract protected function getRepository();

    /**
     * Get the class that will actually import the data
     * @return ImporterContract
     */
    abstract protected function getImporter();

    /**
     * Process any options that were provided on the command line
     * @return mixed
     */
    protected function processOptions(){}
}
