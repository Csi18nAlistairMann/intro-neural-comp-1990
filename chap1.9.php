<?php
declare(strict_types=1);

/*
  Chapter 1 Section 9 covers how a network might cycle, and how clamping
  sways outcomes

  19a: Fig1.7a no clamp
  19b: Explores what might be errors in Fig 1.7b
  19c: Fig1.7a with clamp
*/

require_once('defines.php');
require_once('neural-objects.php');
require_once('neural-objects-bodges.php');

//
// Defines only for C1S9
//
// Figure 1.7a termination conditions
define("TEST19a01", "\n111 111 111 111 \n111 111 111 111 \n011 110 011 110\n");
define("TEST19a02", "\n111 111 111 111 111 \n111 011 110 011 110 \n011 110 011 110 011\n");
//
// The following explore the probabilities of successive STMS in Figure 1.7b
// Options from 3,3
define("TEST19b01", "\n111 111 \n111 111 \n011 010\n"); // 2,4
define("TEST19b02", "\n111 111 \n111 011 \n011 010\n"); // 1,5-up
// Options from 1,5-up
define("TEST19b03", "\n111 111 \n011 110 \n010 010\n"); // 1,5-down
define("TEST19b04", "\n111 111 \n011 010 \n010 010\n"); // 0,6
// Options from 1.5-down
define("TEST19b05", "\n111 111 \n110 010 \n010 010\n"); // 0,6
define("TEST19b06", "\n111 111 \n110 011 \n010 010\n"); // 1,5-up
// Options from 2,4
define("TEST19b07", "\n111 111 \n111 110 \n010 010\n"); // 1,5-down
define("TEST19b08", "\n111 111 \n111 010 \n010 010\n"); // 0,6
define("TEST19b09", "\n111 111 \n111 011 \n010 010\n"); // Missing to 1,5-up
define("TEST19b10", "\n111 111 \n111 111 \n010 010\n"); // Missing loop to self
//
// Figure 1.7b termination condition
define("TEST19c03a", " 111 \n");
define("TEST19c03b", " 010 \n");
define("TEST19c03c", " 010\n");

//
// Run Figure 1.7a network at least twice terminating on the bottom two
// STMS being cycled between them
// 111 111 111 111 111
// 111 011 110 011 110
// 011 110 011 110 011
// or
// 111 111 111 111
// 111 111 111 111
// 011 110 011 110
function test_example19a() {
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
    // Starting set provided from p16
    $startingSets = [[1, 1, 1, 1, 1, 1, 0, 1, 1]];

    //
    // Run the network at least twice, and terminating on cycling
    // between the bottom two STMSes
    $finished = false;
    $atleast = 2;
    do {
        $results = test19ac($startingSets, $tanlist);
        $msh = new MemoryStatesHandler($results);
        $readout = $msh->getReadOut_c1s8();
        echo $readout;

        if ($atleast-- > 1)
            continue;

        if (strpos($readout, TEST19a01) !== false)
            $finished = true;
        if (strpos($readout, TEST19a02) !== false)
            $finished = true;
    } while (!$finished);

    echo "Completed " . __FUNCTION__ . "\n";
}

