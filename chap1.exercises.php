<?php
declare(strict_types=1);

/*
  Chapter 1 Exercises 3 & 4 look at how clamping (not, or 0, or 1) does or
  does not sway outcomes
*/

require_once('defines.php');
require_once('neural-objects.php');
require_once('neural-objects-bodges.php');

//
// Defines only for C1Exercises
//
// Indicies for recording alternative outcomes as well as STABLE,
// TOODEEP, and CYCLE2
define("STMS_STABLE_T", 501);
define("STMS_STABLE_H", 502);
// Index for storing the starting pattern for that test
define("STMS_INIT_VALS", 503);
//
// How many times to test one pattern for alternative resolutions
define("PASSES", 5000);
// Maximum cycles to wait for resolution to happen
define("HARDLOOPLIMIT", 6);
//
// Test to distinguish calls
define("NO3N4QUESTION", "3n4 question");
define("NO1ANSWER", "No.1 Answer");
define("NO4ANSWER", "No.4 Answer");
//
// Termination conditions for exercises 3 and 4
// T
define("TESTExercise3n403a", " 111 \n");
define("TESTExercise3n403b", " 010 \n");
define("TESTExercise3n403c", " 010\n");
//H
define("TESTExercise3n404a", " 101 \n");
define("TESTExercise3n404b", " 111 \n");
define("TESTExercise3n404c", " 101\n");
//
//
define("C1EX3N4SET",  [[0, 0, 0, 0, 0, 0, 1, 0, 0],
                       [0, 0, 0, 1, 1, 1, 0, 0, 0],
                       [1, 0, 0, 0, 0, 1, 0, 1, 0],
                       [1, 1, 1, 1, 1, 1, 0, 1, 1],

                       [0, 0, 0, 1, 1, 1, 0, 0, 1],
                       [0, 0, 0, 1, 1, 1, 0, 1, 1],
                       [0, 0, 1, 0, 0, 1, 0, 1, 1],
                       [0, 1, 0, 0, 1, 0, 0, 1, 1],
                       [0, 1, 0, 1, 1, 1, 1, 0, 0],
                       [0, 1, 1, 1, 1, 0, 0, 0, 0],

                       [1, 0, 1, 0, 0, 0, 1, 0, 0],
                       [1, 0, 1, 0, 1, 0, 1, 1, 1],
                       [1, 0, 1, 0, 1, 1, 1, 1, 1],
                       [1, 0, 1, 1, 1, 1, 1, 1, 1],
                       [1, 1, 0, 0, 1, 0, 0, 1, 0],
                       [1, 1, 0, 0, 1, 1, 0, 1, 1],
                       [1, 1, 1, 0, 0, 0, 0, 1, 0],
                       [1, 1, 1, 1, 0, 1, 1, 0, 1]]);

define("C1EXA1SET", [[0, 1, 1, 0, 0, 0, 0, 0, 0],
                     [1, 1, 1, 0, 0, 1, 1, 1, 1]]);

define("C1EXA4SET", [[1, 0, 1, 0, 0, 0, 0, 1, 0]]);

define("C1EXA4BUG", [[0,0,0,0,0,0,1,0,0]]);

//
// Setup the network
function test_exampleExercise($startingSets, $clamped, $which) {
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
    // Exercise invites us to use no clamp, clamped to 0, and clamped to 1
    if ($clamped === UNCLAMPED) {
        echo "No clamp\n";
        $tan22->setClampedValue(UNCLAMPED);
    } else if ($clamped === CLAMPED0) {
        echo "0 clamp\n";
        $tan22->setClampedValue(CLAMPED0);
    } else if ($clamped === CLAMPED1) {
        echo "1 clamp\n";
        $tan22->setClampedValue(CLAMPED1);
    }

    $show_work = ($which === NO4ANSWER) ? true : false;

    runNetwork($startingSets, $tanlist, $show_work);
    echo "Completed " . __FUNCTION__ . " " . $which . "\n";
}

