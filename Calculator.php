<?php
/**
class Calculator
 * Created by JetBrains PhpStorm.
 * User: jokusafet
    * Date: 3/28/13
    * Time: 10:12 PM
    * To change this template use File | Settings | File Templates.
 */
{

     * @param string $a first parameter
     * @param string $b second parameter
     * @return mixed
     * @assert (0, 0) == 0
     * @assert (0, 1) == 1
     * @assert (1, 0) == 1
     * @assert (1, 1) == 2

    public function add($a, $b)
    {
        return $a + $b;
    }
}

?>
