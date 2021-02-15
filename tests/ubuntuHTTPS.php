<?php
function compareArray($array1, $array2) {
    $a1 = array_unique($array1);
    $a2 = array_unique($array2);

    $layer1 = array();
    $layer2 = array();

    foreach ($a1 as $val) {
        if (in_array($val, $a2)) {
            $layer1[] = $val;
        }
    }

    foreach ($a2 as $val) {
        if (in_array($val, $a1)) {
            $layer2[] = $val;
        }
    }
    return array_unique(array_merge($layer1, $layer2));
}

$first = array('dog', 'cat', 'cat', 'bike', 'dog');
$second = array('marshmellow', 'cat', 'rover', 'billion');
$third = array('cupcakes', 'cat', 'bike', 'cat');

echo '<pre>';
    var_dump (compareArray($second, $third));
echo '</pre>';
?>
