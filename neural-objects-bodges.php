<?php
declare(strict_types=1);

/*
  This file contains derived classes for the TAN including code to fake the
  neural processing elements
*/

require_once('defines.php');
require_once('neural-objects.php');

//
// 18a tests against the p13 truth table.

//
// 18b tests demonstrate that the output can be different from the taught
// pattern: note both T and H result in TANs 11, 13 & 22 outputting a 1 both
// ways. I originaly bodged this by a straightforward index into the taught
// set. However it became clear this could be better done having no '0'
// examples in the taught set, only '1' examples. The bodge was removed

//
// 18c tests the STM sequence at the bottom of p15
class ToyAdaptiveNode_test_18 extends ToyAdaptiveNode
{
    private $taughtSet0 = array();
    private $taughtSet1 = array();

    //
    // Bodge to fake the learning described but skipped over
    public function setTaughtSets($array0, $array1) {
        $this->taughtSet0 = $array0;
        $this->taughtSet1 = $array1;
    }

    //
    // Hamming distance is the number of mismatches in two equal length arrays
    private function getHammingDistance($a, $b) {
        if (sizeof($a) !== sizeof($b))
            return TAN_ERROR;

        $dist = 0;
        for ($idx = 0; $idx < sizeof($a); $idx++)
            if ($a[$idx] !== $b[$idx]) $dist++;

        return $dist;
    }

    //
    // Running a node currently means take the inputs and determine which
    // set of traning examples they most look like.
    private function run() {
        $rv = $this->getClampedValue();
        if (!($rv === 0 || $rv === 1)) {
            // If we're not clamped, first get our inputs as an array
            //
            // We do have inputSz, we could generalise this
            $inputArr = array($this->inputArray[0]->getF(__FUNCTION__),
                              $this->inputArray[1]->getF(__FUNCTION__),
                              $this->inputArray[2]->getF(__FUNCTION__),
                              $this->inputArray[3]->getF(__FUNCTION__));
            //
            // Now calculate the lowest hamming distance to anything in the
            // taught sets
            $hammingSet0 = $hammingSet1 = sizeof($this->inputArray) + 1;
            for ($idx = 0; $idx < sizeof($this->taughtSet0); $idx++)
                $hammingSet0 = min($hammingSet0,
                                   $this->getHammingDistance($this->taughtSet0[$idx],
                                                             $inputArr));
            for ($idx = 0; $idx < sizeof($this->taughtSet1); $idx++)
                $hammingSet1 = min($hammingSet1,
                                   $this->getHammingDistance($this->taughtSet1[$idx],
                                                             $inputArr));
            //
            // And use the lowest hamming distance to determine the output,
            // resolving undefined if they're both equal
            if ($hammingSet0 < $hammingSet1) {
                $rv = 0;
            } elseif ($hammingSet0 > $hammingSet1) {
                $rv = 1;
            } else {
                $rv = $this->getUndefined();
            }
        }
        return $rv;
    }

    //
    // Running fast gets inputs to output skipping the cache
    public function run_fast() {
        $this->output = $this->run();
    }

    // Not running fast means moving inputs to outputs in two phases
    //
    // First step, get inputs to the cache ...
    public function runInputs() {
        $this->cachedOutput = $this->run();
    }
    //
    // ... and in the second step, use clamped if present, the cache if
    // loaded, and F otherwise - which allows for undefined to be
    // resolved one way or another
    public function runSlowOutput() {
        $v = $this->getClampedValue();
        if (!($v === 0 || $v === 1)) {
            if ($this->cachedOutput !== UNDEFINED_TXT)
                $this->output = $this->cachedOutput;
            else
                $this->output = $this->getF(__FUNCTION__);
        } else {
            $this->output = $v;
        }
    }

    //
    // Match only against something in the taught set. We use this in the
    // first p13 example
    public function run_matchOnlyTaughtSet() {
        $rv = $this->getClampedValue();
        if ($rv === 0 || $rv === 1) {
            $this->output = $rv;

        } else {
            // If we're not clamped, first get our inputs as an array
            $inputArr = array($this->inputArray[0]->getF(__FUNCTION__),
                              $this->inputArray[1]->getF(__FUNCTION__),
                              $this->inputArray[2]->getF(__FUNCTION__),
                              $this->inputArray[3]->getF(__FUNCTION__));

            // Look to see if we have an exact match in either training sets
            $out0 = $out1 = false;
            foreach($this->taughtSet0 as $set0) {
                if ($inputArr === $set0) {
                    $out0 = true;
                    break;
                }
            }
            foreach($this->taughtSet1 as $set1) {
                if ($inputArr === $set1) {
                    $out1 = true;
                    break;
                }
            }

            // If we do, that's our output. If neither or both, randomly
            // decide
            if ($out0)
                $this->output = 0;
            else if ($out1)
                $this->output = 1;
            else
                $this->output = $this->getUndefined();
        }
    }

    public function getF($example) {
        if ($this->getUsage() !== USAGE_USE)
            return TAN_ERROR;

        $rv = $this->getClampedValue();
        if ($rv === 0 || $rv === 1)
            return $rv;

        if ($this->output !== UNDEFINED_TXT)
            return $this->output;

        return $this->getUndefined();
    }
}

//
// tests 1.5 are about H v T visual recognition
// tests 1.6 reuses, and are about H v T `visual recognition
// with the represented dsecision localised
class ToyAdaptiveNode_test_15 extends ToyAdaptiveNode
{
    private $taughtSet0 = array();
    private $taughtSet1 = array();

    //
    // Bodge to fake the learning described but skipped over
    public function setTaughtSets($array0, $array1) {
        $this->taughtSet0 = $array0;
        $this->taughtSet1 = $array1;
    }

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
        $inputArr = array($this->inputArray[0]->getF(__FUNCTION__),
                          $this->inputArray[1]->getF(__FUNCTION__),
                          $this->inputArray[2]->getF(__FUNCTION__));

        $hammingSet0 = 3;
        for ($idx = 0; $idx < sizeof($this->taughtSet0); $idx++)
            $hammingSet0 = min($hammingSet0,
                               $this->getHammingDistance($this->taughtSet0[$idx],
                                                         $inputArr));
        $hammingSet1 = 3;
        for ($idx = 0; $idx < sizeof($this->taughtSet1); $idx++)
            $hammingSet1 = min($hammingSet1,
                               $this->getHammingDistance($this->taughtSet1[$idx],
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
    protected $inputArrayInt = array();

    private function getF_example1_2_1() {
        if (($this->inputArrayInt[0] +
             $this->inputArrayInt[1] +
             $this->inputArrayInt[2]) === 1) {
            return 1;
        } else {
            return 0;
        }
    }

    private function getF_example1_2_2() {
        if (($this->inputArrayInt[0] +
             $this->inputArrayInt[1] +
             $this->inputArrayInt[2]) === 0) {
            return 0;
        } elseif (($this->inputArrayInt[0] +
                   $this->inputArrayInt[1] +
                   $this->inputArrayInt[2]) === 3) {
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

    //
    // Bodge for first example using INTs not TANs as inputs
    public function setInputNAsInt($input, $value) {
        if ($input >= $this->inputSz)
            return TAN_ERROR;
        if ($value === 0 || $value === 1)
            $this->inputArrayInt[$input] = $value;
        else
            return TAN_ERROR;
    }
}
?>
