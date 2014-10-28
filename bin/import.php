#!/usr/bin/php -q
<?php

$lib_path = dirname(dirname(__FILE__)) . "/lib";
$etc_path = dirname(dirname(__FILE__)) . "/etc";

include_once($lib_path . "/config.php");
include_once($lib_path . "/database.php");
include_once($lib_path . "/parser.php");

function get_log_files($path)
{
	$ret = array();
	if(!($fp = opendir($path)))
	{
		return($ret);
	}
	while(false !== ($fn = readdir($fp)))
	{
		$filename = $path . "/" . $fn;
		if(preg_match("/^access\\.log(.*)$/", $fn) == 0)
		{
			continue;
		}
		if(!(file_exists($filename)))
		{
			continue;
		}
		$ret[] = $filename;
	}
	closedir($fp);

	return($ret);
}

$cfg = new Config($etc_path);
$db = new LogDB($cfg);

$domain = "madhousebeyond.com";

$log_path = $cfg['paths.logs'];
$files = get_log_files($log_path);

foreach($files as $file)
{
	if(!($db->fileNeedsUpdate($file)))
	{
		continue;
	}
	$fp = fopen($file, "r");
	while($row = fgets($fp, 4096))
	{
		$info = parse_line($row);
		$db->import($info, $file, $domain);
	}
	fclose($fp);
}