//
// Actually run the network and aggregate statistics over multiple passes
function runNetwork($startingSets, $tanlist, $show_work) {
    foreach ($startingSets as $initialVals) {
        // Test each starting set several times in turn

        // Make ready to hold the original patterns and stats, +1 each time
        // we have that outcome. T and H independent of STABLE, TOODEEP,
        // and CYCLE2
        $localResult = array(STMS_INIT_VALS => $initialVals,
                             STMS_STABLE => 0, STMS_TOODEEP => 0,
                             STMS_CYCLE2 => 0, STMS_STABLE_T => 0,
                             STMS_STABLE_H => 0);

        // Repeat the same starting point PASSES times to account for
        // undefineds leading to differing resolutions
        for ($i = 0; $i < PASSES; $i++) {
            // Setup for multiple runs in a pass
            $stms = array();
            $hardLoopLimit = HARDLOOPLIMIT;
            // Initialise the network
            $index = 0;
            foreach($tanlist as $tan) {
                $tan->initialise($initialVals[$index++]);
            }

            // Get and record the networks "0-state"
            $finished = false;
            $stmsHistory = array();
            foreach($tanlist as $tan) {
                $stms[] = $tan->getF(__FUNCTION__);
            }
            $stmsHistory[] = $stms;
            if ($show_work)
                showPatternKwik($stms);

            do {
                // Run the network at least once and until finished, either by
                // timing out, or reaching a 0 cycle or 1 cycle state

                // Setup for single run in one pass
                $stms = array();

                // Assess all the inputs first
                foreach($tanlist as $tan)
                    $tan->runInputs();

                // Only once all inputs assessed, obtain and store node outputs
                foreach($tanlist as $tan) {
                    $tan->runSlowOutput();
                    $stms[] = $tan->getF(__FUNCTION__);
                }
                $stmsHistory[] = $stms;
                if ($show_work)
                    showPatternKwik($stms);

                //
                // Now check for termination
                //
                // We could check against all previous short term memory states for
                // a match. However, we could also see an intermediate cycle with
                // an undefined state release us at the next go through. Skipped
                // for now
                // And also terminate if we've run away
                if ($hardLoopLimit-- === -1)
                    // If we're in too deep, we don't need to check the other
                    // conditions
                    $finished = STMS_TOODEEP;

                else if (($sz = sizeof($stmsHistory)) > 1) {
                    if ($stmsHistory[$sz - 1] === $stmsHistory[$sz - 2]) {
                        // This will match on a 1 cycle stable state
                        $finished = STMS_STABLE;

                    } elseif ($sz > 3) {
                        // This will match on a 2 cycle stable state per page18
                        if (($stmsHistory[$sz - 1] === $stmsHistory[$sz - 3]) &&
                            ($stmsHistory[$sz - 2] === $stmsHistory[$sz - 4])) {
                            $finished = STMS_CYCLE2;
                        }
                    }
                }
            } while ($finished === false);
            //
            // Answer 4 just show the working for the first pass
            $show_work = false;

            //
            // Record why we finished, and whether we finished on a
            // prototype pattern
            $localResult[$finished]++;
            if ($stms === [1, 1, 1, 0, 1, 0, 0, 1, 0])
                $localResult[STMS_STABLE_T] = $localResult[$finished];
            else if ($stms === [1, 0, 1, 1, 1, 1, 1, 0, 1])
                $localResult[STMS_STABLE_H] = $localResult[$finished];
        }

        //
        // Inform the user as to what was found during those passes
        reportOneLine($localResult);

    } // now look to check the next pattern
}

function showPatternKwik($stms) {
    echo "Now: " . implode(",", $stms) . "\n";
}

//
// Convert totals as percentages
function sortPc($a, $b, $c = null, $d = null, $e = null) {
    if ($e !== null) {
        $big = ($a + $b + $c + $d);
        $mult = $e;
    } else if ($d !== null) {
        $big = ($a + $b + $c);
        $mult = $d;
    } else {
        $big = ($a + $b);
        $mult = $c;
    }

    if ($big === 0) {
        $val = "0.00";
    } else {
        $pc = 100 / $big;
        $val1 = round($mult * $pc, 2);
        $val2 = number_format($val1, 2);
        if ($val2 === "100.00")
            $val2 = "100";
        $val = $val2;
    }
    return $val;
}

//
// Report stats for multiple passes one one pattern
function reportOneLine($result) {
    $s1 = $result[STMS_STABLE];
    $d1 = $result[STMS_TOODEEP];
    $c1 = $result[STMS_CYCLE2];

    $s2 = sortPc($s1, $d1, $c1, $s1);
    $d2 = sortPc($s1, $d1, $c1, $d1);
    $c2 = sortPc($s1, $d1, $c1, $c1);

    $t1 = $result[STMS_STABLE_T];
    $h1 = $result[STMS_STABLE_H];

    $t2 = sortPc($t1, $h1, PASSES - $t1 - $h1, $t1);
    $h2 = sortPc($t1, $h1, PASSES - $t1 - $h1, $h1);

    echo "Pattern: " . implode(",", $result[STMS_INIT_VALS]) . "  ";
    echo " Stable: " . alignNumber($s2, 5) . "  ";
    echo "TooDeep: " . alignNumber($d2, 5) . "  ";
    echo "  Cycle: " . alignNumber($c2, 5) . "  ";
    echo "      T: " . alignNumber($t2, 5) . "  ";
    echo "      H: " . alignNumber($h2, 5) . "  ";
    echo "\n";
}

//
// Force values to justify right
function alignNumber($num, $len) {
    return (substr("     $num", 0 - $len));
}

//
// Exercise says to test things three ways
test_exampleExercise(C1EX3N4SET, UNCLAMPED, NO3N4QUESTION);
test_exampleExercise(C1EX3N4SET, CLAMPED0, NO3N4QUESTION);
test_exampleExercise(C1EX3N4SET, CLAMPED1, NO3N4QUESTION);
test_exampleExercise(C1EXA1SET, UNCLAMPED, NO1ANSWER);
test_exampleExercise(C1EXA4SET, CLAMPED0, NO4ANSWER);

//
// Looking for source of error, used this to explore how clamping
// was going wrong. Clamping TAN31 per C1S9, not TAN22 per the
// exercises. Duh
// test_exampleExercise(C1EXA4BUG, CLAMPED0, NO4ANSWER);

?>
