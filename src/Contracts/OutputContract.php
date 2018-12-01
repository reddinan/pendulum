<?php
namespace Bytepath\Pendulum\Contracts;

use Bytepath\Pendulum\Contracts\PendulumContract;
use Illuminate\Support\Collection;

/**
 * Interface to output messages to the user of this code in whatever way you see fit
 * @package Bytepath\Pendulum\Contracts
 */
interface OutputContract
{
    /**
     * Output a success message
     * @param String $message
     */
    public function success($message);

    /**
     * Output a duplicate message
     * @param String $message
     */
    public function duplicate($message);

    /**
     * Output a failure message
     * @param mixed $item the item that failed to import
     * @param String $message
     */
    public function failure($message);

    /**
     * Pendulum has started import
     * @return mixed
     */
    public function importStarted();

    /**
     * Pendulum has finished importing
     * @param $message
     * @return mixed
     */
    public function importComplete();
}