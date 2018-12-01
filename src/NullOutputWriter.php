<?php

namespace Bytepath\Pendulum;

use Bytepath\Pendulum\Contracts\OutputContract;

/**
 * Default Output for Pendulum. Just throws messages away without sending them
 * @package Bytepath\Pendulum
 */
class NullOutputWriter implements OutputContract
{
    public function success($message){}
    public function duplicate($message){}
    public function failure($message){}
    public function importStarted(){}
    public function importComplete(){}
}