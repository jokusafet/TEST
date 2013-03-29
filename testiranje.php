<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jokusafet
 * Date: 3/27/13
 * Time: 10:14 PM
 * To change this template use File | Settings | File Templates.
 */
function compute_value($i) {
    return $i*$i+($i*2);
}
function test_compute_value_zero_when_param_zero() {
    $res = compute_value(0);
    $this->assertIdentical($res,0);
}
