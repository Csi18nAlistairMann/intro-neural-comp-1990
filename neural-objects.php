<?php
declare(strict_types=1);

require_once('defines.php');

//
// Obtain a string read out of a network run in a more easily understood
// format. C1S8 example below
class MemoryStatesHandler
{
    private $runs;

    public function __construct($value) {
        $this->runs = $value;
    }

    //
    // C1S8 network
    //
    // Run no: 0
    // 000 101 111 111
    // 000 010 010 010
    // 000 010 010 010
    //
    // Run no: 1
    // 111 111 101 101
    // 111 111 111 111
    // 111 101 101 101
    //
    // Run no: 2
    // 111 111 101 111 101 101
    // 010 111 111 111 111 111
    // 111 000 111 101 101 101
    public function getReadOut_c1s8() {
        $rv = '';
        $runIdx = 0;
        foreach ($this->runs as $run) {
            $rv .= "\n";
            $rv .= "Run no: $runIdx\n";

            $output0 = "";
            $output1 = "";
            $output2 = "";

            foreach ($run as $stms) {
                $output0 .= "${stms[0]}${stms[1]}${stms[2]} ";
                $output1 .= "${stms[3]}${stms[4]}${stms[5]} ";
                $output2 .= "${stms[6]}${stms[7]}${stms[8]} ";
            }
            $output0 .= "\n";
            $output1 .= "\n";
            $output2 .= "\n";
            $rv .= $output0 . $output1 . $output2;

            $runIdx++;
        }

        $rv = trim($rv) . "\n";
        return $rv;
    }
}

class ToyAdaptiveNode
{
    protected $inputArray = array();
    protected $inputSz = TAN_ERROR; // 3 means inputs 0, 1, 2
    protected $teachUse = USAGE_TEACH;
    protected $output = UNDEFINED_TXT;
    protected $cachedOutput = UNDEFINED_TXT;
    protected $clampedValue = UNDEFINED;

    //
    // When a node is clamped, inputs can be ignored during the input phase,
    // and the output directly retrieved from the clamped value during the
    // output phase,
    public function setClampedValue($value) {
        switch ($value) {
        case 0:
        case 1:
        case null:
        case UNDEFINED:
            $this->clampedValue = $value;
            break;
        default:
            return TAN_ERROR;
        }
    }

    public function getClampedValue() {
        return $this->clampedValue;
    }

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
            $v = $this->getClampedValue();
            if ($v === 0 || $v === 1)
                return $v;
            if ($this->output !== UNDEFINED_TXT)
                return $this->output;
            return $this->getUndefined();
        }
        return TAN_ERROR;
    }

    public function initialise($value) {
        $this->output = $value;
        $this->cachedOutput = $value;
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
        $v = $this->getClampedValue();
        if (!($v === 0 || $v === 1)) {
            $v = 0;
            for ($src = 0; $src < $this->inputSz; $src++) {
                $obj = $this->inputArray[$src];
                $v += $obj->getF(__FUNCTION__);
            }
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
        $v = $this->getClampedValue();
        if (!($v === 0 || $v === 1)) {
            $v = 0;
            for ($src = 0; $src < $this->inputSz; $src++) {
                $obj = $this->inputArray[$src];
                $v += $obj->getF(__FUNCTION__);
            }
        }
        $this->cachedOutput = $v;
    }

    public function runSlowOutput() {
        $v = $this->getClampedValue();
        if (!($v === 0 || $v === 1)) {
            if ($this->cachedOutput !== UNDEFINED_TXT)
                $this->output = $this->cachedOutput;
            else
                $this->output = $this->getF(__FUNCTION__);
        }
        $this->output = $v;
    }
}
