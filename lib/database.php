<?php

class LogDB
{
	private $db;
	private $file_cache;

	function __construct($cfg)
	{
		$this->file_cache = array();
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

	function getFile($path)
	{
		if(array_key_exists($path, $this->file_cache))
		{
			return($this->file_cache[$path]);
		}

		$select_query = "select * from files where filename='" . $this->db->escape_string(basename($path)) . "';";
		$insert_query = "insert into files (filename, size, filetime) values ('" . $this->db->escape_string(basename($path)) . "', '" . filesize($path) . "', '" . gmdate("Y-m-d H:i:s", filemtime($path)) . "');";
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

		$this->file_cache[$path] = $ret;
		return($ret);
	}

	function getHost($ip)
	{
		$select_query = "select * from hosts where ip='" . $this->db->escape_string($ip) . "';";
		$ret = array();
		$res = $this->db->query($select_query);
		if($row = $res->fetch_assoc())
		{
			$ret = $row;
		}
		if(count($ret) == 0)
		{
			$host = gethostbyaddr($ip);
			if(strcmp($host, $ip) == 0) { $host = ""; }
			$insert_query = "insert into hosts (ip, host, country) values ('" . $this->db->escape_string($ip) . "', '" . $this->db->escape_string($host) . "', '');";
			$this->db->query($insert_query);
			$res = $this->db->query($select_query);
			if($row = $res->fetch_assoc())
			{
				$ret = $row;
			}
		}
		return($ret);
	}

	function getPath($path)
	{
		if(strlen(stristr($path, "?")) > 0)
		{
			$ext = preg_replace("|^([^\\?]+)/([^/\\?]+)\\.([^/\\.\\?]+)\\?(.*)$|", "$3", $path);
		} else {
			$ext = preg_replace("|^(.+)/([^/]+)\\.([^/\\.]+)$|", "$3", $path);
		}
		if(strcmp($ext, $path) == 0) { $ext = ""; }
		$select_query = "select * from paths where path='" . $this->db->escape_string($path) . "';";
		$insert_query = "insert into paths (path, ext) values ('" . $this->db->escape_string($path) . "', '" . $this->db->escape_string($ext) . "');";
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

	function getReferer($url)
	{
		$select_query = "select * from referers where url='" . $this->db->escape_string($url) . "';";
		$insert_query = "insert into referers (url) values ('" . $this->db->escape_string($url) . "');";
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

	function getUserAgent($agent_string)
	{
		$select_query = "select * from agents where agent='" . $this->db->escape_string($agent_string) . "';";
		$insert_query = "insert into agents (agent) values ('" . $this->db->escape_string($agent_string) . "');";
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

	function fileNeedsUpdate($filename)
	{
		$filesize = 0;
		$filedate = 0;
		$id = -1;
		$ret = false;
		$query = "select * from files where filename='" . $this->db->escape_string(basename($filename)) . "'";
		$res = $this->db->query($query);
		if($row = $res->fetch_assoc())
		{
			$filesize = $row['size'];
			$filedate = strtotime($row['filetime'] . " GMT");
			$id = $row['id'];
		}
		if(filesize($filename) != $filesize)
		{
			$ret = true;
		}
		if(filemtime($filename) != $filedate)
		{
			$ret = true;
		}
		if($ret & ($id >= 0))
		{
			$query = "delete from entries where file='" . $id . "';";
			$this->db->query($query);
			$query = "delete from files where id='" . $id . "';";
			$this->db->query($query);
		}

		return($ret);
	}

	function import($info, $file, $domain)
	{
		$fileinfo = $this->getFile($file);
		$dominfo = $this->getDomain($domain);

		$hostinfo = $this->getHost($info['remotehost']);
		$pathinfo = $this->getPath($info['path']);
		$refererinfo = $this->getReferer($info['referer']);
		$agentinfo = $this->getUserAgent($info['agent']);

		$file_id = $fileinfo['id'];
		$domain_id = $dominfo['id'];
		$host_id = $hostinfo['id'];
		$path_id = $pathinfo['id'];
		$referer_id = $refererinfo['id'];
		$agent_id = $agentinfo['id'];

		$values = array(
			$this->db->escape_string($host_id),
			$this->db->escape_string($domain_id),
			gmdate("Y-m-d H:i:s", $info['date']),
			$this->db->escape_string($info['method']),
			$this->db->escape_string($path_id),
			$this->db->escape_string($info['httpversion']),
			$this->db->escape_string($info['retcode']),
			$this->db->escape_string($info['size']),
			$this->db->escape_string($referer_id),
			$this->db->escape_string($agent_id),
			$this->db->escape_string($file_id)
		);

		$query = "insert into entries ";
		$query .= "(remotehost, domain, date, method, path, httpversion, retcode, size, referer, agent, file) ";
		$query .= "values ";
		$query .= "('" . implode("', '", $values) . "');";

		$this->db->query($query);
	}
}
