<?php

class UserController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */

	public function index()
	{
		//
		 if (Auth::user()->is_admin)
		 {
			// $usuarios  = Account::find(Session::get('account_id'))->users;
			$usuarios =Account::find(Auth::user()->account_id)->users;
			// return Response::json(array('usuarios'=>$usuarios));
			return View::make('users.index')->with('usuarios',$usuarios);
		 }
		return Redirect::to('/inicio');
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{

		if (Auth::user()->is_admin)
		{
			$sucursales =Account::find(Auth::user()->account_id)->branches;
                        $prices = PriceType::get();
                        $groups = Group::get();
                        $data = [
                            'sucursales'=>$sucursales,
                            'precios' => $prices,
                            'grupos'=>$groups,
                        ];
			return View::make('users.create',$data);
		}
		return Redirect::to('/inicio');
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
		//en caso de no haber sesion
		// return Response::json(Input::all());

		if (Auth::user()->is_admin)
		{
                    $usuario = User::createNew();
                    $usuario->setUsername(Input::get('username'));
                    $usuario->setPassword(Input::get('password'),Input::get('password_confirm'));
                    $usuario->setFirstName(Input::get('first_name'));
                    $usuario->setLastName(Input::get('last_name'));
                    $usuario->setEmail(Input::get('email'));
                    $usuario->setPhone(Input::get('phone'));
                    if(Input::get('admin')==1)
                        $usuario->is_admin = 1;
                    else
                        $usuario->is_admin = 0;
                    //$usuario->branch_id = Input::get('branch');
                    $usuario->setPriceType(Input::get('price'));
                    $usuario->setGroupId(implode(",",Input::get('groups')));
					$usuario->setBranch(Input::get('branch'));
			// return var_dump($usuario);

		if($usuario->Guardar())
        {
	        //redireccionar con el mensaje a la siguiente vista

	        Session::flash('message',$usuario->getErrorMessage());


	        if(Input::has('branch'))
	        {
	                foreach (Input::get('branch') as $branch_id) {
	                        # code...
	                        // $cantidad = $cantidad +$sucursal;
	                        $userbranch= UserBranch::createNew();
	                        $userbranch->account_id = Auth::user()->account_id;
	                        $userbranch->user_id = $usuario->id;
	                        $userbranch->branch_id = $branch_id;
	                        // $userbranch->branch_id = UserBranch::getPublicId();
	                        $userbranch->save();

	                }
	        }
	        return Redirect::to('usuarios');
        }
		Session::flash('error',$usuario->getErrorMessage());

		return Redirect::to('usuarios/create');

		}
		return Redirect::to('inicio');

	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//

		if (Auth::user()->is_admin)
		{

			$usuario = User::where('id',$id)->first();
                        if($usuario->is_admin==1)
                            $rol="Administrador";
                        else
                            $rol="Facturador";
                        $price = PriceType::where('id',$usuario->price_type_id)->first();
                        //print_r(explode(',', $usuario->group_ids));
                        //return 0;
                        $groups = Group::whereIn('id',  explode(',', $usuario->group_ids))->get();
                        $data=[
                            'usuario'=>$usuario,
                            'rol'=>$rol,
                            'precio'=>$price->name,
                            'grupos' => $groups,
                        ];

			return View::make('users.show',$data);
		}
		return Redirect::to('/inicio');


	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		if (Auth::user()->is_admin)
		{
			$usuario = User::where('id',$id)->first();
                        $sucursales =Account::find(Auth::user()->account_id)->branches;
                        $sucs=array();
                        $ubras= UserBranch::where('user_id',$usuario->id)->get();
                        foreach ($ubras as $ubra)
                            array_push($sucs,$ubra->branch_id);
                        $prices = PriceType::get();
                        $groups = Group::get();
                        $grus=array();
                        $grupos_s= explode(",",$usuario->group_ids);
                        foreach ($grupos_s as $g)
                            array_push($grus,$g);

                        $data = [
                            'usuario'=>$usuario,
                            'sucursales'=>$sucursales,
                            'precios' => $prices,
                            'grupos'=>$groups,
                            'sucs'=>$sucs,
                            'grus'=>$grus,
                            'bbr'=>1,
                        ];
			return View::make('users.edit',$data);
		}
		return Redirect::to('/inicio');
	}



	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
		// return Response::json(Input::all());
		if (Auth::user()->is_admin)
		{
			$usuario = User::where('id',$id)->first();

			$usuario->first_name = trim(Input::get('first_name'));
			$usuario->last_name = trim(Input::get('last_name'));
            $usuario->email=Input::get('email');
            $usuario->phone=Input::get('phone');
            $usuario->group_ids= implode(",",Input::get('groups'));
            $usuario->setPriceType(Input::get('price'));
            if(Input::get('admin')==1)
                $usuario->is_admin = 1;
            else
                $usuario->is_admin = 0;
            if(Input::get('password') == Input::get('password_confirm')){
                if(Input::get('password')!="**********"){
                    $usuario->password =Hash::make(Input::get('password'));
                    $usuario->username = Input::get('username');
                }
            }
            else{
                Session::flash('error','Las contraseñas no coinciden');
                return Redirect::to('usuarios/'.$usuario->id.'/edit');
            }

			$usuario->save();


			foreach (UserBranch::getSucursalesObject($usuario->id) as $sucursal)
			{
				$sucursal->delete();
			}	
			// return "Eliminado las asignarSucursal";
			if(Input::get('branch'))
			{
				foreach (Input::get('branch') as $branch_id)
				{
					$existeAsignado = UserBranch::withTrashed()->where('user_id',$usuario->id)
								 				->where('branch_id',$branch_id)
												->first();
					if($existeAsignado)
					{
						$existeAsignado->restore();
					}
					else
					{
						$branch = Branch::find($branch_id);
						$userbranch= UserBranch::createNew();
						$userbranch->account_id = $usuario->account_id;
						$userbranch->user_id = $usuario->id;
						$userbranch->branch_id = $branch->id;
						$userbranch->save();
					}
				}

			}
			return Redirect::to('usuarios');
		}
		return Redirect::to('/inicio');

	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
		if (Auth::user()->is_admin)
		{

			$usuario = User::find($id);
			if(!$usuario->is_admin)
			{
                                $usuario->username=$usuario->username.$usuario->id;
                                $usuario->save();
				$usuario->delete();
				Session::flash('message','Se borro exitosamente al usuario');
			}
			else
			{
				Session::flash('error','No se puede borrar al administrador');
			}

			return Redirect::to('usuarios');
		}
		return Redirect::to('/inicio');

	}

	// //Datatable para usuario
	// public function getDatatable()
 //    {
 //        return Datatable::collection(User::all(array('id','username')))
 //        ->showColumns('id', 'username')
 //        ->searchColumns('username')
 //        ->orderColumns('id','username')
 //        ->make();
 //    }

	public function asignarSucursal()
	{
		if(Session::has('branch_id'))
		{
			Session::forget('branch_id');
			Session::forget('branch_name');
		}
		// Session::forget('branch_id');
		Session::put('branch_id',Input::get('branch_id'));
		$sucursal= Branch::find(Session::get('branch_id'));
		Session::put('branch_name',$sucursal->name);

		// return Response::json(array('info  ' =>$sucursal));
		return Redirect::to('inicio');
	}
	public function indexSucursal()
	{

		if(Auth::user()->is_admin)
		{
			$branches = Account::find(Auth::user()->account_id)->branches;
			// $sucursales = Branch::find(Auth::user()->account_id);
			$sucursales = array();
			foreach ($branches as $branch) {
				# code...
				$sucursales[] = array('id'=>$branch->id,'text'=>$branch->name);
			}
			return View::make('users.selectBranch')->with('sucursales',$sucursales);
			// return Response::json($sucursales);
		}
		else
		{
			$branches = UserBranch::getSucursales(Auth::user()->id);
			// return Response::json($branches);
			$sucursales = array();
			foreach ($branches as $branch) {
				# code...
				$sucursales[] = array('id'=>$branch->branch_id,'text'=>$branch->name);
			}
		}
		// return Response::json($sucursales);

		return View::make('users.selectBranch')->with('sucursales',$sucursales);
	}
	public function borrar($id)
	{
		return Response::json(array("borrando id" => $id));
	}



}
