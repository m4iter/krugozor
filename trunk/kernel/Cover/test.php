<?php
error_reporting(E_ALL);
include('Interface.php');
include('kernel/Cover/Abstract/Simple.php');
include('kernel/Cover/Abstract/Array.php');

$array = new Cover_Array( array('foo', 12345, 'element' => array('key' => 'value', 'key2' => 'value2')) );

echo $array->item(0); // foo

echo '<br><br>';

echo $array->element->key; // value

echo '<br><br>';

echo $array->element->count(); // 1

echo '<br><br>';

echo $array->element->append('привет, PHP!')->item(0); // привет, PHP!

echo '<br><br>';

echo $array->element->count(); // 2

echo '<br><br>';

print_r($array->getDataAsArray()); // получаем обычный массив

echo '<br><br>';

foreach ($array->element as $key => $value)
{
    echo "$key => $value<br>";
}

echo '<br>';

// array приводится (!) к типу Cover_Array
$array->is_array = array(1,2,3);
print_r($array->is_array); // Cover_Array Object ( [data:protected] => Array ( [0] => 1 [1] => 2 [2] => 3 ) ) 

var_dump($array->non_exists_prop); // NULL, никаких Notice 
?>
