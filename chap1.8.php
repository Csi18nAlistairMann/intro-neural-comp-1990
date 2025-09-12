<?php
declare(strict_types=1);

/*
  Chapter 1 Section 8 demonstrates a fully autoassociative network
  endeavouring to show that it can take a randomised start point
  and can use its rules to converge on a learned pattern, thus
  demonstrating decision about what the network can see
*/

require_once('defines.php');
require_once('neural-objects.php');
require_once('neural-objects-bodges.php');

//
// Testing a node from fig 1.6 page 12 with no need to handle undefineds
function test18a($tan11, $tan12, $tan13,
                $tan21, $tan22, $tan23,
                $tan31, $tan32, $tan33,
                $input_arr, $expected, $after) {
    $msg = ($after === true) ? "after" : "before";

    $tan22->setTanAsInput($input_arr[0]);
    $tan13->setTanAsInput($input_arr[1]);
    $tan21->setTanAsInput($input_arr[2]);
    $tan33->setTanAsInput($input_arr[3]);

    //
    // Process firing rules
    if ($after)
        $tan23->run_fast();

    //
    // And check result
    if ($tan23->getF(__FUNCTION__) !== $expected)
        echo "Problem with " . $input_arr[0] . $input_arr[1] .
            $input_arr[2] . $input_arr[3] . " " . $msg . "\n";
}

//
// Testing a node from fig 1.6 page 12 with accomodation for undefineds
function test18a200($tan11, $tan12, $tan13,
                    $tan21, $tan22, $tan23,
                    $tan31, $tan32, $tan33,
                    $input_arr, $expected, $after) {
    $msg = ($after === true) ? "after" : "before";

    $tan22->setTanAsInput($input_arr[0]);
    $tan13->setTanAsInput($input_arr[1]);
    $tan21->setTanAsInput($input_arr[2]);
    $tan33->setTanAsInput($input_arr[3]);

    //
    // Run the TAN's firing rule 200x and accumulate results
    $response0 = 0;
    $response1 = 0;
    for ($tests = 0; $tests < 200; $tests++) {
        if ($after) $tan23->run_fast();
        $v = $tan23->getF(__FUNCTION__);
        if ($v === 0) $response0++;
        elseif ($v === 1) $response1++;
        else echo "Unexpected output with " . $input_arr[0] .
                 $input_arr[1] . $input_arr[2] . $input_arr[3] .
                 " " . $msg . "\n";
    }

    //
    // Check results
    if ($expected === UNDEFINED_TXT) {
        if ($response0 === 0 || $response1 === 0)
            echo "Problem with " . $input_arr[0] . $input_arr[1] .
                $input_arr[2] . $input_arr[3] . " " . $msg . " undef\n";
    } elseif ($expected === 0) {
        if ($response0 === 0 || $response1 !== 0)
            echo "Problem with " . $input_arr[0] . $input_arr[1] .
                $input_arr[2] . $input_arr[3] ." " . $msg . " 0\n";
    } elseif ($expected === 1) {
        if ($response0 !== 0 || $response1 === 0)
            echo "Problem with " . $input_arr[0] . $input_arr[1] .
                $input_arr[2] . $input_arr[3] ." " . $msg . " 1\n";
    } else {
        echo "Unexpected expected with " . $input_arr[0] . $input_arr[1] .
            $input_arr[2] . $input_arr[3] . " " . $msg . " 1\n";
    }
}

