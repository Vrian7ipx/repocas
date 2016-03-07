<?php

class ClientController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	// public function index()
	// {
	// 	$clientes = Account::find(Auth::user()->account_id)->clients;
	//   return View::make('clientes.index', array('clients' => $clientes));
	// }

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
                $deptos= explode(',',DEPARTAMENTOS);
                $account = Account::find(Auth::user()->account_id);
                $zones = Zone::orderBy('name','ASC')->get();
                $groups = Group::get();
                $businesses = BusinessType::get();
                $data = [
                    'cuenta'=>$account,
                    'zonas'=>$zones,
                    'deptos'=>$deptos,
                    'grupos'=>$groups,
                    'negocios'=>$businesses,
                ];
                return View::make('clientes.create',$data);
	}

	public function getContacts(){
		$id = Input::get('id');
		//return Response::json($id);
		$contacts = Contact::where('client_id','=', $id )->whereNull('deleted_at')->select('id','first_name','last_name','email','phone')->get();
		$client= Client::where('id',$id)->firstOrFail();
		$enviar=[
		'contact'=>$contacts,
		'note'=>$client->private_notes,
		];
		return Response::json($enviar);
	}
	public function buscar2($cadena="")
	{
		$cadena = Input::get('name');
		$clients = Client::where('account_id','=', Auth::user()->account_id)->where('name','like',$cadena."%")->select('id','name','nit','business_name','public_id')->get();
        $total =0;
        foreach($clients as $key => $value) {
        	$total++;
        }
		if($total==0)
			$clients = Client::where('nit','like',$cadena."%")->where('account_id','=', Auth::user()->account_id)->select('id','name','nit','business_name','public_id')->get();
		return Response::json($clients);
		$newclients = array();
/*
		$nuevo = Client::createNew();
		$nuevo->id ="0";
		$nuevo->name="Nuevo Cliente";

		$newclients[0] = $nuevo;
		$ind = 1;
		foreach ($clients as $client ) {
			# code...
			$newclients[$ind++] = $client;

		}*/
		$newclients[0]=array('name'=> 'Nuevo', 'id'=>'0');//['name'] = $cli['name'];
		$ind =1;
		foreach ($clients as $key => $cli) {
			$newclients[$ind]=array('name'=> $cli->name, 'id'=>$cli->id);//['name'] = $cli['name'];
			//$newclients[$ind]['id'] = $cli['id'];
			$ind++;
		}
		//$newclients[$ind]['name'] = "nuevo";
		//$newclients[$ind]['id'] = "new";
		// $clients = Client::where('name','like',$cadena."%")->select('id','account_id','name')->get();
		//array_unshift($clients,array('id':'1','name':'dadsf','account_id':'1'));

		 // $myarray['blah'] = json_decode('[
   //      {"label":"foo","name":"baz"},
   //      {"label":"boop","name":"beep"}
   //  ]',true);
    	return Response::json($newclients);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{

		//print_r(Input::all());return 0;
		$client = Client::createNew();
		$client->setNit(trim(Input::get('nit')));
		$client->setName(trim(Input::get('name')));
		$client->setBussinesName(trim(Input::get('business_name')));
    $client->setWorkPhone(trim(Input::get('work_phone')));
		$client->setCustomValue1(trim(Input::get('l1')));
		$client->setCustomValue2(trim(Input::get('l2')));
		$client->setCustomValue3(trim(Input::get('l3')));
		$client->setCustomValue4(trim(Input::get('l4')));
		$client->setCustomValue5(trim(Input::get('l5')));
		$client->setCustomValue6(trim(Input::get('l6')));
		$client->setAddress1(trim(Input::get('address1')));
		$client->setAddress2(trim(Input::get('address2')));
		$client->setPrivateNotes(trim(Input::get('private_notes')));

		 $client->setZone(trim(Input::get('zone')));
		 $client->other=trim(Input::get('other'));
		$client->setCity(trim(Input::get('city')));
		$client->setGroup(trim(Input::get('group')));
		$client->setBusiness_type_id(trim(Input::get('business')));

                // $client->city=Input::get('city');
                // $client->group_id = Input::get('group');
                // $client->business_type_id = Input::get('business');
                $dias="";
                for($i=1;$i<8;$i++){
                    if(Input::get('d'.$i))
                        $dias.="1";
                    else
                        $dias.="0";
                }
                //frecuency es handed with 0 and 1
                $client->frecuency=$dias;

		$resultado = $client->guardar();

                if(Input::get('json')=="1")
                {
                    $client->save();
                    return json_encode(0);
                }
		if(!$resultado){
			$message = "Cliente con Nit ".$client->nit."&nbsp; y Razón Social &nbsp;".$client->business_name." &nbsp; creado con éxito";
			$client->save();
		}
		else
		{
			$url = 'clientes/create';
			Session::flash('error',	$resultado);
                    return Redirect::to($url)
                    ->withInput();
		}

		$isPrimary = true;


		$contactos = Utils::parseContactos(Input::get('contactos'));
		if($contactos)
		{
			foreach ($contactos as $contacto) {
			# code...
			// $contador++;
				$contact_new = Contact::createNew();
				$contact_new->client_id=$client->getId();

				$contact_new->setFirstName(trim($contacto['first_name']));
				$contact_new->setLastName(trim($contacto['last_name']));
				$contact_new->setEmail(trim(strtolower($contacto['email'])));
				$contact_new->setPhone(trim(strtolower($contacto['phone'])));
				$contact_new->setIsPrimary($isPrimary);
				$isPrimary = false;

				$resultado = $contact_new->guardar();
				//print_r($resultado);
				$client->contacts()->save($contact_new);
			}
		}


		Session::flash('message',	$message);
		return Redirect::to('clientes');
	}



	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$client = Client::where('id',$id)->withTrashed()->with('contacts')->first();

		if($client)
		{


		//$client = Client::scope($publicId)->with('contacts')->firstOrFail();
		$getTotalCredit = Credit::where('client_id', '=', $client->id)->whereNull('deleted_at')->where('balance', '>', 0)->sum('balance');

		$invoices = Invoice::join('invoice_statuses', 'invoice_statuses.id', '=','invoices.invoice_status_id')
							//->where('invoices.account_id',Auth::user()->account_id)
							->where('invoices.client_id',$client->id)
							->where('invoices.branch_id',Session::get('branch_id'))
							->select('invoices.invoice_number','invoices.invoice_date','invoices.importe_total','invoices.balance','invoices.due_date','invoice_statuses.name','invoices.public_id')->get();
		$pagos = Payment::join('invoices', 'invoices.id', '=','payments.invoice_id')
							 ->join('invoice_statuses','invoice_statuses.id','=','payments.payment_type_id')
							  //->where('payments.account_id',Auth::user()->account_id)
							  ->where('payments.client_id',$client->id)
							  ->select('invoices.invoice_number','payments.transaction_reference','invoice_statuses.name','payments.amount','payments.payment_date')
							  ->get();
                $creditos = Credit::where('account_id','=',Auth::user()->account_id)->where('client_id','=',$client->getId())->get();
                $group = Group::where('id',$client->group_id)->first();
                $business = BusinessType::where('id',$client->business_type_id)->first();
		$data = array(
			'title' => 'Ver Cliente',
			'client' => $client,
			'invoices' => $invoices,
			'pagos' => $pagos,
			'credit' => $getTotalCredit,
                        'creditos'=>$creditos,
                        'days'=>$this->getDays($client->frecuency),
                        'grupo'=>$group->name,
                        'negocio'=>$business->name,
		);
		// return Response::json($data);

			return View::make('clientes.show', $data);
		}

		Session::flash('error', 'No existe el usuario');
		return Redirect::to('clientes');

	}
        private function getDays($dias){
            $days = array();
            //echo $dias;
            if($dias[0]=='1') array_push ($days,"Lunes");
            if($dias[1]=='1') array_push ($days,"Martes");
            if($dias[2]=='1') array_push ($days,"Miércoles");
            if($dias[3]=='1') array_push ($days,"Jueves");
            if($dias[4]=='1') array_push ($days,"Viernes");
            if($dias[5]=='1') array_push ($days,"Sábado");
            if($dias[6]=='1') array_push ($days,"Domingo");
            return $days;
        }


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{

		$client = Client::where('id',$id)->withTrashed()->with('contacts')->first();

                $deptos= explode(',',DEPARTAMENTOS);
                $zones = Zone::orderBy('name','ASC')->get();
                $groups = Group::get();
                $businesses = BusinessType::get();
                $dias = array();
                $fec = str_split($client->frecuency);
                foreach ($fec as $d)
                    array_push ($dias, $d=="1"?true:false);

		if($client)
		{
			if($client->deleted_at!=null)
			{

				$client->restore();
			}


			$contacts = $client->contacts;
			$contactos =array();
			foreach ($contacts as $contact) {

				# code...
				$contactos [] = array('id'=>$contact->id,'nombres'=> $contact->first_name,'apellidos' => $contact->last_name,'email'=> $contact->email,'phone'=>$contact->phone);
	//
			}
			$data = [
				'client' => $client,
				'contactos' => $contactos,
				'url' => 'clientes/' . $id,
				'title' => 'Editar Cliente',
                                'zonas'=>$zones,
                                'deptos'=>$deptos,
                                'grupos'=>$groups,
                                'negocios'=>$businesses,
                                'd'  => $dias,
			];
			$account = Account::find(Auth::user()->account_id);
			//data = array_merge($data, self::getViewModel());
	                $data = array_merge($data, array('cuenta'=>$account));

			// return Response::json($data);
			return View::make('clientes.edit', $data);
		}
		Session::flash('error', 'No existe el usuario');
		return Redirect::to('clientes');
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		// return Response::json(Input::all());

		// return Response::json($contactos);
		$client = Client::where('id',$id)->first();
		$client->setNit(trim(Input::get('nit')));
		$client->setName(trim(Input::get('name')));
		$client->setBussinesName(trim(Input::get('business_name')));
                $client->setWorkPhone(trim(Input::get('work_phone')));
		$client->setCustomValue1(trim(Input::get('l1')));
		$client->setCustomValue2(trim(Input::get('l2')));
		$client->setCustomValue3(trim(Input::get('l3')));
		$client->setCustomValue4(trim(Input::get('l4')));
		$client->setCustomValue5(trim(Input::get('l5')));
		$client->setCustomValue6(trim(Input::get('l6')));
//		$client->setCustomValue7(trim(Input::get('custom_value7')));
//		$client->setCustomValue8(trim(Input::get('custom_value8')));
//		$client->setCustomValue9(trim(Input::get('custom_value9')));
//		$client->setCustomValue10(trim(Input::get('custom_value10')));
//		$client->setCustomValue11(trim(Input::get('custom_value11')));
//		$client->setCustomValue12(trim(Input::get('custom_value12')));

		$client->setAddress1(trim(Input::get('address1')));
		$client->setAddress2(trim(Input::get('address2')));
		$client->setPrivateNotes(trim(Input::get('private_notes')));
                //improve this put method
                $client->other=trim(Input::get('other'));
                $client->setZone(trim(Input::get('zone')));
                $client->setCity(trim(Input::get('city')));
                $client->setGroup(trim(Input::get('group')));
                $client->setBusiness_type_id(trim(Input::get('business')));
                $dias="";
                for($i=1;$i<8;$i++){
                    if(Input::get('d'.$i))
                        $dias.="1";
                    else
                        $dias.="0";
                }
                //frecuency es handed with 0 and 1
                $client->frecuency=$dias;
         // return Response::json($client);
		$resultado = $client->guardar();
		$client->frecuency=$dias;
		$client->save();
		if(!$resultado){
			$message = "Cliente actualizado con éxito";
			$client->save();
		}
		else
		{
			$url = 'clientes/create';
			Session::flash('error',	$resultado);
	        return Redirect::to($url)
	          ->withInput();
		}


		if(Input::has('contactos'))
		{
			// Actualizando contactos si existe


			$contactos = Utils::parseContactosUpdate(Input::get('contactos'));
			$contactosBorrar= array();

		 	foreach ($contactos as $contacto) {

		 		if(!empty($contacto['id']))
		 		{
		 			$contactosBorrar[]= $contacto['id'];
		 		}

		 	}

			foreach ($client->contacts as $contact)
			{
				$sw =true;

				foreach ($contactos as $contacto) {
					# code...

					if(!empty($contacto['id']))
					{
						if($contact->id==$contacto['id'])
						{
							$contact->setFirstName(trim($contacto['first_name']));
							$contact->setLastName(trim($contacto['last_name']));
							$contact->setEmail(trim(strtolower($contacto['email'])));
							$contact->setPhone(trim(strtolower($contacto['phone'])));
							$contact->save();
						}

					}

				}

			}

			$primario = true;
			foreach ($contactos as $contacto) {
				# code...
				if(empty($contacto['id']))
				{
					$contact_new = Contact::createNew();
					$contact_new->client_id=$client->getId();

					$contact_new->setFirstName(trim($contacto['first_name']));
					$contact_new->setLastName(trim($contacto['last_name']));
					$contact_new->setEmail(trim(strtolower($contacto['email'])));
					$contact_new->setPhone(trim(strtolower($contacto['phone'])));
					$contact_new->setIsPrimary($primario);
					$primario = false;

					$resultado = $contact_new->guardar();
					//print_r($resultado);
					$client->contacts()->save($contact_new);
				}
			}

			foreach ($client->contacts as $contact) {
				# code...
				if(!in_array($contact->id, $contactosBorrar))
				{
					$contact->delete();
				}
			}
		}
		else
		{
			if($client->contacts)
			{
				foreach ($client->contacts as $contacto) {

					$contacto->delete();
					# code...
				}
			}


		}



		// return Response::json($contactosBorrar);
		Session::flash('message', $message);

		return Redirect::to('clientes/' . $client->getPublicId());
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($public_id)
	{

		// $client = Client::scope($public_id)->firstOrFail();
		$client = Client::where('account_id',Account::find(Auth::user()->account_id))->where('public_id',$public_id)->first();

		if($client->borrar()){

			Session::flash('message', $client->getErrorMessage());
			// return $client->getErrorMessage();
			return Redirect::to('clientes');
		}



		Session::flash('error', $client->getErrorMessage());
		// return var_dump($client);
		return Redirect::to('clientes/'.$public_id);

		// return Response::json(array('XD'=>'Ooooo'));

		// $getTotalBalance = $client->balance;
		// $getTotalCredit = Credit::scope()->where('client_id', '=', $client->id)->whereNull('deleted_at')->where('balance', '>', 0)->sum('balance');

		// if ($getTotalBalance > 0) {
		// 	$message = "El cliente " . $client->name . " tiene " . $getTotalBalance . " pendiente de pago.";
		// 	Session::flash('error', $message);
		// 	return Redirect::to('clientes');
		// }else if ($getTotalCredit > 0) {
		// 	$message = "El cliente " . $client->name . " tiene " . $getTotalCredit . " de Crédito disponible.";
		// 	Session::flash('error', $message);
		// 	return Redirect::to('clientes');
		// }else{
		// 	return Response::json($client);
		// 	$client->delete();
		// 	$message = "Cliente eliminado con éxito";
		//
		// 	return Redirect::to('clientes');
		// }

	}

	public function cliente($public_id)
    {
     	// $user_id = Auth::user()->getAuthIdentifier();
    	// $user = DB::table('users')->select('account_id')->where('id',$user_id)->first();
    	$client =  DB::table('clients')->select('id','name','nit','public_id')->where('account_id',Auth::user()->account_id)->where('public_id',$public_id)->first();

    	if($client!=null)
    	{

    		$datos = array(
    			'resultado' => 0,
    			'cliente' => $client

    		);
    		return Response::json($datos);
    	}
    	$datos = array(
    			'resultado' => 1,
    			'mensaje' => 'cliente no encontrado'

    		);
    		return Response::json($datos);

    }


		//add R

		public function index($name = null, $numero = null, $nit = null, $group = null, $contact = null)
	  {

	   $name = Input::get('name');
	   $numero = Input::get('numero');
	   $nit = Input::get('nit');
	   $group = Input::get('group');
	   $contact = Input::get('contact');

	  //  die("index");

	  Session::put('sw','DESC');

	   if(!$numero && !$name && !$nit && !$group && !$contact)
	   {
	    $clientes= Client::where('account_id', Auth::user()->account_id)
	    ->select('id', 'business_name', 'nit', 'created_at', 'group_id')
			->orderBy('id', 'DESC')->simplePaginate(30);

			foreach ($clientes as $key => $client) {
				$contacts = Contact::where('account_id', Auth::user()->account_id)
				->select('first_name', 'last_name')->where('client_id', $client->id)->first();

				$client->contacto_first_name = $contacts->first_name;
				$client->contacto_last_name = $contacts->last_name;
			}
	    return View::make('clientes.index', array('clients' => $clientes,'sw'=>'ASC', 'contacts' => $contacts));
	   }
	   if ($numero) {

	     $clientes = Client::where('account_id', Auth::user()->account_id)
	    ->select('id', 'business_name', 'nit', 'created_at', 'group_id')
	    ->where('id', 'like', $numero."%")
	    ->orderBy('id', $sw)
	    ->simplePaginate(30);

			foreach ($clientes as $key => $client) {
				$contacts = Contact::where('account_id', Auth::user()->account_id)
				->select('first_name', 'last_name')
				->where('client_id', $client->id)
				->first();
				$client->contacto_first_name = $contacts->first_name;
				$client->contacto_last_name = $contacts->last_name;
			}

	    $data = [
	      'clients' => $clientes,
	      'numero' => $numero,
	      'name' => $name,
	      'nit' => $nit,
	      'create' => $create
	    ];
	    return View::make('clientes.index', $data);
	  }

	   if ($name) {

	     $clientes = Client::where('account_id', Auth::user()->account_id)
	    ->select('id', 'business_name', 'nit', 'created_at', 'group_id')
	    ->where('business_name', 'like', $name."%")
	    ->orderBy('business_name', $sw)
	    ->simplePaginate(30);
			foreach ($clientes as $key => $client) {
				$contacts = Contact::where('account_id', Auth::user()->account_id)
				->select('first_name', 'last_name')
				->where('client_id', $client->id)
				->first();
				$client->contacto_first_name = $contacts->first_name;
				$client->contacto_last_name = $contacts->last_name;
			}

	    $data = [
	      'clients' => $clientes,
	      'numero' => $numero,
	      'name' => $name,
	      'nit' => $nit,
	      'create' => $create
	    ];
	    return View::make('clientes.index', $data);
	    }

	    if ($nit) {
				$clientes = Client::where('account_id', Auth::user()->account_id)
			 ->select('id', 'business_name', 'nit', 'created_at', 'group_id')
			 ->where('nit', 'like', $nit."%")
			 ->orderBy('nit', $sw)
			 ->simplePaginate(30);
			 foreach ($clientes as $key => $client) {
				 $contacts = Contact::where('account_id', Auth::user()->account_id)
				 ->select('first_name', 'last_name')
				 ->where('client_id', $client->id)
				 ->first();
				 $client->contacto_first_name = $contacts->first_name;
				 $client->contacto_last_name = $contacts->last_name;
			 }

			 $data = [
				 'clients' => $clientes,
				 'numero' => $numero,
				 'name' => $name,
				 'nit' => $nit,
				 'create' => $create
			 ];
			 return View::make('clientes.index', $data);
	    }

	    // if ($group) {
			//
			//
			// 	$clientes = Client::where('account_id', Auth::user()->account_id)
			// 	->join('groups', 'clients.group_id', '=', 'groups.id')
			//  ->select('clients.id', 'clients.business_name', 'clients.nit', 'clients.created_at', 'clients.group_id')
			//  ->where('groups.name', 'like', $group."%")
			//  ->orderBy('groups.name', $sw)
			//  ->simplePaginate(30);
			// 		 foreach ($clientes as $key => $client) {
			// 			 $contacts = Contact::where('account_id', Auth::user()->account_id)
			// 			 ->select('first_name', 'last_name')
			// 			 ->where('client_id', $client->id)
			// 			 ->first();
			// 			 $client->contacto_first_name = $contacts->first_name;
			// 			 $client->contacto_last_name = $contacts->last_name;
			// 		 }
			//
			//  $data = [
			// 	 'clients' => $clientes,
			// 	 'numero' => $numero,
			// 	 'name' => $name,
			// 	 'nit' => $nit,
			// 	 'create' => $create
			//  ];
			//  return View::make('clientes.index', $data);
	    // }

			if ($group){
				$clientes = Client::join('groups', 'clients.group_id', '=', 'groups.id')
										->select('clients.id', 'clients.nit', 'clients.business_name', 'clients.created_at','clients.group_id')
										->where('groups.name', 'like', $group.'%')
										->orderBy('groups.name', $sw)
										->simplePaginate(30);
										foreach ($clientes as $key => $client) {
											$contacts = Contact::where('account_id', Auth::user()->account_id)
											->select('first_name', 'last_name')
											->where('client_id', $client->id)
											->first();
											$client->contacto_first_name = $contacts->first_name;
											$client->contacto_last_name = $contacts->last_name;
										}
						$data = [
							'clients' => $clientes,
							'group' => $group
						];
					return View::make('clientes.index', $data);
			}

	    if ($contact) {

				$clientes= Client::join('contacts', 'clients.id', '=' , 'contacts.client_id')
				->select('clients.id', 'clients.nit', 'clients.business_name', 'clients.created_at', 'clients.group_id', 'contacts.first_name', 'contacts.last_name')
				->where('contacts.first_name', 'like', $contact."%")
				->orderBy('contacts.first_name', $sw)
				->simplePaginate(30);

				foreach ($clientes as $key => $client) {
					$client->contacto_first_name = $client->first_name;
					$client->contacto_last_name = $client->last_name;
				}
				$data = [
					'clients' => $clientes,
					'numero' => $numero,
					'name' => $name,
					'nit' => $nit,
					'create' => $create,
					'contact' => $contact
				];
				return View::make('clientes.index', $data);
	    }
	 }

	   public function indexDown($name = null, $numero = null, $nit = null, $group = null, $contact = null)
	   {


			 $name = Input::get('name');
	     $numero = Input::get('numero');
	     $nit = Input::get('nit');
	     $group = Input::get('group');
	     $contact = Input::get('contact');


	    if(Session::get('sw')=='DESC')
	    {
	      Session::put('sw','ASC');
	    }
	    else {
	      Session::put('sw','DESC');
	    }

	    $sw = Session::get('sw');
	    if($numero == "" && $name == "" && $nit == "" && $group == "" && $contact == "")
	    {

			 $clientes= Client::where('account_id', Auth::user()->account_id)
	     ->select('id', 'business_name', 'nit', 'created_at', 'group_id')->orderBy('id', $sw)->simplePaginate(30);

		 		foreach ($clientes as $key => $client) {
		 			$contacts = Contact::where('account_id', Auth::user()->account_id)
		 			->select('first_name', 'last_name')->where('client_id', $client->id)->first();

		 			$client->contacto_first_name = $contacts->first_name;
		 			$client->contacto_last_name = $contacts->last_name;
	 			}
	     return View::make('clientes.index', array('clients' => $clientes,'sw'=>'ASC', 'contacts' => $contacts));
	    }

			if ($numero) {
	      $clientes = Client::where('account_id', Auth::user()->account_id)
	     ->select('id', 'business_name', 'nit', 'created_at', 'group_id')
	     ->where('id', 'like', $numero."%")
	     ->orderBy('id', $sw)
	     ->simplePaginate(30);

	 		foreach ($clientes as $key => $client) {
	 			$contacts = Contact::where('account_id', Auth::user()->account_id)
	 			->select('first_name', 'last_name')
	 			->where('client_id', $client->id)
	 			->first();
	 			$client->contacto_first_name = $contacts->first_name;
	 			$client->contacto_last_name = $contacts->last_name;
	 		}

	     $data = [
	       'clients' => $clientes,
	       'numero' => $numero,
	       'name' => $name,
	       'nit' => $nit,
	       'create' => $create
	     ];
	     return View::make('clientes.index', $data);
	   }

	    if ($name) {

	      $clientes = Client::where('account_id', Auth::user()->account_id)
	     ->select('id', 'business_name', 'nit', 'created_at', 'group_id')
	     ->where('business_name', 'like', $name."%")
	     ->orderBy('business_name', $sw)
	     ->simplePaginate(30);

			//  $clientes = Client::join('contacts', 'clients.id' ,'=', 'contacts.client_id')
			//  ->where('business_name', 'like', $name."%")
			//  ->select('clients.id', 'clients.business_name', 'clients.nit', 'clients.created_at', 'contacts.first_name', 'contacts.last_name')
			//  ->orderBy('business_name', $sw)
			//  ->simplePaginate(30);
	 		foreach ($clientes as $key => $client) {
				$contacts = Contact::where('account_id', Auth::user()->account_id)
				->select('first_name', 'last_name')
				->where('client_id', $client->id)
				->first();
	 			$client->contacto_first_name = $contacts->first_name;
	 			$client->contacto_last_name = $contacts->last_name;
	 		}
	// return $clientes;
	     $data = [
	       'clients' => $clientes,
	       'numero' => $numero,
	       'name' => $name,
	       'nit' => $nit,
	       'create' => $create
	     ];
	     return View::make('clientes.index', $data);
	     }

	     if ($nit) {
	 			$clientes = Client::where('account_id', Auth::user()->account_id)
	 		 ->select('id', 'business_name', 'nit', 'created_at', 'group_id')
	 		 ->where('nit', 'like', $nit."%")
	 		 ->orderBy('nit', $sw)
	 		 ->simplePaginate(30);
	 		 foreach ($clientes as $key => $client) {
	 			 $contacts = Contact::where('account_id', Auth::user()->account_id)
	 			 ->select('first_name', 'last_name')
	 			 ->where('client_id', $client->id)
	 			 ->first();
	 			 $client->contacto_first_name = $contacts->first_name;
	 			 $client->contacto_last_name = $contacts->last_name;
	 		 }

	 		 $data = [
	 			 'clients' => $clientes,
	 			 'numero' => $numero,
	 			 'name' => $name,
	 			 'nit' => $nit,
	 			 'create' => $create
	 		 ];
	 		 return View::make('clientes.index', $data);
	     }

	    //  if ($create) {
			 //
	 	// 		$clientes = Client::where('account_id', Auth::user()->account_id)
	 	// 	 ->select('id', 'business_name', 'nit', 'created_at', 'group_id')
	 	// 	 ->where('created_at', 'like', $create."%")
	 	// 	 ->orderBy('created_at', $sw)
	 	// 	 ->simplePaginate(30);
	 	// 			 foreach ($clientes as $key => $client) {
	 	// 				 $contacts = Contact::where('account_id', Auth::user()->account_id)
	 	// 				 ->select('first_name', 'last_name')
	 	// 				 ->where('client_id', $client->id)
	 	// 				 ->first();
	 	// 				 $client->contacto_first_name = $contacts->first_name;
	 	// 				 $client->contacto_last_name = $contacts->last_name;
	 	// 			 }
			 //
	 	// 	 $data = [
	 	// 		 'clients' => $clientes,
	 	// 		 'numero' => $numero,
	 	// 		 'name' => $name,
	 	// 		 'nit' => $nit,
	 	// 		 'create' => $create
	 	// 	 ];
	 	// 	 return View::make('clientes.index', $data);
	    //  }

			if ($group){
				$clientes = Client::join('groups', 'clients.group_id', '=', 'groups.id')
										->select('clients.id', 'clients.nit', 'clients.business_name', 'clients.created_at','clients.group_id')
										->where('groups.name', 'like', $group.'%')
										->orderBy('groups.name', $sw)
										->simplePaginate(30);
										foreach ($clientes as $key => $client) {
											$contacts = Contact::where('account_id', Auth::user()->account_id)
											->select('first_name', 'last_name')
											->where('client_id', $client->id)
											->first();
											$client->contacto_first_name = $contacts->first_name;
											$client->contacto_last_name = $contacts->last_name;
										}
						$data = [
							'clients' => $clientes,
							'group' => $group
						];
					return View::make('clientes.index', $data);
			}

			if ($contact) {

				$clientes= Client::join('contacts', 'clients.id', '=' , 'contacts.client_id')
				->select('clients.id', 'clients.nit', 'clients.business_name', 'clients.created_at', 'clients.group_id', 'contacts.first_name', 'contacts.last_name')
				->where('contacts.first_name', 'like', $contact."%")
				->orderBy('contacts.first_name', $sw)
				->simplePaginate(30);

				foreach ($clientes as $key => $client) {
					$client->contacto_first_name = $client->first_name;
					$client->contacto_last_name = $client->last_name;
				}
				$data = [
					'clients' => $clientes,
					'contact' => $contact
				];
				return View::make('clientes.index', $data);
	    }
	  }
}
