<?php

/*
	This file is designed to be dropped in and out depending on your Apache log configuration.
	The parse_string function should take a single line of Apache log and return an associative
	array containing the values remotehost, date, path, method, referer, agent, httpversion, size
	and retcode.
*/

function parse_line($string)
{
	$regexp = "/^([0-9\\.]+)([^\\[]+)\\[([^\\}]+)\\] (.+)$/";

	$m = array();
	$ret = array();
	if(preg_match($regexp, $string, $m) == 0)
	{
		return($ret);
	}

	$ds = $m[3];
	$ip = $m[1];
	$csv = str_getcsv(trim($m[4]), " ");

	$request = $csv[0];
	$m = array();
	if(preg_match("|^([A-Z]+) (.*) HTTP/([0-9\\.]+)$|", $request, $m) == 0)
	{
		return($ret);
	}

	$ret['remotehost'] = $ip;
	$ret['date'] = strtotime($ds);
	$ret['path'] = $m[2];
	$ret['method'] = $m[1];
	$ret['referer'] = $csv[3];
	$ret['agent'] = $csv[4];
	$ret['httpversion'] = $m[3];
	$ret['retcode'] = (int) $csv[1];
	$ret['size'] = (int) $csv[2];

	foreach($ret as $k=>$v)
	{
		if(strcmp($v, "-") == 0)
		{
			$ret[$k] = "";
		}
	}

	return($ret);
}
