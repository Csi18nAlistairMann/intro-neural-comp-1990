<?php
declare(strict_types=1);

/*
  Chapter 1 Section 3 focuses on collecting TANs together into a variety
  of networkl shapes. From a code point of view I'm looking to focus on
  how the individual TANs can reference each other.

 */

require_once('defines.php');
require_once('neural-objects.php');
require_once('neural-objects-bodges.php');

function test_example13a() {
    $offset_a = 100;
    $offset_b = 200;
    $offset_c = 300;

    //
    // The inputs make use of the TAN object solely to generalise
    // referencing
    $tan_input_a = new ToyAdaptiveNode;
    $tan_input_a->setTanAsInput(1);
    $tan_input_b = new ToyAdaptiveNode;
    $tan_input_b->setTanAsInput(1);
    $tan_input_c = new ToyAdaptiveNode;
    $tan_input_c->setTanAsInput(1);

    //
    // Create the nodes as seen in example (a). Each node is bodged to output
    // the sum of the inputs ontop of a provided offset, to differentiate them
    $tan_a = new ToyAdaptiveNode_test_13;
    $tan_a->setNumberInputs(3);
    $tan_a->setUsage(USAGE_USE);
    $tan_a->setInputNAsTan(0, $tan_input_a);
    $tan_a->setInputNAsTan(1, $tan_input_b);
    $tan_a->setInputNAsTan(2, $tan_input_c);
    $tan_a->offset = $offset_a;

    $tan_b = new ToyAdaptiveNode_test_13;
    $tan_b->setNumberInputs(3);
    $tan_b->setUsage(USAGE_USE);
    $tan_b->setInputNAsTan(0, $tan_input_a);
    $tan_b->setInputNAsTan(1, $tan_input_b);
    $tan_b->setInputNAsTan(2, $tan_input_c);
    $tan_b->offset = $offset_b;

    $tan_c = new ToyAdaptiveNode_test_13;
    $tan_c->setNumberInputs(3);
    $tan_c->setUsage(USAGE_USE);
    $tan_c->setInputNAsTan(0, $tan_input_a);
    $tan_c->setInputNAsTan(1, $tan_input_b);
    $tan_c->setInputNAsTan(2, $tan_input_c);
    $tan_c->offset = $offset_c;

    //
    // First recognition that code will have to "run a turn"
    $tan_a->run();
    $tan_b->run();
    $tan_c->run();

    //
    if ($tan_a->getF(__FUNCTION__) != $offset_a + 3)
        echo "Problem with TAN A\n";

    if ($tan_b->getF(__FUNCTION__) != $offset_b + 3)
        echo "Problem with TAN B\n";

    if ($tan_c->getF(__FUNCTION__) != $offset_c + 3)
        echo "Problem with TAN C\n";

    echo "Completed " . __FUNCTION__ . "\n";
}

function test_example13b() {
    $offset_a = 100;
    $offset_b = 200;
    $offset_c = 300;

    //
    // The inputs make use of the TAN object solely to generalise
    // referencing
    $tan_input_a = new ToyAdaptiveNode;
    $tan_input_a->setTanAsInput(1);
    $tan_input_b = new ToyAdaptiveNode;
    $tan_input_b->setTanAsInput(1);
    $tan_input_c = new ToyAdaptiveNode;
    $tan_input_c->setTanAsInput(1);
    $tan_input_d = new ToyAdaptiveNode;
    $tan_input_d->setTanAsInput(1);
    $tan_input_e = new ToyAdaptiveNode;
    $tan_input_e->setTanAsInput(1);
    $tan_input_f = new ToyAdaptiveNode;
    $tan_input_f->setTanAsInput(1);

    //
    // This time only the first two TANs connect to inputs above
    $tan_a = new ToyAdaptiveNode_test_13;
    $tan_a->setNumberInputs(3);
    $tan_a->setUsage(USAGE_USE);
    $tan_a->setInputNAsTan(0, $tan_input_a);
    $tan_a->setInputNAsTan(1, $tan_input_b);
    $tan_a->setInputNAsTan(2, $tan_input_c);
    $tan_a->offset = $offset_a;

    $tan_b = new ToyAdaptiveNode_test_13;
    $tan_b->setNumberInputs(3);
    $tan_b->setUsage(USAGE_USE);
    $tan_b->setInputNAsTan(0, $tan_input_d);
    $tan_b->setInputNAsTan(1, $tan_input_e);
    $tan_b->setInputNAsTan(2, $tan_input_f);
    $tan_b->offset = $offset_b;

    //
    // The third TAN connects not to the inputs but to the TANs above
    $tan_c = new ToyAdaptiveNode_test_13;
    $tan_c->setNumberInputs(2);
    $tan_c->setUsage(USAGE_USE);
    $tan_c->setInputNAsTan(0, $tan_a);
    $tan_c->setInputNAsTan(1, $tan_b);
    $tan_c->offset = $offset_c;

    //
    // First recognition that code will have to "run a turn"
    $tan_a->run();
    $tan_b->run();
    $tan_c->run();

    //
    if ($tan_a->getF(__FUNCTION__) != $offset_a + 3)
        echo "Problem with TAN A\n";

    if ($tan_b->getF(__FUNCTION__) != $offset_b + 3)
        echo "Problem with TAN B\n";

    if ($tan_c->getF(__FUNCTION__) != $offset_c + $offset_b + $offset_a + 3 + 3)
        echo "Problem with TAN C\n";

    echo "Completed " . __FUNCTION__ . "\n";
}

