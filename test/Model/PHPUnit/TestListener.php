<?php

namespace Mismatch\Model\PHPUnit;

use Mismatch\Model\Metadata;
use PHPUnit_Framework_AssertionFailedError;
use PHPUnit_Framework_TestListener;
use PHPUnit_Framework_TestSuite;
use PHPUnit_Framework_Test;
use Exception;

class TestListener implements \PHPUnit_Framework_TestListener
{
    public function addError(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        return;
    }

    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        return;
    }

    public function addIncompleteTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        return;
    }

    public function addRiskyTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        return;
    }

    public function addSkippedTest(PHPUnit_Framework_Test $test, Exception $e, $time)
    {
        return;
    }

    public function startTest(PHPUnit_Framework_Test $test)
    {
        Metadata::reset();
    }

    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        return;
    }

    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        return;
    }

    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        return;
    }
}
