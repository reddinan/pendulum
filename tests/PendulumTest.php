<?php
namespace Bytepath\Pendulum\Tests;

use Bytepath\FlashBang\Flasher;
use Bytepath\FlashBang\BPSessionManager;
use Bytepath\Pendulum\Contracts\ImporterContract;
use Bytepath\Pendulum\Contracts\OutputContract;
use Bytepath\Pendulum\Contracts\PendulumContract;
use Bytepath\Pendulum\Pendulum;
use Mockery;

class PendulumTest extends \Bytepath\Pendulum\Tests\TestCase
{
    public function test_importer_is_called_once_for_each_item()
    {
        $importer = $this->getMockImporter(ImporterContract::IMPORT_SUCCESS, 3);
        $pendulum = new Pendulum($importer, $this->createArrayIterator(["dogs", "cats", "pigs"]));
        $pendulum->import();
    }

    public function test_can_use_php_array_as_repository()
    {
        $array = [
            new SampleImportable("cats"),
            new SampleImportable("dogs"),
            new SampleImportable("pigs")
        ];

        $importer = $this->getMockImporter(ImporterContract::IMPORT_SUCCESS, 3);
        $output = $this->getMockOutputWriter();
        $output->shouldReceive("success")->times(3);

        $pendulum = new Pendulum($importer, $array);
        $pendulum->setOutputWriter($output);
        $pendulum->import();
    }

    public function test_can_use_php_generator_as_repository()
    {
        $generator = function(Array $items){
            foreach($items as $item){
                yield new SampleImportable($item);
            }
        };

        $importer = $this->getMockImporter(ImporterContract::IMPORT_SUCCESS, 4);
        $output = $this->getMockOutputWriter();
        $output->shouldReceive("success")->times(4);

        $pendulum = new Pendulum($importer, $generator(['cats', 'dogs', 'pigs', 'rats']));
        $pendulum->setOutputWriter($output);
        $pendulum->import();
    }

    public function test_output_success_is_called_if_imported_successfully()
    {
        $importer = $this->getMockImporter(ImporterContract::IMPORT_SUCCESS);
        $output = $this->getMockOutputWriter();
        $output->shouldReceive("success")->once();
        $list = $this->createArrayIterator(["cats"]);

        $pendulum = new Pendulum($importer, $list);
        $pendulum->setOutputWriter($output);
        $pendulum->import();

        // The list item should have success set to true because we called the pendulumSuccess function
        $this->assertTrue($list[0]->success);
    }

    public function test_output_failure_is_called_if_imported_fails()
    {
        $importer = $this->getMockImporter(ImporterContract::IMPORT_FAILED);
        $output = $this->getMockOutputWriter();
        $output->shouldReceive("failure")->once();
        $list = $this->createArrayIterator(["cats"]);

        $pendulum = new Pendulum($importer, $list);
        $pendulum->setOutputWriter($output);
        $pendulum->import();

        // The list item should have success set to true because we called the pendulumSuccess function
        $this->assertTrue($list[0]->failure);
    }

    public function test_output_duplicate_is_called_if_already_imported()
    {
        $importer = $this->getMockImporter(ImporterContract::ALREADY_IMPORTED);
        $output = $this->getMockOutputWriter();
        $output->shouldReceive("duplicate")->once();
        $list = $this->createArrayIterator(["cats"]);

        $pendulum = new Pendulum($importer, $list);
        $pendulum->setOutputWriter($output);
        $pendulum->import();

        // The list item should have duplicate set to true because we called the pendulumDuplicate function
        $this->assertTrue($list[0]->duplicate);
    }

    /**
     * NoContractImportable does not have success, failure and duplicate functions, so this will fail if
     * pendulum called these functions.
     */
    public function test_pendulumSuccess_is_not_called_if_the_importable_item_doesnt_implement_pendulum_contract()
    {
        $importer = $this->getMockImporter(ImporterContract::IMPORT_SUCCESS);
        $list = $this->noContractArrayIterator(["cats"]);
        $pendulum = new Pendulum($importer, $list);
        $pendulum->import();

        // The list item should have success set to true because we called the pendulumSuccess function
        $this->assertFalse($list[0]->success);
    }

    /**
     * NoContractImportable does not have success, failure and duplicate functions, so this will fail if
     * pendulum called these functions.
     */
    public function test_pendulumFailure_is_not_called_if_the_importable_item_doesnt_implement_pendulum_contract()
    {
        $importer = $this->getMockImporter(ImporterContract::IMPORT_FAILED);
        $list = $this->noContractArrayIterator(["cats"]);
        $pendulum = new Pendulum($importer, $list);
        $pendulum->import();

        // The list item should have success set to true because we called the pendulumSuccess function
        $this->assertFalse($list[0]->failure);
    }

    /**
     * NoContractImportable does not have success, failure and duplicate functions, so this will fail if
     * pendulum called these functions.
     */
    public function test_pendulumDuplicate_is_not_called_if_the_importable_item_doesnt_implement_pendulum_contract()
    {
        $importer = $this->getMockImporter(ImporterContract::ALREADY_IMPORTED);
        $list = $this->noContractArrayIterator(["cats"]);
        $pendulum = new Pendulum($importer, $list);
        $pendulum->import();

        // The list item should have success set to true because we called the pendulumSuccess function
        $this->assertFalse($list[0]->duplicate);
    }

    protected function createArrayIterator($array)
    {
        foreach($array as $key => $item) {
            $array[$key] = new SampleImportable($item);
        }

        return new \ArrayIterator($array);
    }

    protected function noContractArrayIterator($array)
    {
        foreach($array as $key => $item) {
            $array[$key] = new NonContractImportable($item);
        }

        return new \ArrayIterator($array);
    }

    protected function getMockOutputWriter($startedTimes = 1, $completedTimes = 1)
    {
        $writer = Mockery::mock(OutputContract::class);
        $writer->shouldReceive("importStarted")->times($startedTimes);
        $writer->shouldReceive("importComplete")->times($completedTimes);
        return $writer;
    }

    protected function getMockImporter($result, $times = 1)
    {
        $importer = Mockery::mock(ImporterContract::class);
        $importer->shouldReceive("processItem")->times($times)->andReturn($result);
        return $importer;
    }
}

class SampleImportable implements PendulumContract
{
    public $data = null;
    public $success = false;
    public $duplicate = false;
    public $failure = false;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function pendulumSuccess()
    {
        $this->success = true;
        return "SUCCESS " . $this->data;
    }


    public function pendulumFailure()
    {
        $this->failure = true;
        return "FAILED " . $this->data;
    }


    public function pendulumDuplicate()
    {
        $this->duplicate = true;
        return "DUPLICATE " . $this->data;
    }
}

class NonContractImportable
{
    public $data = null;
    public $success = false;
    public $duplicate = false;
    public $failure = false;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function __call($name, $arguments)
    {
        // if success, duplicate, or failed are called this will set them to true
        $this->{str_replace("pendulum", "", $name)} = true;
    }
}