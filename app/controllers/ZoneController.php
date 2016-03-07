<?php

class ZoneController extends \BaseController {

  public function index(){
    $zones = Zone::select('id', 'reg_code', 'name')->get();
    return View::make('zonas.index', array('zones' =>$zones ));
  }

  public function create(){
    return View::make('zonas.create');
  }

  public function store(){
    $zone = Zone::createNew();
    $zone->setCode(trim(Input::get('code')));
    $zone->setName(trim(Input::get('name')));

    $error = $zone->guardar();


    if($error == ""){
      Session::flash('message', 'Zona creada con éxito');
      return Redirect::to('zonas');
    }
    else{
      Session::flash('error', $error);
      return Redirect::to('zonas/create');
    }
  }

  public function edit($id){
        $zones = Zone::where('id', $id)->select('id','reg_code', 'name')->first();
        return View::make('zonas.edit', array('zone' => $zones ));
  }

  public function update($id){
    $zone = Zone::find($id);
    if (Input::get('code') == $zone->reg_code){
      $zone->reg_code = Input::get('code');
    }
    else{
        $zone->setCode(trim(Input::get('code')));
    }
    $zone->setName(trim(Input::get('name')));

    $resultado = $zone->guardar();

    if($resultado == ""){
      Session::flash('message', "Zona Acualizada con éxito.");
      return Redirect::to('zonas');
    }
    else{
      $url = 'zonas/edit';
      Session::flash('error', $resultado);
      //return Redirect::to($url)->withInput();
      return Redirect::to('zonas/'.$id.'/edit');

    }
  }

  public function show($id){
      $zones = Zone::where('id', $id)->first();
      return View::make('zonas.show', array('zone' => $zones ));
  }

  public function destroy($id){
    if(Auth::user()->is_admin)
    {
      $count = 0;
      $zone = Zone::find($id);
      $clients = Client::select('zone_id')->get();
      foreach ($clients as $key => $client) {
        $u = $client->zone_id;
        $uarray = explode(',', $u);
        if(in_array($id, $uarray, true)){
          $count = $count + 1;
        }
      }

     if($count > 0){
      $field = $count == 1? ' ' : 's';
      $field2 = $count == 1? ' pertenece' : ' pertenecen';
      $message = $count. " Cliente" . $field . $field2 . " a la zona " . $zone->name;
      Session::flash('error', $message);
      return Redirect::to('zonas/'.$id);
     }
     else{
      $zone->delete();
      $message = "Zona Eliminada con éxito";
      Session::flash('message', $message);
      return Redirect::to('zonas');
     }
    }
  }
}
 ?>
