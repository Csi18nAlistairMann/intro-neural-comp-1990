<?php
declare(strict_types=1);

/*
  Chapter 1 Section 2 focuses on the simplest kind of node, calling it the
  Toy Adaptive Node (TAN).
  neural-objects.php contains the structure outlined.
  neural-objects-bodges.php extends that structure to fake responses described

  There are two sets of examples given, this file sets up a TAN and tests
  the faked responses.
 */

require_once('defines.php');
require_once('neural-objects.php');
require_once('neural-objects-bodges.php');

function test_example121() {
    //
    // p3: a node with three inputs returns 1 if one and only one input has
    // a one
    $tan = new ToyAdaptiveNode_test_12;
    $tan->setNumberInputs(3);
    $tan->setUsage(USAGE_USE);
    $tan->setInputN(0, 0);
    $tan->setInputN(1, 0);
    $tan->setInputN(2, 0);
    if ($tan->getF(__FUNCTION__) != 0) {
        echo "Error for 000\n";
    }

    $tan = new ToyAdaptiveNode_test_12;
    $tan->setNumberInputs(3);
    $tan->setUsage(USAGE_USE);
    $tan->setInputN(0, 0);
    $tan->setInputN(1, 1);
    $tan->setInputN(2, 0);
    if ($tan->getF(__FUNCTION__) != 1) {
        echo "Error for 010\n";
    }

    $tan = new ToyAdaptiveNode_test_12;
    $tan->setNumberInputs(3);
    $tan->setUsage(USAGE_USE);
    $tan->setInputN(0, 0);
    $tan->setInputN(1, 1);
    $tan->setInputN(2, 1);
    if ($tan->getF(__FUNCTION__) != 0) {
        echo "Error for 000\n";
    }

    echo "Completed " . __FUNCTION__ . "\n";
}

function test_example122() {
    //
    // p4: a node can respond with 0/1 for any input not all zero or
    // all one
    $tan = new ToyAdaptiveNode_test_12;
    $tan->setNumberInputs(3);
    $tan->setUsage(USAGE_USE);
    $tan->setInputN(0, 0);
    $tan->setInputN(1, 0);
    $tan->setInputN(2, 0);
    if ($tan->getF(__FUNCTION__) != 0) {
        echo "Error for 000\n";
    }

    $tan = new ToyAdaptiveNode_test_12;
    $tan->setNumberInputs(3);
    $tan->setUsage(USAGE_USE);
    $tan->setInputN(0, 1);
    $tan->setInputN(1, 1);
    $tan->setInputN(2, 1);
    if ($tan->getF(__FUNCTION__) != 1) {
        echo "Error for 010\n";
    }

    $response0 = 0;
    $response1 = 0;
    for($a = 0; $a < 200; $a++) {
        $tan = new ToyAdaptiveNode_test_12;
        $tan->setNumberInputs(3);
        $tan->setUsage(USAGE_USE);
        $tan->setInputN(0, 0);
        $tan->setInputN(1, 1);
        $tan->setInputN(2, 1);
        $response = $tan->getF(__FUNCTION__);
        if ($response === 0)
            $response0++;
        elseif ($response === 1)
            $response1++;
        else
            echo "Error for 000\n";
    }
    if ($response0 === 0 || $response1 === 0) {
        echo "Suspicious that no random responses seen\n";
    }

    echo "Completed " . __FUNCTION__ . "\n";
}

test_example121(__FUNCTION__);
test_example122(__FUNCTION__);

?>
