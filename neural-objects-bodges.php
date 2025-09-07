<?php
declare(strict_types=1);

/*
  This file contains a derived class for the TAN including code to fake the
  neural processing elements
 */

require_once('defines.php');
require_once('neural-objects.php');

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
