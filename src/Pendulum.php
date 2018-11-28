<?php
namespace Bytepath\Pendulum;

use Bytepath\Pendulum\Contracts\ImporterContract;
use Bytepath\Pendulum\Contracts\PendulumContract;
use Bytepath\Pendulum\Contracts\RepositoryContract;
use Illuminate\Console\Command;

abstract class Pendulum extends Command
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
     * The order import class
     * @var ImporterContract
     */
    protected $import = null;

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Process any options passed in
        $this->processOptions();

        $this->import = $this->getImporter();
        $this->repository = $this->getRepository();

        $this->info("Running " . (static::class));

        try {
            $orders = $this->repository->getOrdersStartingAt(0, 1, $this->classToClosure([&$this, 'processOrders']));
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
     * @param $list an array containing a row from the CSV file
     */
    protected function processOrders($list)
    {
        $count = 0;
        $list->each(function(PendulumContract $item) use(&$count){
            $result = $this->import->processItem($item);
            if($result > 0)
            {
                if($this->showSuccess){
                    $this->info($this->signature . " " . $item->pendulumSuccess());
                }
            }
            else if($result < 0)
            {
                if($this->showFailures){
                    $this->error($this->signature . " " . $item->pendulumFailed());
                }
            }
            else
            {
                if($this->showWarnings){
                    $this->warn($this->signature . " " . $item->pendulumDuplicate());
                }
            }
        });
    }

    /**
     * A small hack to allow us to pass this class as a callback to the repository
     * @param array $callable
     * @return \Closure
     */
    public function classToClosure(array $callable)
    {
        return function () use ($callable) {
            call_user_func_array($callable, func_get_args());
        };
    }

    /**
     * Get the respository class that will handle the data store
     * @return RepositoryContract
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
