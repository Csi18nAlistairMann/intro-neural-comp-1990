<?php
declare(strict_types=1);

require_once('defines.php');

class ToyAdaptiveNode
{
    protected $inputArray = array();
    protected $inputSz = TAN_ERROR; // 3 means inputs 0, 1, 2
    protected $teachUse = USAGE_TEACH;
    protected $forcedOutput = UNDEFINED_TXT;
    protected $nextForcedOutput = UNDEFINED_TXT;

    public function setNumberInputs($n) {
        if (!(is_int($n)))
            return TAN_ERROR;
        $this->inputSz = $n;
    }

    public function setInputN($input, $value) {
        if ($input >= $this->inputSz)
            return TAN_ERROR;
        if ($value === 0 || $value === 1)
            $this->inputArray[$input] = $value;
        else
            return TAN_ERROR;
    }

    public function setInputNAsTan($input, &$value) {
        if ($input >= $this->inputSz)
            return TAN_ERROR;
        if (is_object($value))
            $this->inputArray[$input] = $value;
        else
            return TAN_ERROR;
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
        else {
            return TAN_ERROR;
        }
    }

    //
    // Book on page 3 requires that undefined be randomly zero or one. This will
    // make testing tricky!
    protected function getUndefined() {
        return rand(0, 1);
    }

    //
    // getF() is where one might expect neural processing to have happened. As
    // that's not yet described, this routine is superseded in the testing
    // class
    public function getF($example) {
        if ($this->getUsage() === USAGE_USE) {
            if ($this->forcedOutput !== UNDEFINED_TXT)
                return $this->forcedOutput;
            return $this->getUndefined();
        }
        return TAN_ERROR;
    }

    //
    // For a special case of objects to add as common inputs to the network.
    // This eases using address by reference later
    public function setTanAsInput($val) {
        if (!(is_int($val))) {
            return TAN_ERROR;
        }
        $this->setNumberInputs(1);
        $this->setUsage(USAGE_USE);
        $this->forcedOutput = $val;
    }

    public function run() {
        $v = 0;
        for ($src = 0; $src < $this->inputSz; $src++) {
            $obj = $this->inputArray[$src];
            $v += $obj->getF(__FUNCTION__);
        }
        $this->forcedOutput = $v;
    }

    public function run_SettleInput() {
        $v = 0;
        for ($src = 0; $src < $this->inputSz; $src++) {
            $obj = $this->inputArray[$src];
            $v += $obj->getF(__FUNCTION__);
        }
        $this->nextForcedOutput = $v;
    }

    public function run_SettleOutput() {
        if ($this->nextForcedOutput !== UNDEFINED_TXT)
            $this->forcedOutput = $this->nextForcedOutput;
        else
            $this->forcedOutput = $this->getF(__FUNCTION__);
    }
}