function test_example13c() {
    $offset_a = 100;
    $offset_b = 200;
    $offset_c = 300;

    //
    // The inputs make use of the TAN object solely to generalise
    // referencing
    $tan_input_a = new ToyAdaptiveNode;
    $tan_input_a->setTanAsInput(1);
    $tan_input_b = new ToyAdaptiveNode;
    $tan_input_b->setTanAsInput(1);
    $tan_input_c = new ToyAdaptiveNode;
    $tan_input_c->setTanAsInput(1);
    $tan_input_d = new ToyAdaptiveNode;
    $tan_input_d->setTanAsInput(1);
    $tan_input_e = new ToyAdaptiveNode;
    $tan_input_e->setTanAsInput(1);

    //
    // Third tan also has second has input, as second has forst
    $tan_a = new ToyAdaptiveNode_test_13;
    $tan_a->setNumberInputs(3);
    $tan_a->setUsage(USAGE_USE);
    $tan_a->setInputNAsTan(0, $tan_input_a);
    $tan_a->setInputNAsTan(1, $tan_input_b);
    $tan_a->setInputNAsTan(2, $tan_input_c);
    $tan_a->offset = $offset_a;

    $tan_b = new ToyAdaptiveNode_test_13;
    $tan_b->setNumberInputs(2);
    $tan_b->setUsage(USAGE_USE);
    $tan_b->setInputNAsTan(0, $tan_a);
    $tan_b->setInputNAsTan(1, $tan_input_d);
    $tan_b->offset = $offset_b;

    $tan_c = new ToyAdaptiveNode_test_13;
    $tan_c->setNumberInputs(2);
    $tan_c->setUsage(USAGE_USE);
    $tan_c->setInputNAsTan(0, $tan_b);
    $tan_c->setInputNAsTan(1, $tan_input_e);
    $tan_c->offset = $offset_c;

    $tan_a->run();
    $tan_b->run();
    $tan_c->run();

    //
    if ($tan_a->getF(__FUNCTION__) != $offset_a + 3)
        echo "Problem with TAN A\n";

    if ($tan_b->getF(__FUNCTION__) != $offset_b + $offset_a + 4)
        echo "Problem with TAN B\n";

    if ($tan_c->getF(__FUNCTION__) != $offset_c + $offset_b + $offset_a + 5)
        echo "Problem with TAN C\n";

    echo "Completed " . __FUNCTION__ . "\n";
}

