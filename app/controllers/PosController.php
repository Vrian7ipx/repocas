<?php

class PosController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
		// $user_id = Auth::user()->getAuthIdentifier();
    	$user = DB::table('users')->select('account_id','price_type_id','branch_id','id','group_ids')->where('id',Auth::user()->id)->first();
   
    	
    	$products = DB::table('products')
    							->join('prices',"product_id","=",'products.id')
    							->select('products.id','products.product_key','products.notes','prices.cost','products.ice','products.units','products.cc')
    						    ->where('products.account_id','=',$user->account_id)
    							->where('prices.price_type_id','=',$user->price_type_id)
    							->get(array('products.id','product_key','notes','cost','ice','units','cc'));

    	$user_branch= UserBranch::where('user_id',Auth::user()->id)->first();
    	// return Response::json($user_branch);

    	$sucursal = DB::table('branches')
    						->select('name','address1','address2','number_autho','deadline','key_dosage','economic_activity','invoice_number_counter','law','type_third')
    						->where('id','=',$user_branch->branch_id)
    						->first();
    	$casamatriz= DB::table('branches')
    						->select('name','address1','address2','number_autho','deadline','key_dosage','economic_activity','invoice_number_counter','law','type_third')
    						->where('id','=','2')
    						->first();
        if($sucursal->terceros==1){
            $var = $casamatriz->address1;					
            $sucursal->address1 = $var;
            $var = $casamatriz->address2;
            $sucursal->address2 = $var;
        }
