<?php
namespace Bytepath\Pendulum\Facades;

use Bytepath\Pendulum\Pendulum as ActualPendulum;
use Illuminate\Support\Facades\Facade;

class Pendulum extends Facade
{
    protected static function getFacadeAccessor() { return ActualPendulum::class; }
}