//
// 19b is about exploring the probabilities that STM state A will flow to
// STM state B or C.
//
// This is necessary because my code demonstrated a behaviour other than
// allowed for in Figure 1.7b:
// - That 2,4 can lead back to 2,4 with probability 1/4
// - That 2,4 can lead to 1,5-up with probability 1/4
// And therefore the probabilities to 1,5-down and 0,6 are also 1/4.
//
// This run demonstrates both:
// 111 111 111 111 111 111
// 111 111 111 011 010 010
// 011 010 010 010 010 010
//
// 19b works by taking each STM shown, running it forward exactly once,
// and accumulating statistics on which STMS was reached. 3,3 and both 1,5s
// have two following states; 0,6 has one following state, and 2,4 has by my
// estimation four following states - not the two shown.
// By way of example:
//  $ php chap1.9.php
//   3,3   [  2,4   ]: 49.81 [ 1,5-up ]: 50.19
//  1,5-up [1,5-down]: 49.80 [  0,6   ]: 50.20
// 1,5-down[  0,6   ]: 50.07 [ 1,5-up ]: 49.93
//   2,4   [1,5-down]: 24.76 [  0,6   ]: 25.20 [ 1,5-up ]: 25.09 [2,4 loop]: 24.95
// Completed test_example19b
//
// The text both on p16 and the legend on the figure itself strongly imply the
// hamming distance is of the WHOLE network: "given a starting state which is
// closer (in Hamming distance) to one of the taught patterns" and "can be
// expected to create another pattern which is even closer." This might suggest
// some additional whole network mechanism exists. However if that mechanism
// says the hamming distance can be equivalent at each step (as 1,5-up and
// 1,5-down are) then why does it skip that 2,4 looping is similarly
// equivalent? But if one may not loop back to oneself, would one not expect to
// see that enumerated in the text?
//
// On the other hand, the LTM enumerated on p14 is clear that TAN21 and TAN23
// both respond "$" to the inputs 1110. Why would that not mean there's a path
// from 2,4 to 1,5-up?
//
// I can't find evidence of an errata for this book, and a pdf I found contains
// the same figure. I've forwarded an email to an author but absent progress
// there my assumption is that Figure 1.7b is in error. In the meantime I've
// left an errata in ./Errata/Figure1.7b
//
// A final clue might be that I know the Bolzmann machine is upcoming, for
// which it's possible that we use noise to bump over local minima. Perhaps
// this is an early hint of it.

//
// Calculate percentage values for 19b
function sortPc($a, $b, $c = null, $d = null, $e = null) {
    if ($e != null) {
        $pc = 100 / ($a + $b + $c + $d);
        $val = round($e * $pc, 2);
    } else {
        $pc = 100 / ($a + $b);
        $val = round($c * $pc, 2);
    }
    return (number_format($val, 2));
}

//
// Repeatedly run the network and return accumulated results
function runNetworkPercentage($runs_to_make, $tanlist,
                              $startingSets,
                              $termString1, $termString2,
                              $termString3 = __FUNCTION__,
                              $termString4 = __FUNCTION__) {
    $finished = false;
    $atleast = $runs_to_make;
    $ctr1 = $ctr2 = $ctr3 = $ctr4 = 0;
    do {
        $results = test19b($startingSets, $tanlist);
        $msh = new MemoryStatesHandler($results);
        $readout = $msh->getReadOut_c1s8();

        if (strpos($readout, $termString1) !== false)
            $ctr1++;
        else if (strpos($readout, $termString2) !== false)
            $ctr2++;
        else if (strpos($readout, $termString3) !== false)
            $ctr3++;
        else if (strpos($readout, $termString4) !== false)
            $ctr4++;
        else {
            echo "Big yikes!"; exit;
        }

        if ($atleast-- === 0)
            $finished = true;
    } while (!$finished);
    return array($ctr1, $ctr2, $ctr3, $ctr4);
}