//    	$ice = DB::table('tax_rates')->select('rate')// ->where('account_id','=',$user->account_id)
//    		->where('name','=','ice')
//        ->first();
        $ice = DB::table('tax_rates')->where('id',1)->first();


    	$mensaje = array(
    			// 'clientes' => $array,//esta lista estara dentro del menu principal
    			'sucursal'=> $sucursal,
    			'productos' => $products,
    			'ice'=>$ice->rate
    		);
    	return Response::json($mensaje);	
    	// return Response::json($mensaje);
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
		//guardando facturas XD 
		//$user = User::fin(-1)->firstOrFail();
		// return $this->newSave();
		//return $this->saveBackup();

		return $this->saveOfflineInvoices();
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
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
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
	}
	public function guardarCliente()
	{
		$client = Client::createNew();
		$client->setNit(trim(Input::get('nit')));
		$client->setName(trim(Input::get('name')));
		$client->setBussinesName(trim(Input::get('name')));
		$client->save();
		//$client = Client::where()->first();
		$client2 = Client::where('id',$client->id)->first();
		$clientPOS = array(
				'id'=>$client2->id,
				'public_id'=>$client2->public_id,
				'name'=>$client2->name,
				'nit'=>$client2->nit,
				'business_name'=>$client2->business_name
				);

				$datos = array(
    			'resultado' => 0,
    			'cliente' => $clientPOS
				);
    			return Response::json($datos);	


	}
	public function cliente($nit)
    {
     	// $cuenta = Account::find(Auth::user()->account_id);
    	// $client =  DB::table('clients')->select('id','public_id','name','nit')->where('account_id',$user->account_id)->where('nit',$public_id)->first();
    	$client = Client::where('account_id',Auth::user()->account_id)->where('nit',$nit)->first();
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
    public function newSave(){
    	//return Response::json(0);

    	$conectado=true;
    	$resultado = 0;
    	//$this->saveBackUpToMirror();
    	//return 0;    	

    	//$input = Input::all();

    	$respuesta = array();
    	// $input =  $this->setDataOffline();
    	$input = Input::all();
    	//return $input;
    	$PosBackup = new Backup();
        $PosBackup->user_id=Auth::user()->id;
        $PosBackup->json=json_encode($input);
        $PosBackup->save();
    	 return Response::view('errors.missing', array(), 404);
    	// return $input;
    }

     public function saveOfflineInvoices(){
    	//return Response::json(0);

    	$conectado=true;
    	$resultado = 0;
    	//$this->saveBackUpToMirror();
    	//return 0;    	

    	//$input = Input::all();

    	$respuesta = array();
    	// $input =  $this->setDataOffline();
    	$input = Input::all(); 
    	//$input=iconv(mb_detect_encoding($input, mb_detect_order(), true), "UTF-8", $input);
		//$utf8Str = iconv('ISO-8859-1', 'UTF-8', $input);
    	//return $input;
    // 	DB::table('pos_backups')->insert(
    //     array('user_id' => Auth::user()->id, 'json' => json_encode($input),'created_at'=>date())
    // );

    	$PosBackup = new Backup();
        $PosBackup->user_id=Auth::user()->id;
        $PosBackup->json=json_encode($input);
        $PosBackup->save();
    	
    	//return $input;
    	// $ingresa = json_encode($ingresa);
    	// $input = json_decode($ingresa);

    	//return Response::json(Input::all());
    	//$input = json_decode($input);
    	// return Response::json($input);

    	$backup = array();
    	$cantidad =  0;

    	if($conectado){

	    	foreach ($input as $key => $factura) {    		
				array_push($backup, $this->completeFields($factura));   
				//return ;				
	    		array_push($respuesta, $factura['invoice_number']);  		
	    		$cantidad++;
	    	}

	    	$input = $this->saveBackUpToMirror($backup);	    	
	    	//return $input;
//print_r($input);	    	return ;	 
			$input = json_decode($input);			
			

	    	foreach ($input as $key => $factura) {    				
	    		$this->saveOfflineInvoice($factura);
	    	}
	    	//return ;
    	}
    	else{

    		if(!$this->verificar($input))
    			$resultado=1;    		
    		else
		    	foreach ($input as $key => $factura) {   
		    		$factura['client_id']= $factura['client_id']/10; 				
		    		$this->saveOfflineInvoice($factura);	    		    		
		    	}
    	}
    	

    	$datos = array('resultado' => $resultado.'','respuesta'=>$cantidad.'');
    	//hereBithc();
    	//print_r($datos);
		return Response::json($datos);
    }

    private function saveOfflineInvoice($factura)
    {
    	   // return Response::json($factura);
		$input = $factura;
		//print_r($input->invoice_number);
		//return ;
		// $invoice_number = Auth::user()->account->getNextInvoiceNumber();
		//$invoice_number = (int)Auth::user()->branch->getNextInvoiceNumber();

		//$numero =(int) $input['invoice_number'];
		$numero =(int) $input->invoice_number;


		$user_branch= UserBranch::where('user_id',Auth::user()->id)->first();

  		$sucursal = Branch::find($user_branch->branch_id);
        //$sucursal->invoice_number_counter=  $input['invoice_number']+1;
        $sucursal->invoice_number_counter=  $input->invoice_number+1;
        $sucursal->save();



		$invoice_number = $numero;

		// $numero =(int)  $input['invoice_number'];

		// if($invoice_number!=$numero)
		// {			
		// 	return Response::json( array('resultado' => '1' ,'invoice_number'=>$invoice_number));

		// }
		//$client_id = $input['client_id'];
		$client_id = $input->client_id;
		// $client = DB::table('clients')->select('id','nit','name','public_id')->where('id',$input['client_id'])->first();

		// $user_id = Auth::user()->getAuthIdentifier();

		$user  = DB::table('users')->select('account_id','price_type_id')->where('id',Auth::user()->id)->first();
		
		//$account_id = $user->account_id;
		// $account = DB::table('accounts')->select('num_auto','llave_dosi','fecha_limite')->where('id',$user->account_id)->first();
		//$branch = DB::table('branches')->select('num_auto','llave_dosi'	,'fecha_limite','address1','address2','country_id','industry_id')->where('id',$user['branch_id'])->first();
		//$branch = DB::table('branches')->select('num_auto','llave_dosi','fecha_limite','address1','address2','country_id','industry_id')->where('id','=',$user->branch_id)->first();	
    	// $branch = DB::table('branches')->select('number_autho','key_dosage','deadline','address1','address2','country_id','industry_id','law','activity_pri','activity_sec1','name')->where('id','=',$user->branch_id)->first();	
    	$branch = DB::table('branches')->where('id','=',$user_branch->branch_id)->first();	
    	//$items = $input['invoice_items'];
    	$items = $input->invoice_items;
    	// echo "this is initializioinh";
    	// print_r($input->invoice_items); 
		// return 0;
    	
    	// $linea ="";
    	$amount = 0;
    	$subtotal=0;
    	$fiscal=0;
    	$icetotal=0;
    	$bonidesc =0;
    	foreach ($items as $item) 
    	{
    		# code...
    		//$product_id = $item['id'];
    		$product_id = $item->id;
    		 
    		$pr = DB::table('products')
    							->join('prices',"product_id","=",'products.id')
    					
    							->select('products.id','products.notes','prices.cost','products.ice','products.units','products.cc')
    						    ->where('prices.price_type_id','=',$user->price_type_id)
    						    ->where('products.account_id','=',$user->account_id)
    						    ->where('products.id',"=",$product_id)
    							->first();

    		// $pr = DB::table('products')->select('cost')->where('id',$product_id)->first();
    		
    		//$qty = (int) $item['qty'];
    		$qty = (int) $item->qty;
    		$cost = $pr->cost/$pr->units;
    		$st = ($cost * $qty);
    		$subtotal = $subtotal + $st; 
    		//$bd= ($item['boni']*$cost) + $item['desc'];
    		$bd= ($item->boni*$cost) + $item->desc;
    		$bonidesc= $bonidesc +$bd;
    		$amount = $amount +$st-$bd;

    		$ice = DB::table('tax_rates')->select('rate')->where('name','=','ICE')->first();
    		if($pr->ice == 1)
    		{
    			
    			//caluclo ice bruto 
    			$iceBruto = ($qty *($pr->cc/1000)*$ice->rate);
    			//$iceNeto = (((int)$item['boni']) *($pr->cc/1000)*$ice->rate);
    			$iceNeto = (((int)$item->boni) *($pr->cc/1000)*$ice->rate);
    			$icetotal = $icetotal +($iceBruto-$iceNeto) ;
    			// $fiscal = $fiscal + ($amount - ($item['qty'] *($pr->cc/1000)*$ice->rate) );
    		}
    		
    		

    			

    	}
    	//$fiscal = $amount -$bonidesc-$icetotal;
        $fiscal = $subtotal -$bonidesc-$icetotal;

    	$balance= $amount;
    	/////////////////////////hasta qui esta bien al parecer hacer prueba de que fuciona el join de los productos XD
    	$invoice_dateCC = date("Ymd");
    	//$invoice_date = date("Y/m/d");
    	$invoice_date =date("Y-m-d", strtotime($input->fecha));
    	// date("Y-m-d", strtotime($fecha))
    
		$invoice_date_limitCC = date("Y/m/d", strtotime($branch->deadline));

		// require_once(app_path().'/includes/control_code.php');	
		// $cod_control = codigoControl($invoice_number, $input['nit'], $invoice_dateCC, $amount, $branch->number_autho, $branch->key_dosage);
	     //$cod_control = $input['cod_control'];
	     $cod_control = $input->cod_control;
	     $ice = DB::table('tax_rates')->select('rate')->where('name','=','ice')->first();
	     //
	     // creando invoice
	     $cuenta = Account::find(1);
	     $invoice = Invoice::createNew();
	     $invoice->invoice_number=$invoice_number;
	     $invoice->client_id=$client_id;
	     $invoice->user_id=Auth::user()->id;
	     $invoice->account_id = Auth::user()->account_id;
	     $invoice->branch_id= $user_branch->branch_id;
	     $invoice->importe_neto =number_format((float)$amount, 2, '.', '');	
	     $invoice->importe_total = $subtotal;
	     $invoice->debito_fiscal =$fiscal;
	     $invoice->law = $branch->law;
	     $invoice->balance=$balance;
	     $invoice->control_code=$cod_control;
	     $invoice->start_date =$invoice_date;
	     $invoice->invoice_date=$invoice_date;

		 $invoice->economic_activity=$branch->economic_activity;
	     // $invoice->activity_sec1=$branch->activity_sec1;
	     
	     // $invoice->invoice
	     $invoice->end_date=$invoice_date_limitCC;
	     //datos de la empresa atra vez de una consulta XD
	     /*****************error generado al intentar guardar **/
	   	 $invoice->branch_name = $branch->name;
	     $invoice->address1=$branch->address1;
	     $invoice->address2=$branch->address2;
	     $invoice->number_autho=$branch->number_autho;
	     $invoice->phone=$branch->work_phone;
			$invoice->city=$branch->city;
			$invoice->state=$branch->state;
		 $invoice->account_name = $cuenta->name;
		 $invoice->account_nit = $cuenta->nit;
	     // $invoice->industry_id=$branch->industry_id;
		 $invoice->qr =$invoice->account_nit.'|'.$invoice->invoice_number.'|'.$invoice->number_autho.'|'.$invoice->invoice_date.'|'.$invoice->importe_neto.'|'.$invoice->importe_total.'|'.$invoice->client_nit.'|'.$invoice->importe_ice.'|0|0|'.$invoice->descuento_total;
	     // $invoice->country_id= $branch->country_id;
	     $invoice->key_dosage = $branch->key_dosage;
	     $invoice->deadline = $branch->deadline;
	     $invoice->importe_ice =$icetotal;
	     // $invoice->ice = $ice->rate;
	     //cliente
	     //$invoice->client_nit=$input['nit'];
	     $invoice->client_nit=$input->nit;
	     ///$invoice->client_name =$input['name'];
	     $invoice->client_name =$input->name;
              $documents = TypeDocumentBranch::where('branch_id',$invoice->branch_id)->orderBy('id','ASC')->get();
            foreach ($documents as $document)
            {
                $actual_document = TypeDocument::where('id',$document->type_document_id)->first();
                if($actual_document->master_id==1)
                $id_documento = $actual_document->id;
            }
            $invoice->setJavascript($id_documento);
            $invoice->logo = 0;

	     //$invoice->save();
	     
	     $account = Auth::user()->account;	     
	     	$invoice->save();
				//$fecha =$input['fecha'];
				
	     	 // DB::table('invoices')
        //     ->where('id', $invoice->id)
        //     ->update(array('branch' => $branch->name,'invoice_date'=>$f));
	     //error verificar

	     // $invoice = DB::table('invoices')->select('id')->where('invoice_number',$invoice_number)->first();

	     //guardadndo los invoice items
	    foreach ($items as $item) 
    	{
    		
    		
    		
    		// $product = DB::table('products')->select('notes')->where('id',$product_id)->first();
    		  //$product_id = $item['id'];
    		$product_id = $item->id;
	    		 
	    		$product = DB::table('products')
	    							->join('prices',"product_id","=",'products.id')
	    					
	    							->select('products.id','products.notes','prices.cost','products.ice','products.units','products.cc','products.product_key')
	    						    ->where('prices.price_type_id','=',$user->price_type_id)
	    						    ->where('products.account_id','=',$user->account_id)
	    						    ->where('products.id',"=",$product_id)
	    							->first();

	    		// $pr = DB::table('products')->select('cost')->where('id',$product_id)->first();
	    		
	    		
	    		$cost = $product->cost/$product->units;
	    		//$line_total= ((int)$item['qty'])*$cost;
	    		$line_total= ((int)$item->qty)*$cost;

    		
    		  $invoiceItem = InvoiceItem::createNew();
    		  // $invoiceItem->invoice_id = $invoice->id;
    		  $invoiceItem->invoice_id = $invoice->id; 
		      $invoiceItem->product_id = $product_id;
		      $invoiceItem->product_key = $product->product_key;
		      $invoiceItem->notes = $product->notes;
		      $invoiceItem->cost = $cost;
		      /*$invoiceItem->boni = (int)$item['boni'];
		      $invoiceItem->discount =$item['desc'];
		      $invoiceItem->qty = (int)$item['qty'];*/
		      $invoiceItem->boni = (int)$item->boni;
		      $invoiceItem->discount =$item->desc;
		      $invoiceItem->qty = (int)$item->qty;
		      // $invoiceItem->line_total=$line_total;
		      // $invoiceItem->tax_rate = 0;
		      $invoiceItem->save();
		  
    	}
    	

		$datos = array('resultado ' => "0");
		//colocar una excepcion en caso de error

		// return Response::json($datos);
		//return Response::json($datos);
       
    }
     private function completeFields($factura)
    {
    	$invoice_items = array();

    	$datos = $factura;    	    	
		$user_id = Auth::user()->id;
		$user  = DB::table('users')->select('id','account_id','price_type_id')->where('id',$user_id)->first();
    	$ice = DB::table('tax_rates')->select('rate')->where('name','=','ICE')->first();

    	$user_branch= UserBranch::where('user_id',Auth::user()->id)->first();

    	$branch = DB::table('branches')->where('id','=',$user_branch->branch_id)->first();	
        //$branch = DB::table('branches')->where('id','=',1)->first();	    	
    	foreach ($factura['invoice_items'] as $key => $item) {
    		$product = DB::table('products')
    							->join('prices',"product_id","=",'products.id')    					
    							->select('products.id','products.notes','prices.cost','products.ice','products.units','products.cc','products.product_key')
    						    ->where('prices.price_type_id','=',$user->price_type_id)
    						    //->where('products.account_id','=',$user->account_id)
    						    ->where('products.id',"=",$item['id'])
    							->first();    	
    	//print_r($user->price_type_id."hola".$user_id);
    	//return;    									
			$new_item = [
				'boni'	=>	$item['boni'],
				'desc'	=>	$item['desc'],
				'qty'	=>	$item['qty'],
				'id' 	=>	$item['id'],
				'units'		=>	$product->units,
				'cost'		=>	$product->cost,
				'ice'		=>	$product->ice,
				'cc'		=>	$product->cc,
				'product_key'	=>	$product->product_key,
				'notes'		=>	$product->notes,				
			];

			array_push($invoice_items, $new_item) ;
    	}    	

    	$new = [
    	    'fecha'	=>	$datos['fecha'],
    	    'name'	=>	$datos['name'],
    	    'cod_control'	=>	$datos['cod_control'],
    	    'nit'	=>	$datos['nit'],
    	    'invoice_number'	=>	$datos['invoice_number'],
    	    'client_id'	=>	$datos['client_id'],//until here is sent from POS
    	    'user_id'	=>	$user_id,
    	    'ice'	=>	$ice->rate,
    	    'deadline'	=>	$branch->deadline,
    	    'account_id'	=>	'1',
    	    'branch_id'	=>	$branch->id,
    	    'law'	=>	$branch->law,
    	    'activity_pri'	=>	$branch->economic_activity,
    	    'activity_sec1'	=>	$branch->aconomic_activity,
    	    'address1'	=>	$branch->address1,
    	    'address2'	=>	$branch->address2,
    	    'number_autho'	=>	$branch->number_autho,
    	    'postal_code'	=>	$branch->sfc,
    	    'city'	=>	$branch->city,
    	    'state'	=>	$branch->state,
    	    'country_id'	=>	$branch->id,
    	    'key_dosage'	=>	$branch->key_dosage,
    	    'branch'	=>	$branch->name,
    	    'invoice_items'	=> $invoice_items,
            'terceros'  =>  $branch->terceros,
    	];

    	return $new;
    }
    private function saveBackUpToMirror($backup)
    {
    	
    	extract($_POST);
        $username='firstuser';
		$password='first_password';
		$URL='45.55.184.233/public/api/v1/ventas';
		//echo json_encode($backup);
		$fields= array(
			'ventas'=>urlencode(json_encode($backup)),			
			'adicional'=>urlencode("nada")
			);		
		$fields_string="";
		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
		rtrim($fields_string, '&');
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$URL);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_POST, count($fields));
		curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
		$response = curl_exec($ch);
		if(!$response){
    		die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
		}						
		curl_close ($ch);
		return $response;

    }

    private function isConnected($url=NULL)  
	{  

	    if($url == NULL) return false;  
	    $ch = curl_init($url);  
	    curl_setopt($ch, CURLOPT_TIMEOUT, 5);  
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);  
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
	    $data = curl_exec($ch);  
	    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
	    curl_close($ch);  
	    if($httpcode>=200 && $httpcode<300){  
	        return true;  
	    } else {  
	        return false;  
	    }  
	}
	 private function verificar($dato){
    	foreach ($dato as $key => $factura) {
    		if($factura['invoice_number']=="0")
    			return false;
    	}
    	return true;
    }

    public function clientes()
    {
    	// $user_id = Auth::user()->getAuthIdentifier();
    	// $user = DB::table('users')->select('account_id','price_type_id','branch_id')->where('id',$user_id)->first();
    	// $clients = DB::table('clients')->select('id','name','nit','public_id')->where('account_id',$user->account_id)->get(array('id','name','nit','public_id'));
    	// $user_id = Auth::user()->getAuthIdentifier();
    	$user = DB::table('users')->select('account_id','price_type_id','id','group_ids')->where('id',Auth::user()->id)->first();
    	// return Response::json($user);
    	$grupo = explode(',',$user->group_ids);
    	// return Response::json($grupo);	
    	$array =array();

    	// $user_branch= UserBranch::where('user_id',Auth::user()->id)->first();
    	foreach ($grupo as $idgrupo) {

    		// $idg = $idgrupo+1;
    		$cliente = DB::table('clients')->select('id','name','nit','business_name','public_id')
    							->where('account_id',1)
    							->where('group_id',$idgrupo)
    							->get(array('id','name','business_name','nit','public_id'));
    							// ->first();
    		foreach ($cliente as $cli) {
    			# code...
    			$array[] =$cli;
    		}
    		
    		# code...
    	}


   		return Response::json($array); 	
    }
    public function version()
	{
		return Response::json(array('version'=> '3.9.2a'));
	}

	public function saveBackup()
	{

		$input = Input::all();
    	//return $input;
    	$PosBackup = new Backup();
        $PosBackup->user_id=Auth::user()->id*-1;
        $PosBackup->json=json_encode($input);
        $PosBackup->save();
        return Response::json($PosBackup);
	}
	public function test()
	{

	}

	public function guardarFactura()
    {
		$input =json_decode (json_encode (Input::all()), FALSE);
	
		$numero =(int) $input->invoice_number;


		$user_branch= UserBranch::where('user_id',Auth::user()->id)->first();

		$invoice_number = $numero;

		$client_id = $input->client_id;
		// return Response::json($client_id);
		$user  = DB::table('users')->select('account_id','price_type_id')->where('id',Auth::user()->id)->first();
		
		$branch = DB::table('branches')->where('id','=',$user_branch->branch_id)->first();	
    	$items = $input->invoice_items;
    	    	
    	$amount = 0;
    	$subtotal=0;
    	$fiscal=0;
    	$icetotal=0;
    	$bonidesc =0;
    	foreach ($items as $item) 
    	{
    		# code...
    		$product_id = $item->id;
    		 
    		$pr = DB::table('products')
    							->join('prices',"product_id","=",'products.id')
    					
    							->select('products.id','products.notes','prices.cost','products.ice','products.units','products.cc')
    						    ->where('prices.price_type_id','=',$user->price_type_id)
    						    ->where('products.account_id','=',$user->account_id)
    						    ->where('products.id',"=",$product_id)
    							->first();

    		$qty = (int) $item->qty;
    		$cost = $pr->cost/$pr->units;
    		$st = ($cost * $qty);
    		$subtotal = $subtotal + $st; 
    		//$bd= ($item['boni']*$cost) + $item['desc'];
    		$bd= ($item->boni*$cost) + $item->desc;
    		$bonidesc= $bonidesc +$bd;
    		$amount = $amount +$st-$bd;

    		$ice = DB::table('tax_rates')->select('rate')->where('name','=','ICE')->first();
    		if($pr->ice == 1)
    		{
    			
    			//caluclo ice bruto 
    			$iceBruto = ($qty *($pr->cc/1000)*$ice->rate);
    			//$iceNeto = (((int)$item['boni']) *($pr->cc/1000)*$ice->rate);
    			$iceNeto = (((int)$item->boni) *($pr->cc/1000)*$ice->rate);
    			$icetotal = $icetotal +($iceBruto-$iceNeto) ;
    			// $fiscal = $fiscal + ($amount - ($item['qty'] *($pr->cc/1000)*$ice->rate) );
    		}
    		
    		

    			

    	}
    	
        $fiscal = $subtotal -$bonidesc-$icetotal;

    	$balance= $amount;
    	/////////////////////////hasta qui esta bien al parecer hacer prueba de que fuciona el join de los productos XD
    	$invoice_date =date("Y-m-d", strtotime($input->fecha));
    
		$invoice_date_limitCC = date("Y/m/d", strtotime($branch->deadline));


	     $ice = DB::table('tax_rates')->select('rate')->where('name','=','ice')->first();
	     //
	     // creando invoice
	     $cuenta = Account::find(1);
	     $invoice = Invoice::createNew();
	     $invoice->invoice_status_id=6;
	     $invoice->invoice_number=$invoice_number;
	     $invoice->client_id=$client_id;
	     $invoice->user_id=Auth::user()->id;
	     $invoice->account_id = Auth::user()->account_id;
	     $invoice->branch_id= $user_branch->branch_id;
	     $invoice->importe_neto =number_format((float)$amount, 2, '.', '');	
	     $invoice->importe_total = $subtotal;
	     $invoice->debito_fiscal =$fiscal;
	     $invoice->law = $branch->law;
	     $invoice->balance=$balance;
	     // $invoice->control_code=$cod_control;
	     $invoice->start_date =$invoice_date;
	     $invoice->invoice_date=$invoice_date;

		 $invoice->economic_activity=$branch->economic_activity;
	     // $invoice->activity_sec1=$branch->activity_sec1;
	     
	     // $invoice->invoice
	     $invoice->end_date=$invoice_date_limitCC;
	     //datos de la empresa atra vez de una consulta XD
	     /*****************error generado al intentar guardar **/
	   	 $invoice->branch_name = $branch->name;
	     $invoice->address1=$branch->address1;
	     $invoice->address2=$branch->address2;
	     $invoice->number_autho=$branch->number_autho;
	     $invoice->phone=$branch->work_phone;
			$invoice->city=$branch->city;
			$invoice->state=$branch->state;
		 $invoice->account_name = $cuenta->name;
		 $invoice->account_nit = $cuenta->nit;
	     // $invoice->industry_id=$branch->industry_id;
		 $invoice->qr =$invoice->account_nit.'|'.$invoice->invoice_number.'|'.$invoice->number_autho.'|'.$invoice->invoice_date.'|'.$invoice->importe_neto.'|'.$invoice->importe_total.'|'.$invoice->client_nit.'|'.$invoice->importe_ice.'|0|0|'.$invoice->descuento_total;
	     // $invoice->country_id= $branch->country_id;
	     $invoice->key_dosage = $branch->key_dosage;
	     $invoice->deadline = $branch->deadline;
	     $invoice->importe_ice =$icetotal;
	     // $invoice->ice = $ice->rate;
	     //cliente
	     //$invoice->client_nit=$input['nit'];
	     $invoice->client_nit=$input->nit;
	     ///$invoice->client_name =$input['name'];
	     $invoice->client_name =$input->name;
              $documents = TypeDocumentBranch::where('branch_id',$invoice->branch_id)->orderBy('id','ASC')->get();
            foreach ($documents as $document)
            {
                $actual_document = TypeDocument::where('id',$document->type_document_id)->first();
                if($actual_document->master_id==1)
                $id_documento = $actual_document->id;
            }
            $invoice->setJavascript($id_documento);
            $invoice->logo = 0;

	     //$invoice->save();
	     
	     $account = Auth::user()->account;

	     $numAuth = $invoice->number_autho;
		 $numfactura = $invoice->invoice_number;
		 $nit = $invoice->client_nit;
		 $fechaEmision =date("Ymd",strtotime($invoice->invoice_date));
		 $total = $invoice->importe_total;
		 $llave = $branch->key_dosage;
		 $codigoControl = Utils::getControlCode($numfactura,$nit,$fechaEmision,$total,$numAuth,$llave);

		 $invoice->control_code = $codigoControl;	
	     
	     $invoice->save();
	     // return Response::json($invoice);
	     //guardadndo los invoice items
	    foreach ($items as $item) 
    	{
    		
    		
    		
    		// $product = DB::table('products')->select('notes')->where('id',$product_id)->first();
    		  //$product_id = $item['id'];
    		$product_id = $item->id;
	    		 
	    		$product = DB::table('products')
	    							->join('prices',"product_id","=",'products.id')
	    					
	    							->select('products.id','products.notes','prices.cost','products.ice','products.units','products.cc','products.product_key')
	    						    ->where('prices.price_type_id','=',$user->price_type_id)
	    						    ->where('products.account_id','=',$user->account_id)
	    						    ->where('products.id',"=",$product_id)
	    							->first();

	    		// $pr = DB::table('products')->select('cost')->where('id',$product_id)->first();
	    		
	    		
	    		$cost = $product->cost/$product->units;
	    		//$line_total= ((int)$item['qty'])*$cost;
	    		$line_total= ((int)$item->qty)*$cost;

    		
    		  $invoiceItem = InvoiceItem::createNew();
    		  // $invoiceItem->invoice_id = $invoice->id;
    		  $invoiceItem->invoice_id = $invoice->id; 
		      $invoiceItem->product_id = $product_id;
		      $invoiceItem->product_key = $product->product_key;
		      $invoiceItem->notes = $product->notes;
		      $invoiceItem->cost = $cost;
		      /*$invoiceItem->boni = (int)$item['boni'];
		      $invoiceItem->discount =$item['desc'];
		      $invoiceItem->qty = (int)$item['qty'];*/
		      $invoiceItem->boni = (int)$item->boni;
		      $invoiceItem->discount =$item->desc;
		      $invoiceItem->qty = (int)$item->qty;
		      // $invoiceItem->line_total=$line_total;
		      // $invoiceItem->tax_rate = 0;
		      $invoiceItem->save();
		  
    	}
    	return Response::json($invoice);
    }
}
