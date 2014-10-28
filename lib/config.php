<?php

class Config implements ArrayAccess
{
	private $cfg;

	function get($key)
	{
		if(!(array_key_exists($key, $this->cfg)))
		{
			return "";
		}
		return($this->cfg[$key]);
	}

	function keys()
	{
		return(array_keys($this->cfg));
	}

	public function offsetSet($offset, $value)
	{
		if (is_null($offset))
		{
			$this->cfg[] = $value;
		} else {
			$this->cfg[$offset] = $value;
		}
	}

	public function offsetExists($offset)
	{
		return isset($this->cfg[$offset]);
	}

	public function offsetUnset($offset)
	{
		unset($this->cfg[$offset]);
	}

	public function offsetGet($offset)
	{
		return($this->get($offset));
	}

	function __construct($path)
	{
		$this->cfg = array();

		if(!($fh = opendir($path)))
		{
			return;
		}

		while(false !== ($fn = readdir($fh)))
		{
			$m = array();
			if(preg_match("/^([^\\.]*)\\.json$/", $fn, $m) == 0)
			{
				continue;
			}
			$filename = $path . "/" . $fn;
			if(!(file_exists($filename)))
			{
				continue;
			}
			$key = $m[1];
			$obj = json_decode(file_get_contents($filename), true);
			if(!(is_array($obj)))
			{
				continue;
			}
			foreach($obj as $k=>$v)
			{
				$kk = $key . "." . $k;
				$this->cfg[$kk] = $v;
			}
		}

		closedir($fh);
	}
}
