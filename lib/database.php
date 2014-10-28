<?php

class LogDB
{
	private $db;

	function __construct($cfg)
	{
		$this->db = new mysqli($cfg['database.host'], $cfg['database.username'], $cfg['database.password'], $cfg['database.database'], 3306);
	}

	function __destruct()
	{
		$this->db->close();
	}

	function getDomain($domain)
	{
		$select_query = "select * from domains where domain='" . $this->db->escape_string($domain) . "';";
		$insert_query = "insert into domains (domain, title) values ('" . $this->db->escape_string($domain) . "', '');";
		$ret = array();
		$res = $this->db->query($select_query);
		if($row = $res->fetch_assoc())
		{
			$ret = $row;
		}
		if(count($ret) == 0)
		{
			$this->db->query($insert_query);
			$res = $this->db->query($select_query);
			if($row = $res->fetch_assoc())
			{
				$ret = $row;
			}
		}
		return($ret);
	}
}
