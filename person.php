<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jokusafet
 * Date: 3/28/13
 * Time: 12:02 AM
 * To change this template use File | Settings | File Templates.
 */

class Person {
    public $name;

    public function __construct ( $name ) {
        $this->name = $name;

    }
    public function getName() {
        return $this->name;
    }


}

?>