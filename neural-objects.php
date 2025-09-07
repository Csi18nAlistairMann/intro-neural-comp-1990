<?php
declare(strict_types=1);

require_once('defines.php');

class ToyAdaptiveNode
{
    protected $inputArray = array();
    protected $inputSz = TAN_ERROR; // 3 means inputs 0, 1, 2
    protected $teachUse = USAGE_TEACH;

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
            // not written
            return TAN_ERROR;
        }
        return TAN_ERROR;
    }
}
