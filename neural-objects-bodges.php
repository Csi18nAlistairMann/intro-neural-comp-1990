<?php
declare(strict_types=1);

/*
  This file contains derived classes for the TAN including code to fake the
  neural processing elements
 */

require_once('defines.php');
require_once('neural-objects.php');

//
// tests 1.4 is about the firing rule
class ToyAdaptiveNode_test_14 extends ToyAdaptiveNode
{

    private function getHammingDistance($a, $b) {
        if (sizeof($a) !== sizeof($b))
            return TAN_ERROR;

        $dist = 0;
        for ($idx = 0; $idx < sizeof($a); $idx++)
            if ($a[$idx] !== $b[$idx]) $dist++;

        return $dist;
    }

    public function run_fast() {
        // What is the hamming distance from either of the taught sets?
        $taughtSet0 = array([0, 0, 0], [0, 0, 1]);
        $taughtSet1 = array([1, 1, 1], [1, 0, 1]);

        $inputArr = array($this->inputArray[0]->getF(__FUNCTION__),
                          $this->inputArray[1]->getF(__FUNCTION__),
                          $this->inputArray[2]->getF(__FUNCTION__));

        $hammingSet0 = 3;
        for ($idx = 0; $idx < sizeof($taughtSet0); $idx++)
            $hammingSet0 = min($hammingSet0,
                               $this->getHammingDistance($taughtSet0[$idx],
                                                         $inputArr));
        $hammingSet1 = 3;
        for ($idx = 0; $idx < sizeof($taughtSet1); $idx++)
            $hammingSet1 = min($hammingSet1,
                               $this->getHammingDistance($taughtSet1[$idx],
                                                         $inputArr));

        if ($hammingSet0 < $hammingSet1) {
            $this->output = 0;
        } elseif ($hammingSet0 > $hammingSet1) {
            $this->output = 1;
        } else {
            $this->output = $this->getUndefined();
        }
    }

    public function getF($example) {
        if ($this->getUsage() !== USAGE_USE)
            return TAN_ERROR;
        // If fired
        if ($this->output !== UNDEFINED_TXT)
            return $this->output;
        // if not fired
        if (($this->inputArray[0]->getF($example) === 0 &&
            $this->inputArray[1]->getF($example) === 0 &&
            $this->inputArray[2]->getF($example) === 0) ||
            ($this->inputArray[0]->getF($example) === 0 &&
            $this->inputArray[1]->getF($example) === 0 &&
             $this->inputArray[2]->getF($example) === 1)) {
            return 0;
        }
        if (($this->inputArray[0]->getF($example) === 1 &&
            $this->inputArray[1]->getF($example) === 1 &&
            $this->inputArray[2]->getF($example) === 1) ||
            ($this->inputArray[0]->getF($example) === 1 &&
            $this->inputArray[1]->getF($example) === 0 &&
             $this->inputArray[2]->getF($example) === 1)) {
            return 1;
        }
        return $this->getUndefined();
    }
}

//
// tests 1.3 are about how nodes connect in a network
class ToyAdaptiveNode_test_13 extends ToyAdaptiveNode
{
    public $offset = 0;

    public function run_fast() {
        if ($this->offset === 0)
            return TAN_ERROR;
        $rv = $this->offset;
        for ($src = 0; $src < $this->inputSz; $src++) {
            $obj = $this->inputArray[$src];
            $v = $obj->getF(__FUNCTION__);
            $rv += $v;
        }
        $this->output = $rv;
    }

    public function runSlowInput() {
        if ($this->offset === 0)
            return TAN_ERROR;
        $rv = $this->offset;
        for ($src = 0; $src < $this->inputSz; $src++) {
            $obj = $this->inputArray[$src];
            $v = $obj->getF(__FUNCTION__);
            $rv += $v;
        }
        $this->cachedOutput = $rv;
    }

    public function runSlowOutput() {
        if ($this->cachedOutput !== UNDEFINED_TXT)
            $this->output = $this->cachedOutput;
        else
            $this->output = $this->offset + $this->getF(__FUNCTION__);
    }
}

//
// tests 1.2 are about a node relating inputs to outputs
class ToyAdaptiveNode_test_12 extends ToyAdaptiveNode
{
    private function getF_example1_2_1() {
        if (($this->inputArray[0] +
             $this->inputArray[1] +
             $this->inputArray[2]) === 1) {
            return 1;
        } else {
            return 0;
        }
    }

    private function getF_example1_2_2() {
        if (($this->inputArray[0] +
             $this->inputArray[1] +
             $this->inputArray[2]) === 0) {
            return 0;
        } elseif (($this->inputArray[0] +
                   $this->inputArray[1] +
                   $this->inputArray[2]) === 3) {
            return 1;
        } else {
            return $this->getUndefined();
        }
    }

    public function getF($example) {
        if ($this->getUsage() !== USAGE_USE) {
            return TAN_ERROR;
        } elseif ($example === 'test_example121') {
            return $this->getF_example1_2_1();
        } elseif ($example === 'test_example122') {
            return $this->getF_example1_2_2();
        }
        return TAN_ERROR;
    }
}
?>
