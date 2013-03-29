<?php

require_once 'PHPUnit/Framework/TestCase.php';

require_once 'Utils1.php';

/**
 * Utils test cases
 */
class UtilsTest extends PHPUnit_Framework_TestCase {

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp() {
        parent::setUp ();
        // currently Utils.php is just a set of functions
        // if it becomes a class, this is where we would instantiate it like so:
        // $this->Utils = new Utils();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown() {
        parent::tearDown ();
        // currently Utils.php is just a set of functions
        // if it becomes a class, this is where we would cleanup the instance like so:
        // $this->Utils = null;
    }

    /**
     * Constructs the test case.
     */
    public function __construct() {
    }

    /**
     * Tests array_occursOdd()
     */
    public function testArray_occursOdd() {
        // define test cases
        $testValues = array(
            'arg' => array( // arguments to test
                array(1,2,71,2,3,5,1),
                array(1,2,2,3),
                array(1,1),
                array('b','a','b')
            ),
            'exp' => array( // expected results for each argument
                array(3,5,71),
                array(1,3),
                array(),
                array('a')
            )
        );

        // run each case
        for($i=0; $i < sizeof($testValues['arg']); $i++) {
            $result = array_occursOdd($testValues['arg'][$i]);
            $this->assertSame($testValues['exp'][$i],$result,'test case #'.($i+1).': ');
        }
    }
}
?>
