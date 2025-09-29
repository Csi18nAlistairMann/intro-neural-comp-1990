<?php
declare(strict_types=1);

/*
  Chapter 1 Section 8 demonstrates a fully autoassociative network
  endeavouring to show that it can take a randomised start point
  and can use its rules to converge on a learned pattern, thus
  demonstrating decision about what the network can see.

  C1S8 also modifies what I learn: where before I had thought the taught
  sets needed the different targets on either side, I see now that the
  different targets can be on the same side to give effect to "all 0s" or
  "all 1s" in the output. Further, that the different sides can have different
  meaning at each node.

  In learning this I also set up 'clamped' functionality even though that's
  not defined until C1S9.
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

    $w->setTanAsInput($input_arr[0]);
    $n->setTanAsInput($input_arr[1]);
    $e->setTanAsInput($input_arr[2]);
    $s->setTanAsInput($input_arr[3]);

    //
    // Process firing rules
    if ($after === MATCH_WITH_HAMMING)
        $tan->run_fast();

    //
    // And check result
    if (($v = $tan->getF(__FUNCTION__)) !== $expected)
        echo "Problem with " . $input_arr[0] . $input_arr[1] .
            $input_arr[2] . $input_arr[3] . " " . $msg . " $v $tanidx $expected\n";
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

    $w->setTanAsInput($input_arr[0]);
    $n->setTanAsInput($input_arr[1]);
    $e->setTanAsInput($input_arr[2]);
    $s->setTanAsInput($input_arr[3]);

    //
    // Run the TAN's firing rule 200x and accumulate results
    $response0 = 0;
    $response1 = 0;
    for ($tests = 0; $tests < 200; $tests++) {
        if ($after === MATCH_WITH_HAMMING)
            $tan->run_fast();
        $v = $tan->getF(__FUNCTION__);
        if ($v === 0) $response0++;
        elseif ($v === 1) $response1++;
        else echo "Unexpected output with " . $input_arr[0] .
                 $input_arr[1] . $input_arr[2] . $input_arr[3] .
                 " " . $msg . " 2\n";
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
    if ($after === MATCH_WITH_HAMMING)
        $tan23->run_fast();
    else if ($after === MATCH_TAUGHT_ONLY)
        $tan23->run_matchOnlyTaughtSet();

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
        if ($after === MATCH_WITH_HAMMING)
            $tan23->run_fast();
        else if ($after === MATCH_TAUGHT_ONLY)
            $tan23->run_matchOnlyTaughtSet();
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
    //
    // By coincidence the taught sets divide nicely into 0 for T, 1 for H,
    // which later misled me to think that they should always do so.
    // Rather, the zero set should hold all those teaching examples that
    // would result in a 0 (white) for that particular cell, the one set
    // likewise the teaching examples that should result in a 1 (black) for
    // that particular cell.
    // This example is purely about TAN23 which is only 0/white for a T, and
    // only 1/black for an H.
    $tan11 = new ToyAdaptiveNode_test_18;
    $tan11->setNumberInputs(4);
    $tan11->setUsage(USAGE_USE);
    $tan11->setTaughtSets([[1, 0, 1, 0]], [[1, 1, 0, 1]]);
    $tan12 = new ToyAdaptiveNode_test_18;
    $tan12->setNumberInputs(4);
    $tan12->setUsage(USAGE_USE);
    $tan12->setTaughtSets([[1, 1, 1, 1]], [[1, 0, 1, 1]]);
    $tan13 = new ToyAdaptiveNode_test_18;
    $tan13->setNumberInputs(4);
    $tan13->setUsage(USAGE_USE);
    $tan13->setTaughtSets([[1, 0, 1, 0]], [[0, 1, 1, 1]]);
    $tan21 = new ToyAdaptiveNode_test_18;
    $tan21->setNumberInputs(4);
    $tan21->setUsage(USAGE_USE);
    $tan21->setTaughtSets([[0, 1, 1, 0]], [[1, 1, 1, 1]]);
    $tan22 = new ToyAdaptiveNode_test_18;
    $tan22->setNumberInputs(4);
    $tan22->setUsage(USAGE_USE);
    $tan22->setTaughtSets([[0, 1, 0, 1]], [[1, 0, 1, 0]]);
    $tan23 = new ToyAdaptiveNode_test_18;
    $tan23->setNumberInputs(4);
    $tan23->setUsage(USAGE_USE);
    $tan23->setTaughtSets([[1, 1, 0, 0]], [[1, 1, 1, 1]]);
    $tan31 = new ToyAdaptiveNode_test_18;
    $tan31->setNumberInputs(4);
    $tan31->setUsage(USAGE_USE);
    $tan31->setTaughtSets([[0, 1, 1, 0]], [[1, 0, 1, 1]]);
    $tan32 = new ToyAdaptiveNode_test_18;
    $tan32->setNumberInputs(4);
    $tan32->setUsage(USAGE_USE);
    $tan32->setTaughtSets([[0, 1, 0, 1]], [[1, 1, 1, 0]]);
    $tan33 = new ToyAdaptiveNode_test_18;
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
            [1, 1, 0, 0], 0, MATCH_TAUGHT_ONLY);
    // Test H
    test18a($tan11, $tan12, $tan13,
            $tan21, $tan22, $tan23,
            $tan31, $tan32, $tan33,
            [1, 1, 1, 1], 1, MATCH_TAUGHT_ONLY);
    // Test undef, firing won't change
    test18a200($tan11, $tan12, $tan13,
               $tan21, $tan22, $tan23,
               $tan31, $tan32, $tan33,
               [1, 1, 1, 0], UNDEFINED_TXT, MATCH_TAUGHT_ONLY);
    // Test undef, firing will change
    test18a200($tan11, $tan12, $tan13,
               $tan21, $tan22, $tan23,
               $tan31, $tan32, $tan33,
               [0, 1, 1, 1], UNDEFINED_TXT, MATCH_TAUGHT_ONLY);

    // After firing tests
    //
    // Test undef, firing makes 0
    test18a($tan11, $tan12, $tan13,
            $tan21, $tan22, $tan23,
            $tan31, $tan32, $tan33,
            [0, 1, 0, 0], 0, MATCH_WITH_HAMMING);
    // Test undef, firing makes 1
    test18a($tan11, $tan12, $tan13,
            $tan21, $tan22, $tan23,
            $tan31, $tan32, $tan33,
            [0, 0, 1, 1], 1, MATCH_WITH_HAMMING);
        // Test undef, firing doesn't change
    test18a200($tan11, $tan12, $tan13,
               $tan21, $tan22, $tan23,
               $tan31, $tan32, $tan33,
               [1, 1, 1, 0], UNDEFINED_TXT, MATCH_WITH_HAMMING);
    // Test undef, firing does change
    test18a200($tan11, $tan12, $tan13,
               $tan21, $tan22, $tan23,
               $tan31, $tan32, $tan33,
               [0, 1, 1, 1], 1, MATCH_WITH_HAMMING);

    echo "Completed " . __FUNCTION__ . "\n";
}

//
// Test p14 Truth Tables for all of the TANs
//
// What we did for TAN23 above we now do for all the cells. In particular
// notice TAN11, TAN13, and TAN22 are unbalanced: this causes the node to
// always output the unbalanced result without need for clamping.
// Also notice that while TAN23 uses the first, zero set for T and the second
// one set for H, TAN12 reverses that: the zero set for H and the one set
// for T. The zero sets indicate 0/white out for that cell, the one set
// indicates 1/black out.
function test_example18b() {
    //
    // Set up TANs to have taught set as provided by T and H
    $tan11 = new ToyAdaptiveNode_test_18;
    $tan11->setNumberInputs(4);
    $tan11->setUsage(USAGE_USE);
    $tan11->setTaughtSets([], [[1, 1, 0, 1], [1, 0, 1, 0]]); // ,TH
    $tan12 = new ToyAdaptiveNode_test_18;
    $tan12->setNumberInputs(4);
    $tan12->setUsage(USAGE_USE);
    $tan12->setTaughtSets([[1, 0, 1, 1]], [[1, 1, 1, 1]]); // H,T
    $tan13 = new ToyAdaptiveNode_test_18;
    $tan13->setNumberInputs(4);
    $tan13->setUsage(USAGE_USE);
    $tan13->setTaughtSets([], [[0, 1, 1, 1], [1, 0, 1, 0]]); // ,TH
    $tan21 = new ToyAdaptiveNode_test_18;
    $tan21->setNumberInputs(4);
    $tan21->setUsage(USAGE_USE);
    $tan21->setTaughtSets([[0, 1, 1, 0]], [[1, 1, 1, 1]]); // T,H
    $tan22 = new ToyAdaptiveNode_test_18;
    $tan22->setNumberInputs(4);
    $tan22->setUsage(USAGE_USE);
    $tan22->setTaughtSets([], [[1, 0, 1, 0], [0, 1, 0, 1]]); // ,TH
    $tan23 = new ToyAdaptiveNode_test_18;
    $tan23->setNumberInputs(4);
    $tan23->setUsage(USAGE_USE);
    $tan23->setTaughtSets([[1, 1, 0, 0]], [[1, 1, 1, 1]]); // T,H
    $tan31 = new ToyAdaptiveNode_test_18;
    $tan31->setNumberInputs(4);
    $tan31->setUsage(USAGE_USE);
    $tan31->setTaughtSets([[0, 0, 1, 1]], [[1, 1, 0, 1]]); // T,H
    $tan32 = new ToyAdaptiveNode_test_18;
    $tan32->setNumberInputs(4);
    $tan32->setUsage(USAGE_USE);
    $tan32->setTaughtSets([[1, 1, 1, 0]], [[0, 1, 0, 1]]); // H,T
    $tan33 = new ToyAdaptiveNode_test_18;
    $tan33->setNumberInputs(4);
    $tan33->setUsage(USAGE_USE);
    $tan33->setTaughtSets([[1, 0, 0, 1]], [[0, 1, 1, 1]]); // T,H

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
        // Work through each TAN present
        $test = $outputs[$tanidx];
        $tan = array_shift($test);
        $truthtable = $test;

        for ($idx = 0; $idx < sizeof($test); $idx++) {
            // Work through all the inputs possible
            $inputsArr = $inputRange[$idx];
            $expected = $outputs[$tanidx][$idx + 1];

            if ($expected === 2) {
                test18b200($tan11, $tan12, $tan13,
                           $tan21, $tan22, $tan23,
                           $tan31, $tan32, $tan33,
                           [$inputsArr[0], $inputsArr[1],
                            $inputsArr[2], $inputsArr[3]],
                           UNDEFINED_TXT, MATCH_WITH_HAMMING, $tanidx, $truthtable);
            } else {
                test18b($tan11, $tan12, $tan13,
                        $tan21, $tan22, $tan23,
                        $tan31, $tan32, $tan33,
                        [$inputsArr[0], $inputsArr[1],
                         $inputsArr[2], $inputsArr[3]],
                        $expected, MATCH_WITH_HAMMING, $tanidx, $truthtable);
            }
        }
    }

    echo "Completed " . __FUNCTION__ . "\n";
}

//
// Test p15 short term memory state sequence
//
// Starting with an all zero network, if we run it once we should see a Y
// shape. Run it a second time and we should see it become a T. It's not
// said but a third time should see no changes.
// Example:
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
// 111 111 101 111 101 111 101 111
// 010 011 010 011 110 011 110 011
// 111 000 011 000 011 100 011 100
//
// Run no: 3
// 010 111 101 111 101
// 010 011 010 011 010
// 111 000 011 000 011
function test_example18c() {
    //
    // Set up TANs to have taught set as provided by T and H
    $tan11 = new ToyAdaptiveNode_test_18;
    $tan11->setNumberInputs(4);
    $tan11->setUsage(USAGE_USE);
    $tan11->setTaughtSets([], [[1, 1, 0, 1], [1, 0, 1, 0]]); // ,TH
    $tan12 = new ToyAdaptiveNode_test_18;
    $tan12->setNumberInputs(4);
    $tan12->setUsage(USAGE_USE);
    $tan12->setTaughtSets([[1, 0, 1, 1]], [[1, 1, 1, 1]]); // H,T
    $tan13 = new ToyAdaptiveNode_test_18;
    $tan13->setNumberInputs(4);
    $tan13->setUsage(USAGE_USE);
    $tan13->setTaughtSets([], [[0, 1, 1, 1], [1, 0, 1, 0]]); // ,TH
    $tan21 = new ToyAdaptiveNode_test_18;
    $tan21->setNumberInputs(4);
    $tan21->setUsage(USAGE_USE);
    $tan21->setTaughtSets([[0, 1, 1, 0]], [[1, 1, 1, 1]]); // T,H
    $tan22 = new ToyAdaptiveNode_test_18;
    $tan22->setNumberInputs(4);
    $tan22->setUsage(USAGE_USE);
    $tan22->setTaughtSets([], [[1, 0, 1, 0], [0, 1, 0, 1]]); // ,TH
    $tan23 = new ToyAdaptiveNode_test_18;
    $tan23->setNumberInputs(4);
    $tan23->setUsage(USAGE_USE);
    $tan23->setTaughtSets([[1, 1, 0, 0]], [[1, 1, 1, 1]]); // T,H
    $tan31 = new ToyAdaptiveNode_test_18;
    $tan31->setNumberInputs(4);
    $tan31->setUsage(USAGE_USE);
    $tan31->setTaughtSets([[0, 0, 1, 1]], [[1, 1, 0, 1]]); // T,H
    $tan32 = new ToyAdaptiveNode_test_18;
    $tan32->setNumberInputs(4);
    $tan32->setUsage(USAGE_USE);
    $tan32->setTaughtSets([[1, 1, 1, 0]], [[0, 1, 0, 1]]); // H,T
    $tan33 = new ToyAdaptiveNode_test_18;
    $tan33->setNumberInputs(4);
    $tan33->setUsage(USAGE_USE);
    $tan33->setTaughtSets([[1, 0, 0, 1]], [[0, 1, 1, 1]]); // T,H

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

    $tanlist = array($tan11, $tan12, $tan13,
                     $tan21, $tan22, $tan23,
                     $tan31, $tan32, $tan33);

    //
    // The book on p14 offers that all 0s resolves to T, and on p16 that all
    // 1s resolves to H.
    // I also fancied running the network starting from an I and a 1.
    $startingSets = [[0, 0, 0, 0, 0, 0, 0, 0, 0],
                     [1, 1, 1, 1, 1, 1, 1, 1, 1],
                     [1, 1, 1, 0, 1, 0, 1, 1, 1],
                     [0, 1, 0, 0, 1, 0, 1, 1, 1]];

    //
    // I'm expecting all zeroes to resolve to T as per the book.
    //
    // We'll test we got what we expected at the end, storing them here
    $results = array();

    foreach ($startingSets as $initialVals) {
        // Test each starting set in turn

        // Setup
        $stms = array();
        $hardLoopLimit = 6;
        // Initialise the network
        $index = 0;
        foreach($tanlist as $tan) {
            $tan->initialise($initialVals[$index++]);
        }

        // Obtain the stms
        $finished = false;
        $stmsHistory = array();

        // Get and record the networks "0-state"
        foreach($tanlist as $tan) {
            $stms[] = $tan->getF(__FUNCTION__);
        }
        $stmsHistory[] = $stms;

        do {
            // Run the network at least once and until finished, either by
            // timing out, or reaching a 0 cycle or 1 cycle state

            // Setup
            $stms = array();

            // Assess all the input first
            foreach($tanlist as $tan)
                $tan->runInputs();

            // Only once all inputs assessed, obtain node outputs
            foreach($tanlist as $tan) {
                $tan->runSlowOutput();
                $stms[] = $tan->getF(__FUNCTION__);
            }
            $stmsHistory[] = $stms;

            //
            // Now check for termination
            //
            // We could check against all previous short term memory states for
            // a match. However, we could also see an intermediate cycle with
            // an undefined state release us at the next go through. Skipped
            // for now
            if (($sz = sizeof($stmsHistory)) > 1) {
                if ($stmsHistory[$sz - 1] === $stmsHistory[$sz - 2])
                    // This will match on a 1 cycle stable state
                    $finished = STMS_STABLE;

                elseif ($sz > 3) {
                    // This will match on a 2 cycle stable state per page18
                    if (($stmsHistory[$sz - 1] === $stmsHistory[$sz - 3]) &&
                        ($stmsHistory[$sz - 2] === $stmsHistory[$sz - 4]))
                        $finished = STMS_CYCLE2;
                }
            }
            // And also terminate if we've run away
            if ($hardLoopLimit-- === -1)
                $finished = STMS_TOODEEP;

        } while ($finished === false);

        // Record the results of just this run for handling later
        $results[] = $stmsHistory;
    }

    //
    // Process the results into something readable
    $msh = new MemoryStatesHandler($results);
    $readout = $msh->getReadOut_c1s8();
    echo $readout;

    echo "Completed " . __FUNCTION__ . "\n";
}

test_example18a(__FUNCTION__);
test_example18b(__FUNCTION__);
test_example18c(__FUNCTION__);

?>
