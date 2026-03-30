<?php

namespace App\Utiliter\Services;

class StepCounter
{
    private const MAX_STEPS = 10;

    public function __construct()
    {
        if (!isset($_SESSION['steps'])) {
            $this->reset(); // set in this case
        }
    }

    /**
     * Increment number of steps (max=10).
     * @return void
     */
    public function increment(): void
    {
        if ($this->getSteps() < self::MAX_STEPS) {
            $_SESSION['steps']++;
        }
    }

    /**
     * Reset (or set) state to initial values.
     * @return void
     */
    public function reset(): void
    {
        $_SESSION['steps'] = 0;
        $_SESSION['start_time'] = time();
    }

    /**
     * Get number of made steps.
     * @return int
     */
    public function getSteps(): int
    {
        return $_SESSION['steps'];
    }

    /**
     * Get final status.
     * @return bool
     */
    public function isFinished(): bool
    {
        return $this->getSteps() >= self::MAX_STEPS;
    }

    /**
     * Show user friendly elapsed time since start.
     * @return string
     */
    public function getElapsed(): string
    {
        $seconds = time() - $_SESSION['start_time'];
        return $seconds >= 60
            ? round($seconds / 60) . ' min'
            : $seconds . ' sec';
    }

    /**
     * Format user text based on current step state.
     * Resets the counter when finished.
     */
    public function formatUserText() : string
    {
        $steps = $this->getSteps();
        
        if($this->isFinished()) {
            $elapsed = $this->getElapsed();
            $this->reset();
            return <<<HTML
                <p>Čestitamo, prošli ste {$steps} koraka u {$elapsed}!</p>
            HTML;
        }

        if($steps > 0) {
            return <<<HTML
                <p>Prošli ste {$steps} koraka.</p>
                <a href="?action=step">Sljedeći korak</a>
            HTML;
        }

        return <<<HTML
            <p>Kreni hodati</p>
            <a href="?action=step">Hodaj</a>
        HTML;
    }
}