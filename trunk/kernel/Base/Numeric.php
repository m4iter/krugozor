<?php
class Base_Numeric
{
    public static function is_decimal($value, $unsigned=false)
	{
	    $pattern = $unsigned ? '~^([0-9]+)$~' : '~^([+\-]?[1-9][0-9]*|0)$~';

	    if (preg_match($pattern, strval($value), $matches))
	    {
	        return $matches;
	    }

	    return FALSE;
	}
}
?>