<?php

class GroupController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
   public function index(){
     if(Auth::user()->is_admin){
       $grupos = Group::select('id','code', 'name', 'delimitaciones')->get();
       return View::make('grupos.index', array('grupos' => $grupos));
    }
    return View::make('cuentas.dashboard');
  }

   public function create(){
     return View::make('grupos.create');
   }


   public function store(){
     if(Auth::user()->is_admin){
     $group = Group::createNew();
     $group->setAccount(Auth::user()->account_id);
     $group->setCode(trim(Input::get('codigo')));
     $group->setName(trim(Input::get('name')));
     $group->setDelimitaciones(trim(Input::get('datos')));

     $error = $group->guardar();

     if($error == ""){
       Session::flash('message', 'Grupo creado con éxito');
       return Redirect::to('grupos');
     }
     else{
       Session::flash('error', $error);
       return Redirect::to('grupos/create');
     }
    }
    return View::make('cuentas.dashboard');
   }

   public function edit($id){
     if(Auth::user()->is_admin){
     $grupo = Group::where('id', $id)->first();
     return View::make('grupos.edit')->with('grupo', $grupo);
    }
    return View::make('cuentas.dashboard');
   }

   public function update($id){
     $grupo = Group::where('id', $id)->first();

     $grupo->setCode(trim(Input::get('code')));
     $grupo->setName(trim(Input::get('name')));
     $grupo->setDelimitaciones(trim(Input::get('datos')));

     $resultado = $grupo->guardar();

     if($resultado == ""){
       Session::flash('message', 'Grupo actualizado con éxito');
       return Redirect::to('grupos');
     }
     else{
       $url = 'grupos/edit';
       Session::flash('error',$resultado);
       return Redirect::to($url)->withInput();
     }

   }

   public function show($id){
     if(Auth::user()->is_admin)
     {
       $grupo = Group::where('id', $id)->first();
       return View::make('grupos.show')->with('grupo', $grupo);
     }
     return Redirect::to('/inicio');

   }

   public function destroy($id){
     if(Auth::user()->is_admin)
     {
       $count = 0;
       $grupo = Group::find($id);
       $users = User::select('group_ids')->get();
       foreach ($users as $key => $user) {
         $u = $user->group_ids;
         $uarray = explode(',', $u);
         if(in_array($id, $uarray, true)){
           $count = $count + 1;
         }
       }

      if($count > 0){
       $field = $count == 1? ' ' : 's';
       $field2 = $count == 1? ' pertenece' : ' pertenecen';
       $message = $count. " Usuario" . $field . $field2 . " al grupo " . $grupo->name;
       Session::flash('error', $message);
       return Redirect::to('grupos/'.$id);
      }
      else{
       $grupo->delete();
       $message = "Grupo Eliminado con éxito";
       Session::flash('message', $message);
       return Redirect::to('grupos');
      }
     }
   }

}
