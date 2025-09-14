<?php
declare(strict_types=1);

/*
  Chapter 1 Section 8 demonstrates a fully autoassociative network
  endeavouring to show that it can take a randomised start point
  and can use its rules to converge on a learned pattern, thus
  demonstrating decision about what the network can see.

  C1S8 also differs because we're not just about the hamming distance. Take
  the truth table on page 14 with the output of Tan 11 all 1s for both T
  and H. T solves as 0 and H as 1: to have all 1s as an output, we need to
  add a layer:
  This pixel is of type 0, 'T', and so should output a 1.
  This pixel is of type 1, 'H', and so should output a 1.
  This code bodges it by skipping the hamming distance altogether and using
  the inputs to index into the stored truth table.
*/

require_once('defines.php');
require_once('neural-objects.php');
require_once('neural-objects-bodges.php');

//
// Testing truth tables on p14, no need for undefineds
function test18b($tan11, $tan12, $tan13,
                 $tan21, $tan22, $tan23,
                 $tan31, $tan32, $tan33,
                 $input_arr, $expected, $after, $tanidx, $truthtable) {
    $msg = ($after === true) ? "after" : "before";

    switch ($tanidx) {
    case 0: $tan = $tan11; $w = $tan13; $n = $tan31; $e = $tan12; $s = $tan21; break;
    case 1: $tan = $tan12; $w = $tan11; $n = $tan32; $e = $tan13; $s = $tan22; break;
    case 2: $tan = $tan13; $w = $tan12; $n = $tan33; $e = $tan11; $s = $tan23; break;
    case 3: $tan = $tan21; $w = $tan23; $n = $tan11; $e = $tan22; $s = $tan31; break;
    case 4: $tan = $tan22; $w = $tan21; $n = $tan12; $e = $tan23; $s = $tan32; break;
    case 5: $tan = $tan23; $w = $tan22; $n = $tan13; $e = $tan21; $s = $tan33; break;
    case 6: $tan = $tan31; $w = $tan33; $n = $tan21; $e = $tan32; $s = $tan11; break;
    case 7: $tan = $tan32; $w = $tan31; $n = $tan22; $e = $tan33; $s = $tan12; break;
    case 8: $tan = $tan33; $w = $tan32; $n = $tan23; $e = $tan31; $s = $tan13; break;
    default: echo "Unexpected tanidx: $tanidx\n"; return;
    }

    //
    // Notice additional bodge - we're storing entire truth table
    // no just the taught sets from it
    $tan->setTaughtSets($truthtable, []);

    $w->setTanAsInput($input_arr[0]);
    $n->setTanAsInput($input_arr[1]);
    $e->setTanAsInput($input_arr[2]);
    $s->setTanAsInput($input_arr[3]);

    //
    // Process firing rules
    if ($after)
        $tan->run_fast();

    //
    // And check result
    if ($tan->getF(__FUNCTION__) !== $expected)
        echo "Problem with " . $input_arr[0] . $input_arr[1] .
            $input_arr[2] . $input_arr[3] . " " . $msg . " 1\n";
}

//
// Testing truth tables on p14, including need for undefineds
function test18b200($tan11, $tan12, $tan13,
                    $tan21, $tan22, $tan23,
                    $tan31, $tan32, $tan33,
                    $input_arr, $expected, $after, $tanidx, $truthtable) {
    $msg = ($after === true) ? "after 200" : "before";

    switch ($tanidx) {
    case 0: $tan = $tan11; $w = $tan13; $n = $tan31; $e = $tan12; $s = $tan21; break;
    case 1: $tan = $tan12; $w = $tan11; $n = $tan32; $e = $tan13; $s = $tan22; break;
    case 2: $tan = $tan13; $w = $tan12; $n = $tan33; $e = $tan11; $s = $tan23; break;
    case 3: $tan = $tan21; $w = $tan23; $n = $tan11; $e = $tan22; $s = $tan31; break;
    case 4: $tan = $tan22; $w = $tan21; $n = $tan12; $e = $tan23; $s = $tan32; break;
    case 5: $tan = $tan23; $w = $tan22; $n = $tan13; $e = $tan21; $s = $tan33; break;
    case 6: $tan = $tan31; $w = $tan33; $n = $tan21; $e = $tan32; $s = $tan11; break;
    case 7: $tan = $tan32; $w = $tan31; $n = $tan21; $e = $tan33; $s = $tan12; break;
    case 8: $tan = $tan33; $w = $tan32; $n = $tan21; $e = $tan31; $s = $tan13; break;
    default: echo "Unexpected tanidx: $tanidx\n"; return;
    }

    //
    // Notice additional bodge - we're storing entire truth table
    // no just the taught sets from it
    $tan->setTaughtSets($truthtable, []);

    $w->setTanAsInput($input_arr[0]);
    $n->setTanAsInput($input_arr[1]);
    $e->setTanAsInput($input_arr[2]);
    $s->setTanAsInput($input_arr[3]);

    //
    // Run the TAN's firing rule 200x and accumulate results
    $response0 = 0;
    $response1 = 0;
    for ($tests = 0; $tests < 200; $tests++) {
        if ($after) $tan->run_fast();
        $v = $tan->getF(__FUNCTION__);
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
    //
    // Run the TAN's firing rule 200x and accumulate results
    if ($after)
        $tan23->run_fast();

    $v = $tan23->getF(__FUNCTION__);

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
               [0, 1, 1, 1], 1, true);

    echo "Completed " . __FUNCTION__ . "\n";
}

