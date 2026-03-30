<?php

namespace Tests\Zadatak4;

use App\Utiliter\Services\StepCounter;
use PHPUnit\Framework\TestCase;

// example of one service test
class StepCounterTest extends TestCase
{
    private StepCounter $stepCounter;

    protected function setUp(): void
    {
        $_SESSION = [];
        $this->stepCounter = new StepCounter();
    }

    public function test_initial_steps_are_zero(): void
    {
        $this->assertEquals(0, $this->stepCounter->getSteps());
    }

    public function test_increment_increases_steps(): void
    {
        for ($i = 0; $i < 7; $i++) {
            $this->stepCounter->increment();
        }
        
        $this->assertEquals(7, $this->stepCounter->getSteps());
    }

    public function test_counter_does_not_exceed_max_steps(): void
    {
        for ($i = 0; $i < 15; $i++) {
            $this->stepCounter->increment();
        }
        $this->assertEquals(10, $this->stepCounter->getSteps());
    }

    public function test_is_finished_after_ten_steps(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->stepCounter->increment();
        }
        $this->assertTrue($this->stepCounter->isFinished());
    }

    public function test_reset_clears_steps(): void
    {
        for ($i = 0; $i < 7; $i++) {
            $this->stepCounter->increment();
        }

        $this->stepCounter->reset();

        $this->assertNotEquals(7, $this->stepCounter->getSteps()); // is cleared
        $this->assertEquals(0, $this->stepCounter->getSteps());
    }

    public function test_is_not_finished_initially(): void
    {
        $this->assertFalse($this->stepCounter->isFinished());
    }
}