<?php

class BusinessType extends EntityModel
{

	private $fv_error_message;

	protected $table = 'business_types';

	public function getId(){
		return $this->id;
	}

	public function getAccountId(){
		return $this->account_id;
	}

	public function getCode(){
		return $this->code;
	}

	public function setCode($code){
		if(is_null($code)){
			$this->fv_error_message = $this->fv_error_message.'<br>- Código'.ERROR_NULL;
			return;
		}
		$codeZ = BusinessType::where('cod', $code)->select('cod')->first();

		if(!empty($codeZ->cod)){
		 $this->fv_error_message = $this->fv_error_message.'<br>- Código '.ERROR_DUPLICADO;
		 return;
		}
		$this->cod = $code;
		return $this;
	}

	public function getName(){
		return $this->name;
	}

	public function setName($name){
		if(is_null($name)){
			$this->fv_error_message = $this->fv_error_message.'<br> - Nombre '.ERROR_NULL;
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
