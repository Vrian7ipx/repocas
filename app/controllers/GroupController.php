<?php

class GroupController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
   public function index(){
     $grupos = DB::table('groups')->select('code', 'name', 'delimitaciones')->get();

     return View::make('grupos.index', array('grupos' => $grupos));

   }

   public function create(){
     
     return View::make('grupos.create');
   }

   public function store(){

   }

   public function edit(){

   }

   public function show(){

   }
}
