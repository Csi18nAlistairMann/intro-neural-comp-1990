<?php
declare(strict_types=1);

/*
  Chapter 1 Section 5 looks at using three TANs to differentiate
  between T and H in an 3x3 graphic
 */

require_once('defines.php');
require_once('neural-objects.php');
require_once('neural-objects-bodges.php');

//
// Test one TAN once for known outputs
function test1a($tan, $input_arr, $expected, $after) {
    $msg = ($after === true) ? "after" : "before";

    //
    // Overall inputs
    $tan_input_xx1 = new ToyAdaptiveNode;
    $tan_input_xx2 = new ToyAdaptiveNode;
    $tan_input_xx3 = new ToyAdaptiveNode;
    $tan_input_xx1->setTanAsInput($input_arr[0]);
    $tan_input_xx2->setTanAsInput($input_arr[1]);
    $tan_input_xx3->setTanAsInput($input_arr[2]);
    $tan->setInputNAsTan(0, $tan_input_xx1);
    $tan->setInputNAsTan(1, $tan_input_xx2);
    $tan->setInputNAsTan(2, $tan_input_xx3);

    //
    // Process firing rules
    if ($after)
        $tan->run_fast();

    //
    // And check result
    if ($tan->getF(__FUNCTION__) !== $expected)
        echo "Problem with " . $input_arr[0] . $input_arr[1] .
            $input_arr[2] , " " . $msg . "\n";
}

//
// Test one TAN 200 times in case of undefined outputs
function test200a($tan, $input_arr, $expected, $after) {
    $msg = ($after === true) ? "after" : "before";

    //
    // Overall inputs
    $tan_input_a = new ToyAdaptiveNode;
    $tan_input_b = new ToyAdaptiveNode;
    $tan_input_c = new ToyAdaptiveNode;
    $tan_input_a->setTanAsInput($input_arr[0]);
    $tan_input_b->setTanAsInput($input_arr[1]);
    $tan_input_c->setTanAsInput($input_arr[2]);
    $tan->setInputNAsTan(0, $tan_input_a);
    $tan->setInputNAsTan(1, $tan_input_b);
    $tan->setInputNAsTan(2, $tan_input_c);

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
                 $input_arr[1] . $input_arr[2] , " " . $msg . "\n";
    }

    //
    // Check results
    if ($expected === UNDEFINED_TXT) {
        if ($response0 === 0 || $response1 === 0)
            echo "Problem with " . $input_arr[0] . $input_arr[1] .
                $input_arr[2] , " " . $msg . " undef\n";
    } elseif ($expected === 0) {
        if ($response0 === 0 || $response1 !== 0)
            echo "Problem with " . $input_arr[0] . $input_arr[1] .
                $input_arr[2] , " " . $msg . " 0\n";
    } elseif ($expected === 1) {
        if ($response0 !== 0 || $response1 === 0)
            echo "Problem with " . $input_arr[0] . $input_arr[1] .
                $input_arr[2] , " " . $msg . " 1\n";
    } else {
        echo "Unexpected expected with " . $input_arr[0] . $input_arr[1] .
            $input_arr[2] , " " . $msg . " 1\n";
    }
}

//
// Test three TANs once for known outputs
function test1b($tan_a, $tan_b, $tan_c, $input_arr, $expected, $after) {
    $msg = ($after === true) ? "after" : "before";

    //
    // Overall inputs
    $tan_input_x11 = new ToyAdaptiveNode;
    $tan_input_x12 = new ToyAdaptiveNode;
    $tan_input_x13 = new ToyAdaptiveNode;
    $tan_input_x21 = new ToyAdaptiveNode;
    $tan_input_x22 = new ToyAdaptiveNode;
    $tan_input_x23 = new ToyAdaptiveNode;
    $tan_input_x31 = new ToyAdaptiveNode;
    $tan_input_x32 = new ToyAdaptiveNode;
    $tan_input_x33 = new ToyAdaptiveNode;
    $tan_input_x11->setTanAsInput($input_arr[0]);
    $tan_input_x12->setTanAsInput($input_arr[1]);
    $tan_input_x13->setTanAsInput($input_arr[2]);
    $tan_input_x21->setTanAsInput($input_arr[3]);
    $tan_input_x22->setTanAsInput($input_arr[4]);
    $tan_input_x23->setTanAsInput($input_arr[5]);
    $tan_input_x31->setTanAsInput($input_arr[6]);
    $tan_input_x32->setTanAsInput($input_arr[7]);
    $tan_input_x33->setTanAsInput($input_arr[8]);
    //
    // Attach Overall Inputs to respective TANs
    $tan_a->setInputNAsTan(0, $tan_input_x11);
    $tan_a->setInputNAsTan(1, $tan_input_x12);
    $tan_a->setInputNAsTan(2, $tan_input_x13);
    $tan_b->setInputNAsTan(0, $tan_input_x21);
    $tan_b->setInputNAsTan(1, $tan_input_x22);
    $tan_b->setInputNAsTan(2, $tan_input_x23);
    $tan_c->setInputNAsTan(0, $tan_input_x31);
    $tan_c->setInputNAsTan(1, $tan_input_x32);
    $tan_c->setInputNAsTan(2, $tan_input_x33);

    //
    // Run the firing rules for each TAN
    if ($after) {
        $tan_a->run_fast();
        $tan_b->run_fast();
        $tan_c->run_fast();
    }

    //
    // Check results align
    if ($tan_a->getF(__FUNCTION__) !== $expected[0] ||
        $tan_b->getF(__FUNCTION__) !== $expected[1] ||
        $tan_c->getF(__FUNCTION__) !== $expected[2]) {
        echo "Problem with " . $expected[0] . $expected[1] .
            $expected[2] , " " . $msg . "\n";
    }
}

