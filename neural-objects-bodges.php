<?php
declare(strict_types=1);

/*
  This file contains derived classes for the TAN including code to fake the
  neural processing elements
*/

require_once('defines.php');
require_once('neural-objects.php');

//
// 18b tests demonstrate that the output can be different from the taught
// pattern: note both T and H result in TANs 11 & 22 outputting a 1 both
// ways. This is bodged by a straightforward index into the taught set.
//
// I can't tell just yet how this will work out. Both training sets output
// a 1? All inputs on or off cause a 1? The firing rule uses the hamming
// distance and outputs whatevers at that index in another lookup?
class ToyAdaptiveNode_test_18b extends ToyAdaptiveNode
{
    private $taughtSet0 = array();

    public function setTaughtSets($array0, $array1) {
        $this->taughtSet0 = $array0;
    }

    public function run_fast() {
        //
        // Use the inputs as an index into the taught set to establish
        // what the output should be
        //
        // 1. Get the inputs
        $w = $this->inputArray[0]->getF(__FUNCTION__);
        $n = $this->inputArray[1]->getF(__FUNCTION__);
        $e = $this->inputArray[2]->getF(__FUNCTION__);
        $s = $this->inputArray[3]->getF(__FUNCTION__);
        // 2. Convert the inputs into 0 to 15
        $zero2fifteen = ($w * 8) + ($n * 4) + ($e * 2) + $s;
        // 3. Use that to look up the outputs
        $rv = $this->taughtSet0[$zero2fifteen];
        // 4. Handle undefined
        if ($rv === 2)
            $rv = $this->getUndefined();
        $this->output = $rv;
    }

    //
    // Note this time we return the output specified by the taught set, not
    // the output with the lowest hamming distance, which could be different
    public function getF($example) {
        if ($this->getUsage() !== USAGE_USE)
            return TAN_ERROR;

        if ($this->output !== UNDEFINED_TXT)
            return $this->output;

        if ($this->inputArray[0]->getF($example) === $this->taughtSet0[0] &&
            $this->inputArray[1]->getF($example) === $this->taughtSet0[1] &&
            $this->inputArray[2]->getF($example) === $this->taughtSet0[2] &&
            $this->inputArray[3]->getF($example) === $this->taughtSet0[3]) {
            return 0;
        }
        if ($this->inputArray[0]->getF($example) === $this->taughtSet1[0] &&
            $this->inputArray[1]->getF($example) === $this->taughtSet1[1] &&
            $this->inputArray[2]->getF($example) === $this->taughtSet1[2] &&
            $this->inputArray[3]->getF($example) === $this->taughtSet1[3]) {
            return 1;
        }
        return $this->getUndefined();
    }
}

//
// 18a tests against the p13 truth table.
//
// Note unlike 18b, TAN23 uses the same output as for the hamming distance
// to the taught set.
class ToyAdaptiveNode_test_18a extends ToyAdaptiveNode
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
                          $this->inputArray[2]->getF(__FUNCTION__),
                          $this->inputArray[3]->getF(__FUNCTION__));

        $hammingSet0 = 4;
        for ($idx = 0; $idx < sizeof($this->taughtSet0); $idx++)
            $hammingSet0 = min($hammingSet0,
                               $this->getHammingDistance($this->taughtSet0[$idx],
                                                         $inputArr));
        $hammingSet1 = 4;
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

        if ($this->output !== UNDEFINED_TXT)
            return $this->output;

        //
        // TAN23 bodged responses to specific tests in the caller
        if (($this->inputArray[0]->getF($example) === 1 &&
             $this->inputArray[1]->getF($example) === 1 &&
             $this->inputArray[2]->getF($example) === 0 &&
             $this->inputArray[3]->getF($example) === 0) ||
            ($this->inputArray[0]->getF($example) === 0 &&
             $this->inputArray[1]->getF($example) === 1 &&
             $this->inputArray[2]->getF($example) === 0 &&
             $this->inputArray[3]->getF($example) === 0)) {
            return 0;
        }
        if (($this->inputArray[0]->getF($example) === 1 &&
             $this->inputArray[1]->getF($example) === 1 &&
             $this->inputArray[2]->getF($example) === 1 &&
             $this->inputArray[3]->getF($example) === 1) ||
            ($this->inputArray[0]->getF($example) === 0 &&
             $this->inputArray[1]->getF($example) === 0 &&
             $this->inputArray[2]->getF($example) === 1 &&
             $this->inputArray[3]->getF($example) === 1)) {
            return 1;
        }
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
