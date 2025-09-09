<?php
declare(strict_types=1);

/*
  Chapter 1 Section 4 is about the firing rule and limiting the
  possibilities to 0 or 1.

  Example provided outputs 0, 1, or undefined, depending on the
  Hamming distance
 */

require_once('defines.php');
require_once('neural-objects.php');
require_once('neural-objects-bodges.php');

//
// Test once for known outputs
function test1($tan, $input_arr, $expected, $after) {
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

    if ($after)
        $tan->run_fast();
    if ($tan->getF(__FUNCTION__) !== $expected)
        echo "Problem with " . $input_arr[0] . $input_arr[1] .
            $input_arr[2] , " " . $msg . "\n";
}

//
// Test 200 times in case of undefined outputs
function test200($tan, $input_arr, $expected, $after) {
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

    $response0 = 0;
    $response1 = 0;
    for ($tests = 0; $tests < 200; $tests++) {
        if ($after)
            $tan->run_fast();
        $v = $tan->getF(__FUNCTION__);
        if ($v === 0)
            $response0++;
        elseif ($v === 1)
            $response1++;
        else
            echo "Unexpected output with " . $input_arr[0] .
                $input_arr[1] . $input_arr[2] , " " . $msg . "\n";
    }

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

function test_example14() {
    //
    // Just the one TAN
    $tan = new ToyAdaptiveNode_test_14;
    $tan->setNumberInputs(3);
    $tan->setUsage(USAGE_USE);

    // The before tests match only on the learning set and are
    // undefined otherwise
    //
    // test 000 before firing rule, expect 0
    test1($tan, [0, 0, 0], 0, false);

    // test 010 before firing rule, expect undef, rule affects
    test200($tan, [0, 1, 0], UNDEFINED_TXT, false);

    // test 011 before firing rule, expect undef, rule doesn't affect
    test200($tan, [0, 1, 1], UNDEFINED_TXT, false);

    // test 011 before firing rule, expect undef, rule doesn't affect
    test200($tan, [1, 1, 0], UNDEFINED_TXT, false);

    // test 101 before firing rule, expect 1
    test1($tan, [1, 0, 1], 1, false);

    // The after firing tests match on the hamming distance of the
    // values and the learning set, smallest difference wins, and
    // equal difference returned as undefined

    //
    // test 000 after firing rule, expect 0
    test1($tan, [0, 0, 0], 0, true);

    // test 010 after firing rule, expect 0
    test200($tan, [0, 1, 0], 0, true);

    // test 011 after firing rule, expect undef, rule doesn't affect
    test200($tan, [0, 1, 1], UNDEFINED_TXT, true);

    // test 110 before firing rule, expect 1
    test200($tan, [1, 1, 0], 1, true);

    // test 101 after firing rule, expect 1
    test1($tan, [1, 0, 1], 1, true);

    echo "Completed " . __FUNCTION__ . "\n";
}

test_example14(__FUNCTION__);

?>
