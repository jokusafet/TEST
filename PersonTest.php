<?php

require_once 'person.php';
class PersonTest extends PHPUnit_Framework_TestCase {
    public $test;
    public function setUp() {
        $this->test = new Person("Jokusafet");
    }
    public function testName() {
        $jokusafet = $this->test->getName();
        $this->assertTrue($jokusafet == "Jokusafet");

    }
}

?>