//
// Test Three TAN's 200 times in case of undefined outputs
function test200b($tan_a, $tan_b, $tan_c, $input_arr, $expected, $after) {
    $msg = ($after === true) ? "after" : "before";

    //
    // Overall inputs
    $tan_input_x11 = new ToyAdaptiveNode;
    $tan_input_x12 = new ToyAdaptiveNode;
    $tan_input_x13 = new ToyAdaptiveNode;
    $tan_input_x21 = new ToyAdaptiveNode;
    $tan_input_x22 = new ToyAdaptiveNode;
    $tan_input_x23 = new ToyAdaptiveNode;
    $tan_input_x31 = new ToyAdaptiveNode;
    $tan_input_x32 = new ToyAdaptiveNode;
    $tan_input_x33 = new ToyAdaptiveNode;
    $tan_input_x11->setTanAsInput($input_arr[0]);
    $tan_input_x12->setTanAsInput($input_arr[1]);
    $tan_input_x13->setTanAsInput($input_arr[2]);
    $tan_input_x21->setTanAsInput($input_arr[3]);
    $tan_input_x22->setTanAsInput($input_arr[4]);
    $tan_input_x23->setTanAsInput($input_arr[5]);
    $tan_input_x31->setTanAsInput($input_arr[6]);
    $tan_input_x32->setTanAsInput($input_arr[7]);
    $tan_input_x33->setTanAsInput($input_arr[8]);
    //
    // Attach Overall Inputs to respective TANs
    $tan_a->setInputNAsTan(0, $tan_input_x11);
    $tan_a->setInputNAsTan(1, $tan_input_x12);
    $tan_a->setInputNAsTan(2, $tan_input_x13);
    $tan_b->setInputNAsTan(0, $tan_input_x21);
    $tan_b->setInputNAsTan(1, $tan_input_x22);
    $tan_b->setInputNAsTan(2, $tan_input_x23);
    $tan_c->setInputNAsTan(0, $tan_input_x31);
    $tan_c->setInputNAsTan(1, $tan_input_x32);
    $tan_c->setInputNAsTan(2, $tan_input_x33);

    //
    // Run the TAN's firing rule 200x and accumulate results
    $responsef10 = 0;
    $responsef11 = 0;
    $responsef20 = 0;
    $responsef21 = 0;
    $responsef30 = 0;
    $responsef31 = 0;
    for ($tests = 0; $tests < 200; $tests++) {
        if ($after) {
            $tan_a->run_fast();
            $tan_b->run_fast();
            $tan_c->run_fast();
        }
        $f1 = $tan_a->getF(__FUNCTION__);
        $f2 = $tan_b->getF(__FUNCTION__);
        $f3 = $tan_c->getF(__FUNCTION__);
        if ($f1 === 0) $responsef10++;
        elseif ($f1 === 1) $responsef11++;
        else echo "Unexpected output with f1 " . $msg . "\n";
        if ($f2 === 0) $responsef20++;
        elseif ($f2 === 1) $responsef21++;
        else echo "Unexpected output with f2 " . $msg . "\n";
        if ($f3 === 0) $responsef30++;
        elseif ($f3 === 1) $responsef31++;
        else echo "Unexpected output with f3 " . $msg . "\n";
    }

    //
    // And check results align
    if ($expected[0] === UNDEFINED_TXT) {
        if ($responsef10 === 0 || $responsef11 === 0)
            echo "Problem with tan_a not undef\n";
    } elseif ($expected[0] === 0) {
        if ($responsef10 === 0 || $responsef11 !== 0)
            echo "Problem with tan_a not 0\n";
    } elseif ($expected[0] === 1) {
        if ($responsef10 !== 0 || $responsef11 === 0)
            echo "Problem with tan_a not 1\n";
    } else {
        echo "Unexpected expected[0]=" . $expected[0] . "\n";
    }

    if ($expected[1] === UNDEFINED_TXT) {
        if ($responsef20 === 0 || $responsef21 === 0)
            echo "Problem with tan_b not undef\n";
    } elseif ($expected[1] === 0) {
        if ($responsef20 === 0 || $responsef21 !== 0)
            echo "Problem with tan_b not 0\n";
    } elseif ($expected[1] === 1) {
        if ($responsef20 !== 0 || $responsef21 === 0)
            echo "Problem with tan_b not 1\n";
    } else {
        echo "Unexpected expected[1]=" . $expected[1] . "\n";
    }

    if ($expected[2] === UNDEFINED_TXT) {
        if ($responsef30 === 0 || $responsef31 === 0)
            echo "Problem with tan_c not undef\n";
    } elseif ($expected[2] === 0) {
        if ($responsef30 === 0 || $responsef31 !== 0)
            echo "Problem with tan_c not 0\n";
    } elseif ($expected[2] === 1) {
        if ($responsef30 !== 0 || $responsef31 === 0)
            echo "Problem with tan_c not 1\n";
    } else {
        echo "Unexpected expected[2]=" . $expected[2] . "\n";
    }
}

