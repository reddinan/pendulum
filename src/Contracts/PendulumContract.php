<?php
namespace Bytepath\Pendulum\Contracts;

interface PendulumContract
{
    /**
     * Called if the importable item imported successfully
     * @return string An information message that will be passed on to the user
     */
    public function pendulumSuccess();

    /**
     * Called if the importable item failed to import
     * @return string An information message that will be passed on to the user
     */
    public function pendulumFailure();

    /**
     * Called if the importable item was already imported
     * @return string An information message that will be passed on to the user
     */
    public function pendulumDuplicate();
}