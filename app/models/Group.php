<?php
class Group extends EntityModel
{
  protected $table = 'groups';

  private $fv_id;

  private $fv_accountId;

  private $fv_createdAt;

  private $fv_updatedAt;

  private $fv_deletedAt;

  private $fv_code;

  private $fv_name;

  private $fv_delimitaciones;

  private $fv_public;

  private $fv_userId;

  public function getId(){
    return $this->id;
  }

  public function getAccount(){
    return $this->account_id;
  }

  public function setAccount($account_id){
    if(is_null($account_id)){
      $this->fv_accountId = "cuenta ".ERROR_NULL."<br>";
      return;
    }
    $this->fv_accountId = null;
    $this->account_id = $account_id;
    return $this;
  }

  public function getCreatedAt(){
    return $this->created_at;
  }

  public function getDeletedAt(){
    return $this->updated_at;
  }

  public function setDeletedAt($deletedAt){
    if(is_null($deletedAt)){
      $this->fv_deletedAt = "borrar ".ERROR_NULL."<br>";
      return;
    }
    $this->fv_deletedAt = null;
    $this->deleted_at = $deletedAt;
    return $this;

  }

  public function getCode(){
    return $this->code;
  }

  public function setCode($code){
    if($code == ""){
      $this->fv_code = "código ".ERROR_NULL."<br>";
      return;
    }
    $this->fv_code = null;
    $this->code = $code;
    return $this;
  }

  public function getName(){
    return $this->name;
  }

  public function setName($name){
    if($name == ""){
      $this->fv_name = "nombre ".ERROR_NULL."<br>";
      return;
    }
    $this->fv_name = null;
    $this->name = $name;
    return $this;

  }

  public function getDelimitaciones(){
    return $this->delimitaciones;
  }

  public function setDelimitaciones($delimitacion){
    if(is_null($delimitacion)){
      $this->fv_delimitaciones = "delimitaciones ".ERROR_NULL."<br>";
      return;
    }
    $this->fv_delimitaciones = null;
    $this->delimitaciones = $delimitacion;
    return $this;
  }

  public function getPublic(){
    return $this->public;
  }

  public function setPublic($public){
    if(is_null($public)){
      $this->fv_public = "public ".ERROR_NULL."<br>";
      return;
    }
    $this->fv_public = null;
    $this->public = $public;
    return $this;
  }

  public function getUserId(){
    return $this->user_id;
  }

  public function setUserId($userId){
    if(is_null($userId)){
      $this->fv_userId = "Usuario ".ERROR_NULL."<br>";
      return;
    }
    $this->fv_userId = null;
    $this->user_id = $userId;
    return $this;
  }

  public function getErrorMessage(){
    return $this->error_message;
  }

  public function validate(){
    $error_messge = "";
    if($this->fv_accountId){
      $error_messge = $error_messge.$this->fv_accountId;
    }
    if($this->fv_deletedAt){
      $error_messge = $error_messge.$this->fv_deletedAt;
    }
    if($this->fv_code){
      $error_messge = $error_messge.$this->fv_code;
    }
    if($this->fv_name){
      $error_messge = $error_messge.$this->fv_name;
    }
    if($this->fv_delimitaciones){
      $error_messge = $error_messge.$this->fv_delimitaciones;
    }
    if($this->fv_public){
      $error_messge = $error_messge.$this->fv_public;
    }
    if($this->fv_userId){
      $error_messge = $error_messge.$this->fv_userId;
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
