<?php
declare(strict_types=1);

require_once('defines.php');

class ToyAdaptiveNode
{
    protected $inputArray = array();
    protected $inputSz = TAN_ERROR; // 3 means inputs 0, 1, 2
    protected $teachUse = USAGE_TEACH;
    protected $output = UNDEFINED_TXT;
    protected $cachedOutput = UNDEFINED_TXT;

    public function setNumberInputs($n) {
        if (!(is_int($n)))
            return TAN_ERROR;
        $this->inputSz = $n;
    }

    public function setInputNAsTan($input, &$value) {
        if ($input >= $this->inputSz)
            return TAN_ERROR;
        if (is_object($value))
            $this->inputArray[$input] = $value;
        else
            return TAN_ERROR;
    }

    public function setFromInputNAsTan($input, $tanArr) {
        $fakeArray = array();

        if ($input >= $this->inputSz)
            return TAN_ERROR;

        foreach ($tanArr as &$value) {
            if (is_object($value))
                $fakeArray[$input++] = $value;
            else
                return TAN_ERROR;
        }
        $this->inputArray = $fakeArray;
    }

    public function setUsage($usage) {
        if ($usage === USAGE_USE || $usage === USAGE_TEACH)
            $this->teachUse = $usage;
        else
            return TAN_ERROR;
    }

    public function getUsage() {
        if ($this->teachUse === USAGE_USE || $this->teachUse === USAGE_TEACH)
            return $this->teachUse;
        else
            return TAN_ERROR;
    }

    //
    // Book on page 3 requires that undefined be randomly zero or one. This will
    // make testing tricky!
    protected function getUndefined() {
        return rand(0, 1);
    }

    //
    // getF() is where one might expect neural processing to have happened.
    public function getF($example) {
        if ($this->getUsage() === USAGE_USE) {
            if ($this->output !== UNDEFINED_TXT)
                return $this->output;
            return $this->getUndefined();
        }
        return TAN_ERROR;
    }

    //
    // We use this special case of a TAN to hold an Overall Input. This eases
    // accessibility later by using same address by reference technique as for
    // normal nodes
    public function setTanAsInput($val) {
        if (!(is_int($val)))
            return TAN_ERROR;
        $this->setNumberInputs(1);
        $this->setUsage(USAGE_USE);
        $this->output = $val;
    }

    //
    // run_fast() is only for networks with no feedback loops
    public function run_fast() {
        $v = 0;
        for ($src = 0; $src < $this->inputSz; $src++) {
            $obj = $this->inputArray[$src];
            $v += $obj->getF(__FUNCTION__);
        }
        $this->output = $v;
    }

    //
    // runSlow*() is for when feedback loops are present and propagates
    // data forward in two goes
    // 1. run_fast() risks undefined outputs changing between accesses in the
    // same layer of the network
    // 2. Ditto risks processing happening in one node before another in the
    // same layer of the network
    // Solution is to provide for two passes, with all nodes getting one pass
    // before getting the other
    // runSlowInput() pass conducts processing and caches result
    // runSlowOutput() pass either reuses that cached result, uses the existing
    // output if not undefined, or settles the undefined value to be used
    public function runSlowInput() {
        $v = 0;
        for ($src = 0; $src < $this->inputSz; $src++) {
            $obj = $this->inputArray[$src];
            $v += $obj->getF(__FUNCTION__);
        }
        $this->cachedOutput = $v;
    }

    public function runSlowOutput() {
        if ($this->cachedOutput !== UNDEFINED_TXT)
            $this->output = $this->cachedOutput;
        else
            $this->output = $this->getF(__FUNCTION__);
    }
}
