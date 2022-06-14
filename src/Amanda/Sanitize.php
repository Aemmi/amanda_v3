<?php 

/**
* sanitizer
**/

namespace Src\Amanda;

class Sanitize{
	public function __construct($var){

		$this->var = $var;

		$this->clean();

	}

	private function clean(){
		$this->var = htmlspecialchars($this->var);

		$this->var = addslashes($this->var);

		$this->var = trim($this->var);

		return $this->var;
	}
}