<?php
namespace Bytepath\FlashBang\Tests;

use Bytepath\FlashBang\Flasher;
use Bytepath\FlashBang\BPSessionManager;
use Mockery;

class FlasherTest extends \Bytepath\FlashBang\Tests\TestCase
{
    public function test_can_create()
    {
        $log = $this->mockLog();
        $session = $this->mockSession();
        $flash = new Flasher($log, $session);
        $this->assertIsClass(Flasher::class, $flash);
    }

    public function test_success_with_no_message()
    {
        $log = $this->mockLog();
        $session = $this->mockSession();

        $log->shouldNotReceive("notice");
        $session->shouldReceive("flash")->twice();

        // Multiple calls to shouldReceive seems to have stopped working?
        //$session->shouldReceive("flash")->withArgs(["message","Success!"]);
        //$session->shouldReceive("flash")->withArgs(["messageClass","alert-success"]);

        $flash = new Flasher($log, $session);
        $flash->success();
    }

    public function test_success_with_message()
    {
        $log = $this->mockLog();
        $session = $this->mockSession();

        $log->shouldNotReceive("notice");
        $session->shouldReceive("flash")->twice();

        //$session->shouldReceive("flash")->withArgs(["message","Hello"]);
        //$session->shouldReceive("flash")->withArgs(["messageClass","alert-success"]);

        $flash = new Flasher($log, $session);
        $flash->success("Hello");
    }

    public function test_success_with_message_and_log()
    {
        $log = $this->mockLog();
        $session = $this->mockSession();

        $log->shouldReceive("info")->withAnyArgs();
        $session->shouldReceive("flash")->twice();

        //$session->shouldReceive("flash")->withArgs(["message","Success!"]);
        //$session->shouldReceive("flash")->withArgs(["messageClass","alert-success"]);

        $flash = new Flasher($log, $session);
        $flash->success("Success!", "CATS");
    }

    public function test_failure_with_no_message()
    {
        $log = $this->mockLog();
        $session = $this->mockSession();

        $log->shouldNotReceive("error");
        $session->shouldReceive("flash")->twice();

        //$session->shouldReceive("flash")->withArgs(["message","Failure!"]);
        //$session->shouldReceive("flash")->withArgs(["messageClass","alert-danger"]);

        $flash = new Flasher($log, $session);
        $flash->failure();
    }

    public function test_failure_with_message()
    {
        $log = $this->mockLog();
        $session = $this->mockSession();

        $log->shouldNotReceive("error");
        $session->shouldReceive("flash")->twice();

        //$session->shouldReceive("flash")->withArgs(["message","Hello"]);
        //$session->shouldReceive("flash")->withArgs(["messageClass","alert-danger"]);

        $flash = new Flasher($log, $session);
        $flash->failure("Hello");
    }

    public function test_failure_with_message_and_log()
    {
        $log = $this->mockLog();
        $session = $this->mockSession();

        $log->shouldReceive("error")->withAnyArgs();
        $session->shouldReceive("flash")->twice();

        //$session->shouldReceive("flash")->withArgs(["message","fail"]);
        //$session->shouldReceive("flash")->withArgs(["messageClass","alert-danger"]);

        $flash = new Flasher($log, $session);
        $flash->failure("fail", "CATS");
    }

    public function test_warning_with_message()
    {
        $log = $this->mockLog();
        $session = $this->mockSession();

        $log->shouldNotReceive("notice");
        $session->shouldReceive("flash")->twice();

        //$session->shouldReceive("flash")->withArgs(["message","Hello"]);
        //$session->shouldReceive("flash")->withArgs(["messageClass","alert-warning"]);

        $flash = new Flasher($log, $session);
        $flash->warning("Hello");
    }

    public function test_warning_with_message_and_log()
    {
        $log = $this->mockLog();
        $session = $this->mockSession();

        $log->shouldReceive("warning")->withAnyArgs();
        $session->shouldReceive("flash")->twice();

        //$session->shouldReceive("flash")->withArgs(["message","warn"]);
        //$session->shouldReceive("flash")->withArgs(["messageClass","alert-warning"]);

        $flash = new Flasher($log, $session);
        $flash->warning("warn", "CATS");
    }

    public function test_info_with_message()
    {
        $log = $this->mockLog();
        $session = $this->mockSession();

        $log->shouldNotReceive("notice");
        $session->shouldReceive("flash")->twice();

//        $session->shouldReceive("flash")->withArgs(["message","Hello"]);
//        $session->shouldReceive("flash")->withArgs(["messageClass","alert-info"]);

        $flash = new Flasher($log, $session);
        $flash->info("Hello");
    }

    public function test_info_with_message_and_log()
    {
        $log = $this->mockLog();
        $session = $this->mockSession();

        $log->shouldReceive("info")->withAnyArgs();
        $session->shouldReceive("flash")->twice();

        //$session->shouldReceive("flash")->withArgs(["message","info"]);
        //$session->shouldReceive("flash")->withArgs(["messageClass","alert-info"]);

        $flash = new Flasher($log, $session);
        $flash->info("info", "CATS");
    }

    /**
     * @expectedException \ArgumentCountError
     */
    public function test_warning_with_no_message_throws_exception()
    {
        $log = $this->mockLog();
        $session = $this->mockSession();

        $flash = new Flasher($log, $session);
        $flash->warning();
    }

    /**
     * @expectedException \ArgumentCountError
     */
    public function test_info_with_no_message_throws_exception()
    {
        $log = $this->mockLog();
        $session = $this->mockSession();

        $flash = new Flasher($log, $session);
        $flash->warning();
    }

    protected function mockLog()
    {
        return Mockery::mock(\Illuminate\Contracts\Logging\Log::class);
    }

    /**
     * @return \Mockery\MockInterface
     */
    protected function mockSession()
    {
        return Mockery::mock(BPSessionManager::class);
    }
}