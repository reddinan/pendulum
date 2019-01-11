<?php
namespace Bytepath\Pendulum;

use Bytepath\Pendulum\Contracts\ImporterContract;
use Bytepath\Pendulum\Contracts\OutputContract;
use Bytepath\Pendulum\Contracts\PendulumContract;

class Pendulum
{
    /**
     * The order import class
     * @var ImporterContract
     */
    protected $importer = null;

    /**
     * The repository that interacts with the data store
     * @var \Iterator
     */
    protected $repository = null;

    /**
     * The class that will pass messages to the user via writing to a screen or whatever
     * @var OutputContract
     */
    protected $output = null;

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
     * This constructor requires you to pass in 1 or more objects that implement
     * Bytepath\Pendulum\Contracts\ImporterContract
     * \Iterator
     * (optional) Bytepath\Pendulum\Contracts\OutputContract
     * You can pass in one object that implements both of these
     * contracts, or two different objects each implement one of these contracts. If you pass in two (or more)
     * objects that both implement the same interface, the last one will be the one that this class will use.
     *
     * @param array ...$importerAndRepository one or more classes implementing the interfaces mentioned above
     */
    public function __construct(...$importerAndRepository)
    {
        // By default we don't display any output
        $this->output = new NullOutputWriter();

        //Import the repository and importer that are passed in. This can be one class or multiple
        $this->setRepositoryAndImporter($importerAndRepository);
    }

    /**
     * Unset all dependencies to prevent memory leaks
     */
    public function __destruct()
    {
        //Unset all of the dependencies just in case we cause a memory leak
        unset($this->importer);
        unset($this->repository);
        unset($this->output);
    }

    /**
     * Sets repository and importer classes. This can be one object or multiple
     * @param array $importerAndRepository an array containing one or more classes
     */
    protected function setRepositoryAndImporter($importerAndRepository)
    {
        foreach($importerAndRepository as $theObject){
            // Importer can be passed a normal array. If this happens wrap it in an iterator and use it as repository
            if(is_array($theObject)){
                $this->repository = new \ArrayIterator($theObject);
            }
            // Check the object to see what interfaces it implements. If this class implements ImporterContract,
            // use it as the importer. If it implements Iterator, use it as repository. The same class can be
            // used as both the importer and repository if it implements both interfaces
            else {
                $implements = class_implements($theObject);

                // Check if this class is the importer
                if (array_key_exists(ImporterContract::class, $implements)) {
                    $this->importer = $theObject;
                }

                // Check if this class is the repository
                if (array_key_exists(\Iterator::class, $implements)) {
                    $this->repository = $theObject;
                }

                // Check if this class is the output writer
                if (array_key_exists(OutputContract::class, $implements)) {
                    $this->output = $theObject;
                }
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
        // Notify that import has started and open any files or connections that are needed
        $this->output->importStarted();

        // Process each data item one by one
        foreach($this->getRepository() as $data){
            $this->processItem($data);
        }

        // Notify that import has completed and close any files or connections that are open
        $this->output->importComplete();

    }

    /**
     * @return \Iterator
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
     * Get the output writer
     * @return OutputContract
     */
    public function getOutputWriter()
    {
        return $this->output;
    }

    /**
     * Set the output writer
     * @param OutputContract $writer
     */
    public function setOutputWriter(OutputContract $writer)
    {
        $this->output = $writer;
    }

    /**
     * Process the list of orders
     * @param PendulumContract $item
     */
    protected function processItem($item)
    {
        $this->count++;
        $result = $this->importer->processItem($item);
        $this->itemProcessed($result, $item);
    }

    /**
     * Called after an item has been processed
     * @param mixed $result indicates success,failure,duplicate,etc
     * @param mixed $item the item that was imported
     */
    protected function itemProcessed($result, $item)
    {
        // Failed is a negative number so we add one to avoid any issues there
        $result++;

        // The three valid options that can be returned by the import class + 1 because failed is a negative number
        $options = [
            (ImporterContract::IMPORT_SUCCESS + 1) => "success",
            (ImporterContract::IMPORT_FAILED + 1) => "failure",
            (ImporterContract::ALREADY_IMPORTED + 1) => "duplicate"
        ];

        // If the imported item implements Bytepath\Pendulum\Contracts\PendulumContract then call the appropriate
        // method and pass the string it returns to the output writer class.
        // If the class doesn't implement that method just pass it an empty string
        $message = "";
        $method = "pendulum" . ucfirst($options[$result]);
        if(method_exists($item, $method)){
            $message = $item->{$method}();
        }

        $this->output->{$options[$result]}($message);
    }
}
