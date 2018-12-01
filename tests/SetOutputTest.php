<?php
namespace Bytepath\Pendulum\Tests;

use Bytepath\Pendulum\NullOutputWriter;
use Bytepath\Pendulum\Pendulum;

class SetOutputTest extends \Bytepath\Pendulum\Tests\TestCase
{
    public function test_null_output_writer_is_set_by_default()
    {
        $bothContracts = new ImplementsBothContracts();
        $pendulum = new Pendulum($bothContracts);
        $this->assertIsClass(NullOutputWriter::class, $pendulum->getOutputWriter());
    }
}

