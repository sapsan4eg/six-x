<?php
class ActiveArray {
	protected $array;

	public function __construct($array) {
	    $this->array = &$array;
	}

	public function __set($name, $value) {
	    $this->array[$name] = $value;
	}

	public function __get($name) {
	    if (is_array($this->array[$name]))
	        return new self(&$this->array[$name]);
	    else
	        return $this->array[$name];
	}
}
?>