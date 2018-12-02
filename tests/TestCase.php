<?php

namespace Bytepath\Pendulum\Tests;

use Bytepath\Pendulum\Pendulum;
use Bytepath\Pendulum\PendulumServiceProvider;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as DBCollection;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    /**
     * Load package service provider
     * @param  \Illuminate\Foundation\Application $app
     * @return Array
     */
    protected function getPackageProviders($app)
    {
        return [PendulumServiceProvider::class];
    }
    /**
     * Load package alias
     * @param  \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Pendulum' => Pendulum::class,
        ];
    }

    public function assertIsCollection($item)
    {
        $this->assertTrue((bool)((get_class($item) == Collection::class) | (get_class($item) == DBCollection::class)));
    }

    /**
     * Function to compare floats since PHP really sucks at floats
     * @param float $a
     * @param float $b
     * @return bool
     */
    protected function floatsEqual($a,$b)
    {
        $epsilon = 0.00001;
        return ($a - $b) < $epsilon;
    }

    /**
     *Assertion to check if the given item is of class $class
     * @param string $class the class item should be
     * @param object $item the item to check
     */
    protected function assertIsClass($class, $item)
    {
        $this->assertEquals($class, get_class($item));
    }

    /**
     * assertion to see if two variables are the same class
     * @param $a
     * @param $b
     */
    protected function assertSameClass($a,$b)
    {
        $this->assertEquals(get_class($a), get_class($b));
    }
}