//
// Test the individual TANs per the p9 truth table
function test_example15a() {
    //
    // Set up TANs to have taught set as provided by T and H
    $tan_1 = new ToyAdaptiveNode_test_15;
    $tan_1->setNumberInputs(3);
    $tan_1->setUsage(USAGE_USE);
    $tan_1->setTaughtSets([[1, 0, 1]], [[1, 1, 1]]);
    $tan_2 = new ToyAdaptiveNode_test_15;
    $tan_2->setNumberInputs(3);
    $tan_2->setUsage(USAGE_USE);
    $tan_2->setTaughtSets([[1, 1, 1]], [[0, 1, 0]]);
    $tan_3 = new ToyAdaptiveNode_test_15;
    $tan_3->setNumberInputs(3);
    $tan_3->setUsage(USAGE_USE);
    $tan_3->setTaughtSets([[1, 0, 1]], [[0, 1, 0]]);

    //
    // Test individual TANs
    //
    // Yes, yes, I know I'm not testing for failures directly. I have been
    // making and looking for fails but my focus is the flow of data rather
    // than the structure of the bodges
    //
    // Test that the TAN responds as per the truth table for T, H,
    // and undefineds
    test1a($tan_1, [0, 0, 0], 0, true);
    test1a($tan_1, [1, 1, 1], 1, true);
    test1a($tan_2, [1, 0, 1], 0, true);
    test1a($tan_2, [0, 1, 0], 1, true);
    test200a($tan_2, [0, 1, 1], UNDEFINED_TXT, true);
    test200a($tan_2, [1, 1, 0], UNDEFINED_TXT, true);
    test1a($tan_3, [0, 0, 0], 1, true);
    test1a($tan_3, [1, 1, 1], 0, true);

    echo "Completed " . __FUNCTION__ . "\n";
}

//
// Test the three TANs together, looking to see that all three outputs
// match expectations
function test_example15b() {
    //
    // Set up TANs to have taught set as provided by T and H
    $tan_1 = new ToyAdaptiveNode_test_15;
    $tan_1->setNumberInputs(3);
    $tan_1->setUsage(USAGE_USE);
    $tan_1->setTaughtSets([[1, 0, 1]], [[1, 1, 1]]);
    $tan_2 = new ToyAdaptiveNode_test_15;
    $tan_2->setNumberInputs(3);
    $tan_2->setUsage(USAGE_USE);
    $tan_2->setTaughtSets([[1, 1, 1]], [[0, 1, 0]]);
    $tan_3 = new ToyAdaptiveNode_test_15;
    $tan_3->setNumberInputs(3);
    $tan_3->setUsage(USAGE_USE);
    $tan_3->setTaughtSets([[1, 0, 1]], [[0, 1, 0]]);

    //
    // Test individual TANs
    //
    // Testing Patterns a and b
    test1b($tan_1, $tan_2, $tan_3, [1, 1, 1, 0, 1, 0, 0, 1, 0], [1, 1, 1], true);
    test1b($tan_1, $tan_2, $tan_3, [1, 0, 1, 1, 1, 1, 1, 0, 1], [0, 0, 0], true);
    //
    // Testing pattern c
    test200b($tan_1, $tan_2, $tan_3, [0, 1, 0, 0, 1, 1, 0, 1, 1],
             [1, UNDEFINED_TXT, 1], true);

    echo "Completed " . __FUNCTION__ . "\n";
}

test_example15a(__FUNCTION__);
test_example15b(__FUNCTION__);

?>