function test_example13d() {
    $offset_a = 100;
    $offset_b = 200;
    $offset_c = 300;

    //
    // The inputs make use of the TAN object solely to generalise
    // referencing
    $tan_input_a = new ToyAdaptiveNode;
    $tan_input_a->setTanAsInput(1);
    $tan_input_b = new ToyAdaptiveNode;
    $tan_input_b->setTanAsInput(1);
    $tan_input_c = new ToyAdaptiveNode;
    $tan_input_c->setTanAsInput(1);
    $tan_input_d = new ToyAdaptiveNode;
    $tan_input_d->setTanAsInput(1);

    //
    // Third tan also has second has input, as second has forst
    $tan_a = new ToyAdaptiveNode_test_13;
    $tan_b = new ToyAdaptiveNode_test_13;
    $tan_c = new ToyAdaptiveNode_test_13;

    $tan_a->setNumberInputs(2);
    $tan_a->setUsage(USAGE_USE);
    $tan_a->setInputNAsTan(0, $tan_input_a);
    $tan_a->setInputNAsTan(1, $tan_input_b);
    $tan_a->offset = $offset_a;

    $tan_b->setNumberInputs(3);
    $tan_b->setUsage(USAGE_USE);
    $tan_b->setInputNAsTan(0, $tan_input_c);
    $tan_b->setInputNAsTan(1, $tan_input_d);
    $tan_b->setInputNAsTan(2, $tan_c);
    $tan_b->offset = $offset_b;

    $tan_c->setNumberInputs(2);
    $tan_c->setUsage(USAGE_USE);
    $tan_c->setInputNAsTan(0, $tan_a);
    $tan_c->setInputNAsTan(1, $tan_b);
    $tan_c->offset = $offset_c;

    //
    // Because this example uses feedback, we run the network twice.
    // As it stands, each time the second and third TAN runs, the output
    // accumulates higher and higher
    //
    // On first run, Third TAN has undefined F which resolves as adding
    // 0 or 1 to the normal operation of the Second TAN.
    $lastpass = $offset_c + $offset_b + $offset_a + 2 + 2;
    for ($tests = 0; $tests < 2; $tests++) {

        // First TAN works as above
        $tan_a->run();
        if ($tan_a->getF(__FUNCTION__) != $offset_a + 2)
            echo "Problem with TAN A\n";

        // Second TAN refers to third TAN so has two different responses
        // when the third hasn't been run
        // Once the third Tan has run, it's output is no longer undefined,
        // but it still propagates the difference from the previous run
        $response0 = 0;
        $response1 = 0;
        for ($a = 0; $a < 200; $a++) {
            $tan_b->run();
            $v = $tan_b->getF(__FUNCTION__);
            if ($v === ($lastpass * $tests) + $offset_b + 2 + 0)
                $response0++;
            elseif ($v === ($lastpass * $tests) + $offset_b + 2 + 1)
                $response1++;
            else
                echo "Problem with TAN B pass $tests neither expected >$response0-$response1<\n";
        }
        if ($tests === 0 && ($response0 === 0 || $response1 === 0))
            echo "Problem with TAN B pass $tests >$response0-$response1<\n";

        // Third TAN receives from First as above; Second is in one of
        // two states because it received from Third before Third ran
        $tan_c->run();
        $v = $tan_c->getF(__FUNCTION__);
        if ($v !== ($lastpass * $tests) + $offset_c + $offset_b + $offset_a + 2 + 2 + 0 &&
            $v !== ($lastpass * $tests) + $offset_c + $offset_b + $offset_a + 2 + 2 + 1)
            echo "Problem with TAN C pass $tests\n";
    }

    echo "Completed " . __FUNCTION__ . "\n";
}

