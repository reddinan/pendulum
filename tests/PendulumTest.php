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

    public function test_output_success_is_called_if_imported_successfully()
    {
        $importer = $this->getMockImporter(ImporterContract::IMPORT_SUCCESS);
        $output = $this->getMockOutputWriter();
        $output->shouldReceive("success")->once();

        $pendulum = new Pendulum($importer, $this->createArrayIterator(["cats"]));
        $pendulum->setOutputWriter($output);
        $pendulum->import();
    }

    public function test_output_failure_is_called_if_imported_successfully()
    {
        $importer = $this->getMockImporter(ImporterContract::IMPORT_FAILED);
        $output = $this->getMockOutputWriter();
        $output->shouldReceive("failure")->once();

        $pendulum = new Pendulum($importer, $this->createArrayIterator(["cats"]));
        $pendulum->setOutputWriter($output);
        $pendulum->import();
    }

    public function test_output_duplicate_is_called_if_imported_successfully()
    {
        $importer = $this->getMockImporter(ImporterContract::ALREADY_IMPORTED);
        $output = $this->getMockOutputWriter();
        $output->shouldReceive("duplicate")->once();

        $pendulum = new Pendulum($importer, $this->createArrayIterator(["cats"]));
        $pendulum->setOutputWriter($output);
        $pendulum->import();
    }


    protected function createArrayIterator($array)
    {
        foreach($array as $key => $item) {
            $array[$key] = new SampleImportable($item);
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
    public $failed = false;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function pendulumSuccess()
    {
        $this->success = true;
        return "SUCCESS " . $this->data;
    }


    public function pendulumFailed()
    {
        $this->success = true;
        return "FAILED " . $this->data;
    }


    public function pendulumDuplicate()
    {
        $this->success = true;
        return "DUPLICATE " . $this->data;
    }
}