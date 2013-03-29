<?php
class Utils
    public static function factorial($num)
{
        $total = 1;
    for ($i=1; $i<=$num; $i++)
    {
        $total=$i*$total;
    }
    return $total;
}
?>