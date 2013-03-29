<?php
/**
 * array_occursOdd sifts through an array and returns a list of values that are found
 * to repeat an odd number of times
 * this works with int, string, bool but array, object, function, referece all are examined as string represenations
 * so two different objects will be considered the same
 * this is a limitation of array_diff, which only does a single dimension and (string) cast compare
 * if a comparison method is needed to examin exact values of object, array, function, reference, a more
 * robust method would be needed
 *
 * @param array $array A mixed array to parse
 * @return array The list of mixed type values that were found an odd number of times
 *      in the passed array, ordered alpha-numeric ascending
 */
function array_occursOdd($array) {
    $oddItems = array(); // holder for the values to be returned

    // loop through the given array and build the item list
    for( $i=0; $i<sizeof($array); $i++ ) {
        if(in_array($array[$i], $oddItems, true)===true)    // found a duplicate
            $oddItems = array_diff($oddItems,array($array[$i]));    // remove it (happens every even occurance)
        else    // not found
            array_push($oddItems,$array[$i]);   // add it (happens every odd occurance)
    }

    sort($oddItems); // order the array and clean it up so that the index begins with zero

    return $oddItems;
}
?>
