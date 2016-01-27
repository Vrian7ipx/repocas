<?php

class Zone extends EntityModel
{
	// public $timestamps = true;

	protected $table = 'zones';

	private $fv_error_message;

	public function getCode(){
		return $this->reg_code;

	}

	public function setCode($code){

		if(is_null($code)){
			$this->fv_error_message = $this->fv_error_message.'<br>- Código'.ERROR_NULL;
			return;
		}
		$codeZ = Zone::where('reg_code', $code)->select('reg_code')->first();

		if(!empty($codeZ->reg_code)){
		 $this->fv_error_message = $this->fv_error_message.'<br>- Código '.ERROR_DUPLICADO;
		 return;
		}
		$this->reg_code = $code;
		return $this;
	}

	public function getName(){
		return $this->name;
	}

	public function setName($name){
		if(is_null($name)){
			$this->fv_error_message = $this.fv_error_message.'<br>- Nombre '.ERROR_NULL;
			return;
		}
		$this->name = $name;
		return $this;
	}

	public function getErrorMessage(){
		return $this->fv_error_message;
	}

	public function validate(){
		$error_messge = "";
		if($this->fv_error_message){
			$error_messge = $error_messge.$this->fv_error_message;
		}
		return $error_messge;
	}

	public function guardar(){
		$error = $this->validate();
		if($error==""){
			$this->save();
			return false;
		}
		return $error;

	}
}
?>
