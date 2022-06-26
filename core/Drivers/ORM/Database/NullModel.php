<?php

namespace Core\Drivers\ORM\Database;
class NullModel{

	public function nullExecute(){
		return $this;
	}

	public function __call( string $name , array $arguments){
		return $this;
	}

	public function __get($attribute){
		return null;
	}

}