//
// Explore the probabilities involved in Figure 1.7b
function test_example19b() {
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

    // Figure 1.7b requires TAN31 be clamped to zero
    $tan31->setClampedValue(0);
    $runs_to_make = 100000;

    // Check from 3,3 to 2,4 or 1,5-up (50/50)
    $startingSets = [[1, 1, 1, 1, 1, 1, 0, 1, 1]];
    list($test01b, $test02b) = runNetworkPercentage($runs_to_make, $tanlist,
                                                    $startingSets,
                                                     TEST19b01, TEST19b02);
    $a1 = sortPc($test01b, $test02b, $test01b);
    $a2 = sortPc($test01b, $test02b, $test02b);
    echo "  3,3   " .
        "[  2,4   ]: $a1 [ 1,5-up ]: $a2\n";

    // Check from 1,5 to 1,5-down or 0,6 (50/50)
    $startingSets = [[1, 1, 1, 0, 1, 1, 0, 1, 0]];
    list($test03b, $test04b) = runNetworkPercentage($runs_to_make, $tanlist,
                                                    $startingSets,
                                                    TEST19b03, TEST19b04);
    $a1 = sortPc($test03b, $test04b, $test03b);
    $a2 = sortPc($test03b, $test04b, $test04b);
    echo " 1,5-up ".
        "[1,5-down]: $a1 [  0,6   ]: $a2\n";

    // // Check from 1,5-down to 1,5-up or 0,6 (50/50)
    $startingSets = [[1, 1, 1, 1, 1, 0, 0, 1, 0]];
    list($test05b, $test06b) = runNetworkPercentage($runs_to_make, $tanlist,
                                                    $startingSets,
                                                     TEST19b05, TEST19b06);
    $a1 = sortPc($test05b, $test06b, $test05b);
    $a2 = sortPc($test05b, $test06b, $test06b);
    echo "1,5-down" .
        "[  0,6   ]: $a1 [ 1,5-up ]: $a2\n";

    //
    // Check from 2,4 to 2,4 or 1,5-up or 1,5-down or 0,6 (25/25/25/25)
    $startingSets = [[1, 1, 1, 1, 1, 1, 0, 1, 0]];
    list($test07b, $test08b, $test09b, $test10b) =
        runNetworkPercentage($runs_to_make, $tanlist, $startingSets,
                             TEST19b07, TEST19b08, TEST19b09, TEST19b10);
    $a1 = sortPc($test07b, $test08b, $test09b, $test10b, $test07b);
    $a2 = sortPc($test07b, $test08b, $test09b, $test10b, $test08b);
    $a3 = sortPc($test07b, $test08b, $test09b, $test10b, $test09b);
    $a4 = sortPc($test07b, $test08b, $test09b, $test10b, $test10b);
    echo "  2,4   " .
        "[1,5-down]: $a1 [  0,6   ]: $a2 [ 1,5-up ]: $a3 [2,4 loop]: $a4\n";

    echo "Completed " . __FUNCTION__ . "\n";
}

//
// 19c repeats 19a, this time with TAN31 clamped to zero
function test_example19c() {
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
    // Figure 1.7b uses a clamp
    $tan31->setClampedValue(0);

    //
    // Same starting set given on p16
    $startingSets = [[1, 1, 1, 1, 1, 1, 0, 1, 1]];

    //
    // Keep going for at least two cycles, terminating on seeing T
    $finished = false;
    $atleast = 2;
    do {
        $results = test19ac($startingSets, $tanlist);
        $msh = new MemoryStatesHandler($results);
        $readout = $msh->getReadOut_c1s8();
        echo $readout;

        if ($atleast-- > 1)
            continue;

        if ((strpos($readout, TEST19c03a) !== false) &&
            (strpos($readout, TEST19c03b) !== false) &&
            (strpos($readout, TEST19c03c) !== false))
            $finished = true;
    } while (!$finished);

    echo "Completed " . __FUNCTION__ . "\n";
}

//
// Run network for 19a and 19c
function test19ac($startingSets, $tanlist) {
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

        // Get and record the networks "0-state"
        $finished = false;
        $stmsHistory = array();
        foreach($tanlist as $tan) {
            $stms[] = $tan->getF(__FUNCTION__);
        }
        $stmsHistory[] = $stms;

        do {
            // Run the network at least once and until finished, either by
            // timing out, or reaching a 0 cycle or 1 cycle state

            // Setup
            $stms = array();

            // Assess all the inputs first
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
    return $results;
}

//
// Run the network for 19b which needs probability data not STMSes,
// so only runs each STMS forward one step
function test19b($startingSets, $tanlist) {
    //
    // We'll test we got what we expected at the end, storing them here
    $results = array();

    foreach ($startingSets as $initialVals) {
        // Test each starting set in turn

        // Setup
        $stms = array();
        $hardLoopLimit = -1;
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
    return $results;
}

test_example19a(__FUNCTION__);
test_example19b(__FUNCTION__);
test_example19c(__FUNCTION__);

?>
