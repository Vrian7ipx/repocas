<?php

class BusinessTypeController extends \BaseController{

    public function index(){
      $businesses = BusinessType::select('id', 'cod', 'name')->get();
      return View::make('negocios.index', array('businesses' => $businesses));
    }

    public function show($id){
      $business = BusinessType::find($id);
      return View::make('negocios.show', array('business' => $business));
    }

    public function create(){
      return View::make('negocios.create');
    }

    public function store(){
      $business = BusinessType::createNew();
      $business->setCode(trim(Input::get('code')));
      $business->setName(trim(Input::get('name')));
      $error = $business->guardar();
      if($error == ""){
          Session::flash('message', 'Tipo de Negocio creado exitosamente');
          return Redirect::to('negocios');
      }
      else{
          Session::flash('error', $error);
          return Redirect::to('negocios/create');
      }

    }

    public function edit($id){
      $business = BusinessType::find($id);
      return View::make('negocios.edit', array('business' => $business));

    }

    public function update($id){
      $business = BusinessType::find($id);
      $business->setCode(trim(Input::get('code')));
      $business->setName(trim(Input::get('name')));

      $error = $business->guardar();

      if($error == ""){
          Session::flash('message', 'Tipo de Negocio actualizado con éxito');
          return Redirect::to('negocios');
      }
      else{

          $url = 'negocios/edit';
          Session::flash('error', $error);
          return Redirect::to($url)->withInput();
      }

    }

    public function destroy($id){
      if(Auth::user()->is_admin)
      {
        $count = 0;
        $business = BusinessType::find($id);
        $clients = Client::select('business_type_id')->get();
        foreach ($clients as $key => $client) {
          $u = $client->business_type_id;
          $uarray = explode(',', $u);
          if(in_array($id, $uarray, true)){
            $count = $count + 1;
          }
        }

       if($count > 0){
        $field = $count == 1? ' ' : 's';
        $field2 = $count == 1? ' pertenece' : ' pertenecen';
        $message = $count. " Cliente" . $field . $field2 . " al Tipo de Negocio  " . $business->name;
        Session::flash('error', $message);
        return Redirect::to('negocios/'.$id);
       }
       else{
        $business->delete();
        $message = "Tipo de Negocio Eliminado con éxito";
        Session::flash('message', $message);
        return Redirect::to('negocios');
       }
      }


    }




}

?>
