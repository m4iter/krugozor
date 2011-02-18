<?php
/**
 * Êëàññ-îáåğòêà íàä ôóíêöèÿìè äëÿ ğàáîòû ñ ìàññèâàìè.
 */
class Base_Array
{
    public static function array_unshift_assoc(&$arr, $key, $val)
	{
	    $arr = array_reverse($arr, true);
	    $arr[$key] = $val;
	    $arr = array_reverse($arr, true);
	    return count($arr);
	}

	/**
	 * array_shift äëÿ àññîöèàòèâíûõ ìàññèâîâ.
	 *
	 * @param array
	 * @return array
	 */
	public static function array_kshift(&$arr)
	{
	    list($k) = array_keys($arr);
	    $r = array($k => $arr[$k]);
	    unset($arr[$k]);
	    return $r;
	}
}