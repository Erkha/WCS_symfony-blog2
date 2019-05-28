<?php
namespace App\Service;

Class Slugify 
{
	public function generate(string $input) : string
	{
    $input = str_replace("é", 'e', $input);
    $input = str_replace("à", 'a', $input);
 		$input = trim(preg_replace("/[^a-zA-Z0-9 ]/","",$input));

		return str_replace(" ", "-", $input);
	}
} 