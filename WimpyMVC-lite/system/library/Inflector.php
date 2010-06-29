<?php

class Inflector {
	public static function capitalize ($string) {
		return ucfirst($string);
	}
	public static function capitalizeAll ($string) {
		return ucwords($string);
	}
	public static function toCamelCase ($string,$separator="_") {
		$final_string = "";
		
		$word_list = array();
		$word_list = explode($separator,$string);
		
		for($i=0; $i < sizeof($word_list); ++$i) {
			if ($i == 0) {
				$final_string = $word_list[$i];
			}
			$final_string .= self::capitalize($word_list[$i]);
		}
		
		return $final_string;
	}
}