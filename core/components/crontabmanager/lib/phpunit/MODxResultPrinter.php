<?php

use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Runner\PhptTestCase;

class MODxResultPrinter extends \PHPUnit\TextUI\ResultPrinter
{
    /**
     * @var string
     */
    protected $className;

    /**
     * @var string
     */
    protected $previousClassName;

    /**
     * @var string[]|null
     */
    protected $timeColors;

    /**
     * @var string[]
     */
    protected $defaultTimeColors = [
        '1'    => 'fg-red',
        '.400' => 'fg-yellow',
        '0'    => 'fg-green',
    ];

    /**
     * @param TestSuite<TestCase> $suite
     */
    public function startTestSuite(TestSuite $suite)
    {
        if ($this->debug && is_null($this->timeColors)) {
            if (defined('DIABLO_PRINTER_TIME_COLORS') && is_array(DIABLO_PRINTER_TIME_COLORS)) {
                $this->timeColors = DIABLO_PRINTER_TIME_COLORS;
                krsort($this->timeColors, SORT_NUMERIC);
            } else {
                $this->timeColors = $this->defaultTimeColors;
            }
        }

        parent::startTestSuite($suite);
    }

    public function startTest(Test $test)
    {
        $this->className = get_class($test);
        if (!$this->debug) {
            parent::startTest($test);
        }
    }

    public function endTest(\PHPUnit\Framework\Test $test, $time)
    {
        if (!$this->debug) {
            parent::endTest($test, $time);
        } else {
            foreach ($this->timeColors as $threshold => $color) {
                if ($time >= $threshold) {
                    $timeColor = $color;
                    break;
                }
            }

            if (!$this->lastTestFailed) {
                $this->writeProgress('.');
            }

            $this->write(' ');
            $this->writeWithColor($timeColor, '[' . number_format($time, 3) . 's]', false);
            $this->write(' ');

            $msg = \PHPUnit\Util\Test::describeAsString($test);

            $this->writeWithColor('fg-cyan', $msg, true);

            // Necessary part from \PHPUnit\TextUI\ResultPrinter::endTest
            if ($test instanceof TestCase) {
                $this->numAssertions += $test->getNumAssertions();
            } elseif ($test instanceof PhptTestCase) {
                $this->numAssertions++;
            }

            $this->lastTestFailed = false;
            if ($test instanceof TestCase) {
                if (!$test->hasExpectationOnOutput()) {
                    $this->write($test->getActualOutput());
                }
            }
        }
    }

    protected function writeProgress( $progress)
    {
        if ($this->debug) {
            $this->write($progress);
            ++$this->numTestsRun;
        } else {
            if ($this->previousClassName !== $this->className) {
                $this->write("\n");
                $this->writeWithColor('fg-cyan', str_pad($this->className, 50, ' ', STR_PAD_LEFT) . ' ', false);
            }
            $this->previousClassName = $this->className;

            if ($progress == '.') {
                $this->writeWithColor('fg-green', $progress, false);
            } else {
                $this->write($progress);
            }
        }
    }

    protected function printDefectTrace(\PHPUnit\Framework\TestFailure $defect): void
    {
        $this->write($this->formatExceptionMsg($defect->getExceptionAsString()));
        $trace = \PHPUnit\Util\Filter::getFilteredStacktrace(
            $defect->thrownException()
        );
        if (!empty($trace)) {
            $this->write("\n" . $trace);
        }
        $exception = $defect->thrownException()->getPrevious();
        while ($exception) {
            $this->write(
                "\nCaused by\n" .
                \PHPUnit\Framework\TestFailure::exceptionToString($exception) . "\n" .
                \PHPUnit\Util\Filter::getFilteredStacktrace($exception)
            );
            $exception = $exception->getPrevious();
        }
    }

    protected function formatExceptionMsg(string $exceptionMessage): string
    {
        $exceptionMessage = str_replace("+++ Actual\n", '', $exceptionMessage);
        $exceptionMessage = str_replace("--- Expected\n", '', $exceptionMessage);
        $exceptionMessage = str_replace('@@ @@', '', $exceptionMessage);

        if ($this->colors) {
            $exceptionMessage = preg_replace('/^(Exception.*)$/m', "\033[01;31m$1\033[0m", $exceptionMessage) ?? '';
            $exceptionMessage = preg_replace('/^(Failed.*)$/m', "\033[01;31m$1\033[0m", $exceptionMessage) ?? '';
            $exceptionMessage = preg_replace("/^(\-+.*)$/m", "\033[01;32m$1\033[0m", $exceptionMessage) ?? '';
            $exceptionMessage = preg_replace("/^(\++.*)$/m", "\033[01;31m$1\033[0m", $exceptionMessage) ?? '';
        }

        return $exceptionMessage;
    }
}
