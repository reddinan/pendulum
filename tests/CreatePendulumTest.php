<?php
namespace Bytepath\Pendulum\Tests;

use Bytepath\Pendulum\Contracts\ImporterContract;
use Bytepath\Pendulum\Contracts\RepositoryContract;
use Bytepath\Pendulum\Pendulum;
use Mockery;
use \Iterator;

class CreatePendulumTest extends \Bytepath\Pendulum\Tests\TestCase
{
    public function test_can_create_with_one_object()
    {
        $bothContracts = new ImplementsBothContracts();
        $pendulum = new Pendulum($bothContracts);
        $this->assertIsClass(Pendulum::class, $pendulum);
        $this->assertIsClass(ImplementsBothContracts::class, $pendulum->getRepository());
        $this->assertIsClass(ImplementsBothContracts::class, $pendulum->getImporter());
    }

    public function test_can_create_with_multiple_objects()
    {
        $iterator = new ImplementsIterator();
        $importer = new ImplementsImporter();
        $pendulum = new Pendulum($iterator, $importer);
        $this->assertIsClass(Pendulum::class, $pendulum);
        $this->assertIsClass(ImplementsIterator::class, $pendulum->getRepository());
        $this->assertIsClass(ImplementsImporter::class, $pendulum->getImporter());
    }

    public function test_can_create_with_multiple_objects_in_any_order()
    {
        $iterator = new ImplementsIterator();
        $importer = new ImplementsImporter();

        // Note we are passing these in the opposite order as we do in the previous test
        $pendulum = new Pendulum($importer, $iterator);
        $this->assertIsClass(Pendulum::class, $pendulum);
        $this->assertIsClass(ImplementsIterator::class, $pendulum->getRepository());
        $this->assertIsClass(ImplementsImporter::class, $pendulum->getImporter());
    }

    public function test_uses_the_last_object_to_implement_each_interface()
    {
        $iterator = new ImplementsIterator();
        $alternateIterator = new AlternateIterator();
        $importer = new ImplementsImporter();
        $alternateImporter = new AlternateImporter();

        // Note we are passing these in the opposite order as we do in the previous test
        $pendulum = new Pendulum($importer, $iterator, $alternateImporter, $alternateIterator);
        $this->assertIsClass(Pendulum::class, $pendulum);
        $this->assertIsClass(AlternateIterator::class, $pendulum->getRepository());
        $this->assertIsClass(AlternateImporter::class, $pendulum->getImporter());
    }
}

class ImplementsBothContracts implements ImporterContract, Iterator
{
    public function processItem($item){}
     public function current(){}
     public function key(){}
     public function next (){}
     public function rewind (){}
     public function valid (){}
}

class ImplementsIterator implements Iterator
{
    public function current(){}
    public function key(){}
    public function next (){}
    public function rewind (){}
    public function valid (){}
}


class AlternateIterator implements Iterator
{
    public function current(){}
    public function key(){}
    public function next (){}
    public function rewind (){}
    public function valid (){}
}

class ImplementsImporter implements ImporterContract
{
    public function processItem($item){}
}

class AlternateImporter implements ImporterContract
{
    public function processItem($item){}
}