//
// Test p14 Truth Tables for all of the TANs
function test_example18b() {
    //
    // Set up TANs to have taught set as provided by T and H
    $tan11 = new ToyAdaptiveNode_test_18b;
    $tan11->setNumberInputs(4);
    $tan11->setUsage(USAGE_USE);
    $tan11->setTaughtSets([[1, 0, 1, 0]], [[1, 1, 0, 1]]);
    $tan12 = new ToyAdaptiveNode_test_18b;
    $tan12->setNumberInputs(4);
    $tan12->setUsage(USAGE_USE);
    $tan12->setTaughtSets([[1, 1, 1, 1]], [[1, 0, 1, 1]]);
    $tan13 = new ToyAdaptiveNode_test_18b;
    $tan13->setNumberInputs(4);
    $tan13->setUsage(USAGE_USE);
    $tan13->setTaughtSets([[1, 0, 1, 0]], [[0, 1, 1, 1]]);
    $tan21 = new ToyAdaptiveNode_test_18b;
    $tan21->setNumberInputs(4);
    $tan21->setUsage(USAGE_USE);
    $tan21->setTaughtSets([[0, 1, 1, 0]], [[1, 1, 1, 1]]);
    $tan22 = new ToyAdaptiveNode_test_18b;
    $tan22->setNumberInputs(4);
    $tan22->setUsage(USAGE_USE);
    $tan22->setTaughtSets([[0, 1, 0, 1]], [[1, 0, 1, 0]]);
    $tan23 = new ToyAdaptiveNode_test_18b;
    $tan23->setNumberInputs(4);
    $tan23->setUsage(USAGE_USE);
    $tan23->setTaughtSets([[1, 1, 0, 0]], [[1, 1, 1, 1]]);
    $tan31 = new ToyAdaptiveNode_test_18b;
    $tan31->setNumberInputs(4);
    $tan31->setUsage(USAGE_USE);
    $tan31->setTaughtSets([[0, 1, 1, 0]], [[1, 0, 1, 1]]);
    $tan32 = new ToyAdaptiveNode_test_18b;
    $tan32->setNumberInputs(4);
    $tan32->setUsage(USAGE_USE);
    $tan32->setTaughtSets([[0, 1, 0, 1]], [[1, 1, 1, 0]]);
    $tan33 = new ToyAdaptiveNode_test_18b;
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

    //
    // We are going to check all the possible inputs for each node, so
    // we'll need this nine times
    $inputRange = [[0, 0, 0, 0], [0, 0, 0, 1], [0, 0, 1, 0], [0, 0, 1, 1],
                   [0, 1, 0, 0], [0, 1, 0, 1], [0, 1, 1, 0], [0, 1, 1, 1],
                   [1, 0, 0, 0], [1, 0, 0, 1], [1, 0, 1, 0], [1, 0, 1, 1],
                   [1, 1, 0, 0], [1, 1, 0, 1], [1, 1, 1, 0], [1, 1, 1, 1]];

    //
    // For the nine times we do the above 16 checks, we need 16
    // expected outputs
    // Note $ in the book is down here as 2
    $outputs = [[$tan11, 1,1,1,1, 1,1,1,1, 1,1,1,1, 1,1,1,1],
                [$tan12, 0,0,0,0, 1,1,1,1, 0,0,0,0, 1,1,1,1],
                [$tan13, 1,1,1,1, 1,1,1,1, 1,1,1,1, 1,1,1,1],

                [$tan21, 0,2,0,2, 0,2,0,2, 2,1,2,1, 2,1,2,1],
                [$tan22, 1,1,1,1, 1,1,1,1, 1,1,1,1, 1,1,1,1],
                [$tan23, 0,2,2,1, 0,2,2,1, 0,2,2,1, 0,2,2,1],

                [$tan31, 0,0,0,0, 1,1,0,0, 1,1,0,0, 1,1,1,1],
                [$tan32, 1,1,0,1, 1,1,0,1, 0,1,0,0, 0,1,0,0],
                [$tan33, 0,0,1,1, 1,1,1,1, 0,0,0,0, 0,0,1,1]];

    for ($tanidx = 0; $tanidx < sizeof($outputs); $tanidx++) {
        $test = $outputs[$tanidx];
        $tan = array_shift($test);
        $truthtable = $test;

        for ($idx = 0; $idx < sizeof($test); $idx++) {
            $inputsArr = $inputRange[$idx];
            $expected = $outputs[$tanidx][$idx + 1];

            if ($expected === 2) {
                test18b200($tan11, $tan12, $tan13,
                           $tan21, $tan22, $tan23,
                           $tan31, $tan32, $tan33,
                           [$inputsArr[0], $inputsArr[1],
                            $inputsArr[2], $inputsArr[3]],
                           UNDEFINED_TXT, true, $tanidx, $truthtable);
            } else {
                test18b($tan11, $tan12, $tan13,
                        $tan21, $tan22, $tan23,
                        $tan31, $tan32, $tan33,
                        [$inputsArr[0], $inputsArr[1],
                         $inputsArr[2], $inputsArr[3]],
                        $expected, true, $tanidx, $truthtable);
            }
        }
        $tanidx++;
    }

    echo "Completed " . __FUNCTION__ . "\n";
}

test_example18a(__FUNCTION__);
test_example18b(__FUNCTION__);

?>