function test_example13e() {
    $offset_a = 100;
    $offset_b = 200;

    // There are no inputs for this example

    //
    // Two TANs with each drawing their input from both TANs
    $tan_a = new ToyAdaptiveNode_test_13;
    $tan_b = new ToyAdaptiveNode_test_13;

    $tan_a->setNumberInputs(2);
    $tan_a->setUsage(USAGE_USE);
    $tan_a->setInputNAsTan(0, $tan_a);
    $tan_a->setInputNAsTan(1, $tan_b);
    $tan_a->offset = $offset_a;

    $tan_b->setNumberInputs(2);
    $tan_b->setUsage(USAGE_USE);
    $tan_b->setInputNAsTan(0, $tan_a);
    $tan_b->setInputNAsTan(1, $tan_b);
    $tan_b->offset = $offset_b;

    // This network is fully autoassociative per p5, and so both TANs will be
    // undefined, so we might again see +0, +1 before first run.
    $response0 = 0;
    $response1 = 0;
    for ($tests = 0; $tests < 200; $tests++) {
        $v = $tan_a->getF(__FUNCTION__);
        switch ($v) {
        case 0: $response0++; break;
        case 1: $response1++; break;
        default:
            echo "Problem with TAN A\n";
        }
    }
    if ($response0 === 0 || $response1 === 0)
        echo "Problem with TAN A pass 0 - >$response0-$response1<\n";

    $response0 = 0;
    $response1 = 0;
    for ($tests = 0; $tests < 200; $tests++) {
        $v = $tan_b->getF(__FUNCTION__);
        switch ($v) {
        case 0: $response0++; break;
        case 1: $response1++; break;
        default:
            echo "Problem with TAN B pass 0\n";
        }
    }
    if ($response0 === 0 || $response1 === 0)
        echo "Problem with TAN B pass 0 - >$response0-$response1<\n";

    // The problem is we are run()ing a node before the input nodes have
    // been run(), and with each node using the other nodes for input, we
    // have a circular problem: the 'undefined' output value might change
    // between passes;
    //
    // In particular, I'm using $offset to propagate a value forwards. So
    // when I run $tan_a, it uses 0/1 and not offset+0/1 from tan_b. OTOH
    // because I've run $tan_a, $tan_b IS able to use offset+0/1 from tan_a
    //
    // Solution is two-fold around splitting actions in time by making the
    // output at T+1 derive from the inputs at T+0
    // for each node, if there's a cached output bring it forward; if not,
    // assess as undefined;
    // for each node, assess the inputs and cache result for output next time
    //
    // This will require redoing the previous tests shortly.

    // Settle any undefined outputs, including offset
    $tan_a->run_SettleOutput();
    $tan_b->run_SettleOutput();

    $v = $tan_a->getF(__FUNCTION__);
    if ($v !== $offset_a + 0 && $v !== $offset_a + 1)
        echo "Problem with TAN A - pass 1 >$v<\n";

    $v = $tan_b->getF(__FUNCTION__);
    if ($v !== $offset_b + 0 && $v !== $offset_b + 1)
        echo "Problem with TAN B - pass 1 >$v<\n";

    // This needs another step: the inputs being processed should be made
    // available at the next tick of the network, not at this one.
    // - settle undefined outputs
    // - use outputs to assess inputs
    // - wait for tick
    // - make assessment available at new outputs
    // Then settle the inputs
    $tan_a->run_SettleInput();
    $tan_b->run_SettleInput();

    $v = $tan_a->getF(__FUNCTION__);
    if (!($v === $offset_a + 0 || $v === $offset_a + 1))
        echo "Problem with TAN A - pass 2 >$v<\n";
    $v = $tan_b->getF(__FUNCTION__);
    if (!($v === $offset_b + 0 || $v === $offset_b + 1))
        echo "Problem with TAN B - pass 2 >$v<\n";

    // Now first use of feeding the inputs forward
    $tan_a->run_SettleOutput();
    $tan_b->run_SettleOutput();

    $v = $tan_a->getF(__FUNCTION__);
    if ($v !== $offset_b + 0 + ($offset_a * 2) + 0 &&
        $v !== $offset_b + 0 + ($offset_a * 2) + 1 &&
        $v !== $offset_b + 1 + ($offset_a * 2) + 0 &&
        $v !== $offset_b + 1 + ($offset_a * 2) + 1)
        echo "Problem with TAN A - pass 3 >$v<\n";

    $v = $tan_b->getF(__FUNCTION__);
    if ($v !== ($offset_b * 2) + 0 + $offset_a + 0 &&
        $v !== ($offset_b * 2) + 0 + $offset_a + 1 &&
        $v !== ($offset_b * 2) + 1 + $offset_a + 0 &&
        $v !== ($offset_b * 2) + 1 + $offset_a + 1)
        echo "Problem with TAN B - pass 3 >$v<\n";

    echo "Completed " . __FUNCTION__ . "\n";
}

test_example13a(__FUNCTION__);
test_example13b(__FUNCTION__);
test_example13c(__FUNCTION__);
test_example13d(__FUNCTION__);
test_example13e(__FUNCTION__);

?>