//
// Test p13 Truth Tables for TAN23
function test_example18a() {
    //
    // Set up TANs to have taught set as provided by T and H
    $tan11 = new ToyAdaptiveNode_test_18a;
    $tan11->setNumberInputs(4);
    $tan11->setUsage(USAGE_USE);
    $tan11->setTaughtSets([[1, 0, 1, 0]], [[1, 1, 0, 1]]);
    $tan12 = new ToyAdaptiveNode_test_18a;
    $tan12->setNumberInputs(4);
    $tan12->setUsage(USAGE_USE);
    $tan12->setTaughtSets([[1, 1, 1, 1]], [[1, 0, 1, 1]]);
    $tan13 = new ToyAdaptiveNode_test_18a;
    $tan13->setNumberInputs(4);
    $tan13->setUsage(USAGE_USE);
    $tan13->setTaughtSets([[1, 0, 1, 0]], [[0, 1, 1, 1]]);
    $tan21 = new ToyAdaptiveNode_test_18a;
    $tan21->setNumberInputs(4);
    $tan21->setUsage(USAGE_USE);
    $tan21->setTaughtSets([[0, 1, 1, 0]], [[1, 1, 1, 1]]);
    $tan22 = new ToyAdaptiveNode_test_18a;
    $tan22->setNumberInputs(4);
    $tan22->setUsage(USAGE_USE);
    $tan22->setTaughtSets([[0, 1, 0, 1]], [[1, 0, 1, 0]]);
    $tan23 = new ToyAdaptiveNode_test_18a;
    $tan23->setNumberInputs(4);
    $tan23->setUsage(USAGE_USE);
    $tan23->setTaughtSets([[1, 1, 0, 0]], [[1, 1, 1, 1]]);
    $tan31 = new ToyAdaptiveNode_test_18a;
    $tan31->setNumberInputs(4);
    $tan31->setUsage(USAGE_USE);
    $tan31->setTaughtSets([[0, 1, 1, 0]], [[1, 0, 1, 1]]);
    $tan32 = new ToyAdaptiveNode_test_18a;
    $tan32->setNumberInputs(4);
    $tan32->setUsage(USAGE_USE);
    $tan32->setTaughtSets([[0, 1, 0, 1]], [[1, 1, 1, 0]]);
    $tan33 = new ToyAdaptiveNode_test_18a;
    $tan33->setNumberInputs(4);
    $tan33->setUsage(USAGE_USE);
    $tan33->setTaughtSets([[1, 0, 0, 1]], [[0, 1, 1, 1]]);

    //
    // No overall inputs
    //
    // Set up connections
    $tan11->setFromInputNAsTan(0, [$tan13, $tan31, $tan12, $tan21]);
    $tan12->setFromInputNAsTan(0, [$tan11, $tan32, $tan13, $tan22]);
    $tan13->setFromInputNAsTan(0, [$tan12, $tan33, $tan11, $tan23]);

    $tan21->setFromInputNAsTan(0, [$tan23, $tan11, $tan22, $tan31]);
    $tan22->setFromInputNAsTan(0, [$tan21, $tan12, $tan23, $tan32]);
    $tan23->setFromInputNAsTan(0, [$tan22, $tan13, $tan21, $tan33]);

    $tan31->setFromInputNAsTan(0, [$tan33, $tan21, $tan32, $tan11]);
    $tan32->setFromInputNAsTan(0, [$tan31, $tan22, $tan33, $tan12]);
    $tan33->setFromInputNAsTan(0, [$tan32, $tan23, $tan31, $tan13]);

    // Before firing tests
    //
    // Test T
    test18a($tan11, $tan12, $tan13,
            $tan21, $tan22, $tan23,
            $tan31, $tan32, $tan33,
            [1, 1, 0, 0], 0, false);
    // Test H
    test18a($tan11, $tan12, $tan13,
            $tan21, $tan22, $tan23,
            $tan31, $tan32, $tan33,
            [1, 1, 1, 1], 1, false);
    // Test undef, firing won't change
    test18a200($tan11, $tan12, $tan13,
               $tan21, $tan22, $tan23,
               $tan31, $tan32, $tan33,
               [1, 1, 1, 0], UNDEFINED_TXT, false);
    // Test undef, firing will change
    test18a200($tan11, $tan12, $tan13,
               $tan21, $tan22, $tan23,
               $tan31, $tan32, $tan33,
               [0, 1, 1, 1], UNDEFINED_TXT, false);

    // After firing tests
    //
    // Test undef, firing makes 0
    test18a($tan11, $tan12, $tan13,
            $tan21, $tan22, $tan23,
            $tan31, $tan32, $tan33,
            [0, 1, 0, 0], 0, true);
    // Test undef, firing makes 1
    test18a($tan11, $tan12, $tan13,
            $tan21, $tan22, $tan23,
            $tan31, $tan32, $tan33,
            [0, 0, 1, 1], 1, true);
        // Test undef, firing doesn't change
    test18a200($tan11, $tan12, $tan13,
               $tan21, $tan22, $tan23,
               $tan31, $tan32, $tan33,
               [1, 1, 1, 0], UNDEFINED_TXT, true);
    // Test undef, firing does change
    test18a200($tan11, $tan12, $tan13,
               $tan21, $tan22, $tan23,
               $tan31, $tan32, $tan33,
               [0, 1, 1, 1], 1, false);

    echo "Completed " . __FUNCTION__ . "\n";
}

test_example18a(__FUNCTION__);

?>
