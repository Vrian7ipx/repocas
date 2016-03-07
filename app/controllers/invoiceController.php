<?php

class InvoiceController extends \BaseController {

	public function __construct()
	{


	}

	// public function index()
	// {
  //           $invoices = Invoice::where('account_id',Auth::user()->account_id)->where('branch_id',Session::get('branch_id'))->orderBy('public_id', 'DESC')->get();
	//     return View::make('factura.index', array('invoices' => $invoices));
	// }



	public function create()
	{

			$sucurasl = Branch::find(Session::get('branch_id'));

			if($sucurasl->type_third==0 || $sucurasl->type_third==3)
			{



            $client = null;
            $account = Account::findOrFail(Auth::user()->account_id);
            $branch = Branch::where('id','=',Session::get('branch_id'))->first();
            $today = date("Y-m-d");
            $expire = $branch->deadline;
            $today_time = strtotime($today);
            $expire_time = strtotime($expire);
            $tax = TaxRate::where('name','ICE')->first();
            if ($expire_time < $today_time)
            {
                Session::flash('error','La fecha límite de emisión caducó, porfavor actualice su Dosificación');
                return Redirect::to('sucursales/'.$branch->id.'/edit');
            }
            $last_invoice= Invoice::where('account_id',Auth::user()->account_id)->where('branch_id',Session::get('branch_id'))->max('invoice_date');
            $last_date=  strtotime($last_invoice);
            $secs = $today_time - $last_date;// == <seconds between the two times>
            $days = $secs / 86400;
            $matriz = Branch::where('id',2)->first();
            //getting the product within their datas
            $products = Product::join('prices','products.id','=','prices.product_id')
                                    ->where('prices.price_type_id',Auth::user()->price_type_id)
                                    ->select(array('products.product_key','products.notes','prices.cost','products.qty','products.units','products.ice','products.cc'))
                                    ->get();
            $invoiceDesigns = TypeDocument::where('account_id',\Auth::user()->account_id)->orderBy('public_id', 'desc')->get();
            $data = array(
                            'account' => $account,
                            'data' => Input::old('data'),
                            'invoiceDesigns' => $invoiceDesigns,
                            'last_invoice_date' => $days,
                            'products' => $products,
                            'tax'=>$tax->rate,
                            'matriz' => $matriz,
                            );
                            $data = array_merge($data, self::getViewModel());
                    return View::make('factura.new', $data);
            }

            Session::flash('message','La Dosificación es para un dispositivo POS razon por la cual no se puede facturar via web');
            return Redirect::to('factura');

	}

	public function newCustom($id_cli)
	{
			$sucurasl = Branch::find(Session::get('branch_id'));
			if($sucurasl->type_third!=2)
			{
            $client = null;
            $account = Account::findOrFail(Auth::user()->account_id);
            $branch = Branch::where('id','=',Session::get('branch_id'))->first();
            $today = date("Y-m-d");
            $expire = $branch->deadline;
            $today_time = strtotime($today);
            $expire_time = strtotime($expire);
            $tax = TaxRate::where('name','ICE')->first();
            if ($expire_time < $today_time)
            {
                Session::flash('error','La fecha límite de emisión caducó, porfavor actualice su Dosificación');
                return Redirect::to('sucursales/'.$branch->id.'/edit');
            }
            $last_invoice= Invoice::where('account_id',Auth::user()->account_id)->where('branch_id',Session::get('branch_id'))->max('invoice_date');
            $last_date=  strtotime($last_invoice);
            $secs = $today_time - $last_date;// == <seconds between the two times>
            $days = $secs / 86400;
            //getting the product within their datas
            $products = Product::join('prices','products.id','=','prices.product_id')
                                    ->where('prices.price_type_id',Auth::user()->price_type_id)
                                    ->select(array('products.product_key','products.notes','prices.cost','products.qty','products.units','products.ice','products.cc'))
                                    ->get();
            $invoiceDesigns = TypeDocument::where('account_id',\Auth::user()->account_id)->orderBy('public_id', 'desc')->get();
            $client = Client::where('id',$id_cli)->first();
            $data = array(
                            'account' => $account,
                            'data' => Input::old('data'),
                            'invoiceDesigns' => $invoiceDesigns,
                            'last_invoice_date' => $days,
                            'products' => $products,
                            'tax'=>$tax->rate,
                            'client' => $client
                            );
                            $data = array_merge($data, self::getViewModel());
                    return View::make('factura.newCustom', $data);
            }

            Session::flash('message','La Dosificación es para un dispositivo POS razon por la cual no se puede facturar via web');
            return Redirect::to('factura');

	}

	public function newNotaEntrega()
	{

		$client = null;
		$account = Account::findOrFail(Auth::user()->account_id);
		// if ($clientPublicId)
		// {
		// 	$client = Client::scope($clientPublicId)->first();
  //  		}
                $branch = Branch::where('id','=',Session::get('branch_id'))->first();
                $today = date("Y-m-d");
                $expire = $branch->deadline;
                $today_time = strtotime($today);
                $expire_time = strtotime($expire);

                if ($expire_time < $today_time)
                {
                    Session::flash('error','La fecha límite de emisión caducó, porfavor actualice su Dosificación');
                    return Redirect::to('sucursales/'.$branch->public_id.'/edit');
                }
                $last_invoice= Invoice::where('account_id',Auth::user()->account_id)->where('branch_id',Session::get('branch_id'))->max('invoice_date');
                $last_date=  strtotime($last_invoice);
                $secs = $today_time - $last_date;// == <seconds between the two times>
                $days = $secs / 86400;

   		$invoiceDesigns = TypeDocument::where('account_id',\Auth::user()->account_id)->orderBy('public_id', 'desc')->get();
		$data = array(
				'entityType' => ENTITY_INVOICE,
				'account' => $account,
				'invoice' => null,
				'showBreadcrumbs' => false,
				'data' => Input::old('data'),
				'invoiceDesigns' => $invoiceDesigns,
				'method' => 'POST',
				'url' => 'factura',
				'title' => trans('texts.new_invoice'),
                                'vencido'=>0,//$vencido,
                                'last_invoice_date'=>$days,
				);
		$data = array_merge($data, self::getViewModel());

		return View::make('factura.newNotaEntrega', $data);
	}

	private static function getViewModel()
	{
		return [
			'branches' => Branch::where('account_id', '=', Auth::user()->account_id)->get(),
			//'clients' => Client::scope()->with('contacts')->orderBy('name')->get(),
			//'client' => Client::where('id','=',$id )->first(),
			'taxRates' => TaxRate::scope()->orderBy('name')->get(),
			'frequencies' => array(
				1 => 'Semanal',
				2 => 'Cada 2 semanas',
				3 => 'Cada 4 semanas',
				4 => 'Mensual',
				5 => 'Trimestral',
				6 => 'Semestral',
				7 => 'Anual'
			)
		];
	}
	public function sql(){
		//$users = DB::connection('mysql2')->table('users')->get();
		return "Updated dont try to do it again";
		$branch_id_golden= 14;
		$account_id_golden = 10;
		$user_id_golden = 12;
		$client_id_ini = 38;
		$client_id_step = 37;
		$invoice_id_golden=596;
		$invoice_id_step = 595;

		$clients=DB::connection('mysql2')->table('clients')->where('account_id',4)->get();
		// foreach ($clients as $key => $client) {
		// 	$idd=$client->public_id+$client_id_step;
		// 	echo $idd." - ".$client->name." <br>";
		// }
		$product_id_ini = 27;
		$product_id_step = 26;
		$products=DB::connection('mysql2')->table('products')->where('account_id',4)->get();


		$items = DB::connection('mysql2')->table('invoice_items')->where('account_id',4)->get();
		foreach ($items as $key => $item) {
			$it = InvoiceItem::createNew();
			$it->account_id = $account_id_golden;
			$invoice=DB::connection('mysql2')->table('invoices')->where('id',$item->invoice_id)->first();
			$it->invoice_id = $invoice->public_id+$invoice_id_step;
			$product=DB::connection('mysql2')->table('products')->where('id',$item->product_id)->first();
			$it->product_id = $product->public_id+$product_id_step;
			$it->created_at = $item->created_at;
			$it->updated_at = $item->updated_at;
			$it->deleted_at = $item->deleted_at;
			$it->product_key = $item->product_key;
			$it->notes = $item->notes;
			$it->cost = $item ->cost;
			$it->qty = $item->qty;
			$it->discount = 0;
			//$it->unidad =
			$it->save();
			//print_r($it);
			echo $item->notes."<br><br>";
		}

		/* THIS SCRIPT ITS TO ADD LOGO AND DESIGN*/
		// $type_document =TypeDocument::where('account_id',10)->first();
		// $invoices=Invoice::where("account_id",10)->get();
		// foreach ($invoices as $key => $invoice) {
		// 	$invoice->logo=$type_document->logo;
		// 	$invoice->javascript=$type_document->javascript_web;
		// 	$invoice->save();
		// }

	//	foreach ($products as $key => $product) {
	//		$idd=$product->public_id+$product_id_step;
		//	echo $idd." - ".$product->notes." <br>";
		//}
	// 	$invoices = DB::connection('mysql2')->table('invoices')->where('account_id',4)->get();
	// 	foreach ($invoices as $key => $invoice) {

	// 	$factura = Invoice::createNew();
	// 	$factura->account_id = $account_id_golden;
	// 	$factura->branch_id = $branch_id_golden;
	// 	$client=DB::connection('mysql2')->table('clients')->where('id',$invoice->client_id)->first();
	// 	$factura->client_id = $client->public_id+$client_id_step;
	// 	$factura->user_id = $user_id_golden;
	// 	$factura->invoice_status_id = $invoice->invoice_status_id;
	// 	$factura->created_at = $invoice->created_at;
	// 	$factura->updated_at = $invoice->updated_at;
	// 	$factura->deleted_at = $invoice->deleted_at;
	// 	$factura->invoice_number = $invoice->invoice_number;
	// 	$factura->invoice_date = $invoice->invoice_date;
	// 	$factura->due_date = $invoice->due_date;
	// 	$factura->discount = $invoice->discount;
	// 	$factura->po_number = $invoice->po_number;
	// 	$factura->terms = $invoice->terms;
	// 	$factura->public_notes = $invoice->public_notes;
	// 	//$factura->note = $invoice->acc;
	// 	$factura->account_name = $invoice->account_name;
	// 	$factura->account_nit = $invoice->account_nit;
	// 	//$factura->account_uniper = $invoice->;
	// 	$factura->sfc = "SFC-1";
	// 	$factura->branch_name =$invoice->branch_name;
	// 	$factura->address1 = $invoice->address1;
	// 	$factura->address2 = $invoice->address2;
	// 	$factura->phone = $invoice->phone;
	// 	$factura->city = $invoice->city;
	// 	$factura->state =$invoice->state;
	// 	$factura->number_autho = $invoice->number_autho;
	// 	$factura->deadline = $invoice->deadline;
	// 	$factura->key_dosage = $invoice->key_dosage;
	// 	//$factura->type_third = $invoice->;
	// 	$factura->client_nit = $invoice->client_nit;
	// 	$factura->client_name = $invoice->client_name;
	// 	$factura->economic_activity = $invoice->activity_pri;
	// 	$factura->law = $invoice->law;
	// 	$factura->control_code = $invoice->control_code;
	// 	//$factura->qr = $invoice->;
	// 	$factura->debito_fiscal = $invoice->fiscal;
	// 	$factura->importe_neto = $invoice->amount;
	// 	$factura->importe_total = $invoice->subtotal;
	// 	$factura->importe_ice = $invoice->ice;
	// 	//$factura->importe_exento = $invoice->;
	// 	$factura->descuento_total =$invoice->discount;
	// 	$factura->balance = $invoice->balance;
	// 	//$factura->logo = $invoice->;
	// 	//$factura->javascript = $invoice->;
	// 	//$factura->public_id = $invoice->;
	// 	$factura->save();
	// 	print_r($factura);
	// 	echo $factura->account_id." ".$factura->branch_id."<br><br>";
	// }





		//$invoices=DB::connection('mysql2')->table('invoices')->where('account_id',4)->get();
		//foreach ($invoices as $key => $invoice) {
		//	echo $invoice->invoice_number."<br>";
		//}
		return 0;
	}
	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{

		$sucurasl = Branch::find(Session::get('branch_id'));

			if($sucurasl->type_third!=2)
			{


				if(sizeof(Input::get('productos'))>1)
				{
					if(Input::has('client'))
					{
		                        $account = Account::where('id', Auth::user()->account_id)->first();
					$branch = Branch::find(Session::get('branch_id'));
					$tax = TaxRate::where("name",'ICE')->first();
					$invoice = Invoice::createNew();
					$invoice->setBranch(Session::get('branch_id'));
					$invoice->setTerms(trim(Input::get('terms')));
					$invoice->setPublicNotes(trim(Input::get('public_notes')));
					$invoice->setInvoiceDate(trim(Input::get('invoice_date')));
					$invoice->setClient(trim(Input::get('client')));
					$invoice->setEconomicActivity($branch->economic_activity);
					$dateparser = explode("/",Input::get('due_date'));
		                        if(Input::get('due_date')){
		                            $date = $dateparser[2].'-'.$dateparser[1].'-'.$dateparser[0];
		                            $invoice->setDueDate($date);
		                        }
					$invoice->setDiscount(trim(Input::get('discount')));
					$invoice->setClientName(trim(Input::get('razon')));
					$invoice->setClientNit(trim(Input::get('nit')));
					$invoice->setUser(Auth::user()->id);
					$dateparser = explode("/",Input::get('invoice_date'));
		                        $date = $dateparser[2].'-'.$dateparser[1].'-'.$dateparser[0];
					$invoice->setInvoiceDate($date);
					$invoice->importe_neto = trim(Input::get('total'));
					$invoice->importe_total=trim(Input::get('subtotal'));
		                        if(Input::get('nota')){
		                        $nota = array();
		                        $nota[0] = [
		                            'date' => date('d-m-Y H:i:s'),
		                            'note' => '<b>'.Auth::user()->first_name." ".Auth::user()->last_name."</b>: ".trim(Input::get('nota'))
		                        ];
		                        $invoice->note = json_encode($nota);
		                        }
					//ACCOUTN AND BRANCK
					$invoice->balance =trim(Input::get('total'));
		                        $invoice->setAccountName($account->name);
					$invoice->setAccountNit($account->nit);
					$invoice->setBranchName($branch->name);
					$invoice->setAddress1($branch->address1);
					$invoice->setAddress2($branch->address2);
					$invoice->setPhone($branch->work_phone);
					$invoice->setCity($branch->city);
					$invoice->setState($branch->state);
					$invoice->setNumberAutho($branch->number_autho);
					$invoice->setKeyDosage($branch->key_dosage);
					$invoice->setTypeThird($branch->type_third);
					$invoice->setDeadline($branch->deadline);
					$invoice->setLaw($branch->law);
					$type_document =TypeDocument::where('account_id',Auth::user()->account_id)->first();

					 $numAuth = $invoice->number_autho;
					 $numfactura = $invoice->invoice_number;
					 $nit = $invoice->client_nit;
					 $fechaEmision =date("Ymd",strtotime($invoice->invoice_date));
					 $total = $invoice->importe_total;
					 $llave = $branch->key_dosage;
					 $codigoControl = Utils::getControlCode($numfactura,$nit,$fechaEmision,$total,$numAuth,$llave);

		                        //calculates ICE

					//$desc = $invoice->subtotal-$invoice->amount;

		                        $invoice->importe_ice=number_format(Input::get('importe_ice'), 2, '.', '');
		                        $invoice->debito_fiscal = number_format(Input::get('importe_fiscal'), 2, '.', '');
		                        //$invoice->discount = number_format($desc, 2, '.', '');

					$invoice->setControlCode($codigoControl);
		                        $documents = TypeDocumentBranch::where('branch_id',$invoice->branch_id)->orderBy('id','ASC')->get();
		                        foreach ($documents as $document)
		                        {
		                            $actual_document = TypeDocument::where('id',$document->type_document_id)->first();
		                            if($actual_document->master_id==1)
		                            $id_documento = $actual_document->id;
		                        }
		                        $invoice->setJavascript($id_documento);
		                        if(Input::get('printer_type')==1)
		                            $invoice->logo = 1;
		                        else
		                            $invoice->logo = 0;
					$invoice->sfc = $branch->sfc;
					$invoice->qr =$invoice->account_nit.'|'.$invoice->invoice_number.'|'.$invoice->number_autho.'|'.$invoice->invoice_date.'|'.$invoice->importe_neto.'|'.$invoice->importe_total.'|'.$invoice->client_nit.'|'.$invoice->importe_ice.'|0|0|'.$invoice->descuento_total;
					if($account->is_uniper)
					{
						$invoice->account_uniper = $account->uniper;
					}
		                        $invoice->invoice_number = branch::getInvoiceNumber();
					$invoice->save();
					foreach (Input::get('productos') as $producto)
		                        {
		                            $prod = $producto;
		                            $product = Product::where('account_id',Auth::user()->account_id)->where('product_key',$producto["'product_key'"])->first();
		                            if($product!=null){
		                                $invoiceItem = InvoiceItem::createNew();
		                                $invoiceItem->setInvoice($invoice->id);
		                                $invoiceItem->setProduct($product->id);
		                                $invoiceItem->setProductKey($producto["'product_key'"]);

		                                $proo = DB::table('products')->where('product_key','=',$producto["'product_key'"])->first();

		                                $invoiceItem->setNotes($proo->notes);
		                                $invoiceItem->setCost($producto["'cost'"]);
		                                $invoiceItem->setQty($producto["'qty'"]+$proo->units*$producto["'pack'"]);
		                                $invoiceItem->packs=$producto["'pack'"];
		                                $invoiceItem->discount=$producto["'disc'"];
		                                $invoiceItem->boni=$producto["'bonus'"];
		                                $invoiceItem->save();

		                            }
		                        }
		                //adicionando cargo al cliente
		                $cliente = Client::find($invoice->client_id);
		                $cliente->balance =$cliente->balance+$invoice->balance;
		                $cliente->save();
		                $newInvoice=Invoice::where('id','=',$invoice->getId())->first();
		                return Redirect::to("factura/".$newInvoice->id);
		                }
		                Session::flash('error','por favor ingrese cliente');
		                return Redirect::to('factura/create');
				}
				Session::flash('error','por favor ingrese productos');
				return Redirect::to('factura/create');

		 }

            Session::flash('error','La Dosificaci�n es para un dispositivo POS razon por la cual no se puede facturar via web');
            return Redirect::to('factura');

	}

        public function storeNota(){
            $account = DB::table('accounts')->where('id','=', Auth::user()->account_id)->first();
            $branch = Branch::where("id",Session::get('branch_id'))->first();
            $invoice = Invoice::createNew();
            $invoice->setBranch(Session::get('branch_id'));
            $invoice->setTerms(trim(Input::get('terms')));
            $invoice->setPublicNotes(trim(Input::get('public_notes')));
            $invoice->setInvoiceDate(trim(Input::get('invoice_date')));
            $invoice->setClient(trim(Input::get('client')));
            $invoice->setEconomicActivity($branch->economic_activity);
            $dateparser = explode("/",Input::get('due_date'));
            if(Input::get('due_date')){
                $date = $dateparser[2].'-'.$dateparser[1].'-'.$dateparser[0];
                $invoice->setDueDate($date);
            }
            $invoice->setDiscount(trim(Input::get('discount')));
            $invoice->setClientName(trim(Input::get('razon')));
            $invoice->setClientNit(trim(Input::get('nit')));

            $invoice->setUser(Auth::user()->id);
            $dateparser = explode("/",Input::get('invoice_date'));
            $date = $dateparser[2].'-'.$dateparser[1].'-'.$dateparser[0];
            $invoice->setInvoiceDate($date);
            $invoice->importe_neto = trim(Input::get('total'));
            $invoice->importe_total=trim(Input::get('subtotal'));
            //$invoice->note = trim(Input::get('nota'));
            if(Input::get('nota')){
                $nota = array();
                $nota[0] = [
                    'date' => date('d-m-Y H:i:s'),
                    'note' => '<b>'.Auth::user()->first_name." ".Auth::user()->last_name."</b>: ".trim(Input::get('nota'))
                ];
                $invoice->note = json_encode($nota);
            }
            //ACCOUTN AND BRANCK
            $invoice->balance =trim(Input::get('total'));

            $invoice->setAccountName($account->name);
            //$invoice->setAccountNit($account->nit);
            $invoice->setBranchName($branch->name);
            $invoice->setAddress1($branch->address1);
            $invoice->setAddress2($branch->address2);
            $invoice->setPhone($branch->work_phone);
            //$invoice->setCity($branch->city);
            //$invoice->setState($branch->state);
            //$invoice->setNumberAutho($branch->number_autho);
            //$invoice->setKeyDosage($branch->key_dosage);
            //$invoice->setTypeThird($branch->type_third);
            //$invoice->setDeadline($branch->deadline);
            //$invoice->setLaw($branch->law);
            $type_document =TypeDocument::where('account_id',Auth::user()->account_id)->first();


            //$invoice->invoice_number = branch::getInvoiceNumber();
            $number=1;
            $the_last = Invoice::where('account_id',Auth::user()->account_id)->where('branch_id',Session::get('branch_id'))->whereNotNull('document_number')->orderBy('document_number','DESC')->first();
            if(isset($the_last->document_number))
                $number=$the_last->document_number+1;
            $invoice->document_number = $number;
            //echo Session::get('branch_id')." ".$number." ".$the_last->document_number." -";

//            if(Input::get('printer_type')==1)
//                $invoice->setJavascript($type_document->javascript_web);
//            else
//                $invoice->setJavascript($type_document->javascript_pos);
//            $document = TypeDocumentBranch::where('branch_id',$invoice->branch_id)->orderBy('type_document_id','DESC')->first();
//            $type_document =TypeDocument::where('id',$document->type_document_id)->first();
//            $invoice->setJavascript($type_document->javascript_web);
//            $invoice->logo = $type_document->logo;

            $documents = TypeDocumentBranch::where('branch_id',$invoice->branch_id)->orderBy('id','ASC')->get();
            foreach ($documents as $document)
            {
                $actual_document = TypeDocument::where('id',$document->type_document_id)->first();
                if($actual_document->master_id==2)
                $id_documento = $actual_document->id;
            }
            $invoice->setJavascript($id_documento);

            //$type_document =TypeDocument::where('id',$document->type_document_id)->first();
            //$invoice->setJavascript($type_document->javascript_web);

            if(Input::get('printer_type')==1)
                $invoice->logo = 1;
            else
                $invoice->logo = 0;


            $invoice->save();
            foreach (Input::get('productos') as $producto)
            {
                $prod = $producto;
                $product = Product::where('account_id',Auth::user()->account_id)->where('product_key',$producto["'product_key'"])->first();
                if($product!=null){

                        $invoiceItem = InvoiceItem::createNew();
                        $invoiceItem->setInvoice($invoice->id);
                        $invoiceItem->setProduct($product->id);
                        $invoiceItem->setProductKey($producto["'product_key'"]);

                        $proo = DB::table('products')->where('product_key','=',$producto["'product_key'"])->first();

                        $invoiceItem->setNotes($proo->notes);
                        $invoiceItem->setCost($producto["'cost'"]);
                        $invoiceItem->setQty($producto["'qty'"]);
                        $invoiceItem->save();
                }
            }

           //adicionando cargo al cliente
           $cliente = Client::find($invoice->client_id);
           $cliente->balance =$cliente->balance+$invoice->balance;
           $cliente->save();
           $newInvoice=Invoice::where('id','=',$invoice->getId())->first();

           return Redirect::to("factura/".$newInvoice->getPublicId());

        }
        //My function to send mail
	public function sendInvoiceByMail()
	{
            $mails = array();
            $contactos = "";
            foreach (Input::get('contactos') as $key => $con) {
                    if(isset($con['checked'])){
                            array_push($mails, $con['mail']);
                            $contactos.= "<br><b>".$con['name']."</b> - ".$con['mail'];
                    }
            }
            //return 0;
            $invoices = Invoice::where('account_id',Auth::user()->account_id)->orderBy('public_id', 'DESC')->get();
            $this->sendInvoiceToContact(Input::get('id'),Input::get('date'),Input::get('nit'),$mails);
            if($contactos!=""){
            $this->addNote(Input::get('id'), "<b>Enviada</b> - ".Auth::user()->first_name." ".Auth::user()->last_name.": La factura ah sido enviada exitosamente a: ".$contactos,2);
        	//Session::flash('message',"Enviado corréctamente");
        	}
        	//else
        //		Session::flash('error',"No ingresó el mail del remitente");
            //return View::make('factura.index', array('invoices' => $invoices));
            return Redirect::to('factura');
	}

	private function sendInvoiceToContact($id,$date,$nit,$mail_to){

		$link_object = array(
			'id' => $id,
			'random_string' => "thiIsARandomString,YouNeedToChangeIt",
			'date' => $date,
			'nit' => $nit
		);
		$link_object = json_encode($link_object);
		$idnew = base64_encode($link_object);//base64_encode(1);

		$invoice = Invoice::find($id);

		foreach ($mail_to as $key => $m_to) {
			global $ma_to;
			$ma_to = $m_to;
			Mail::send('emails.wellcome', array('link' => 'www.sigcfactu.com.bo/clientefactura/'.$idnew,'cliente'=>$invoice->client_name,'nit'=>$invoice->client_nit,'monto'=>$invoice->importe_total,'numero_factura'=>$invoice->invoice_number), function($message)
			{
				global $ma_to;
	    		$message->to($ma_to, '')->subject('Factura');
			});
		}
		return 0;
	}
	private function sendByMail(){
				$aux = 0;
				foreach ($client->contacts as $contact)
				{
					if ($contact->email)
					{
						$aux = 1;
					}
				}
				if($aux == 0)
				{
					$errorMessage = trans('El cliente no tiene Correo ElectrÃ³nico.');
					Session::flash('error', $errorMessage);
				}
				else
				{
					if (Auth::user()->confirmed && !Auth::user()->isDemo())
					{
						$message = trans("texts.emailed_{$entityType}");
						$this->mailer->sendInvoice($invoice);
						Session::flash('message', $message);
					}
					else
					{
						$errorMessage = trans(Auth::user()->registered ? 'texts.confirmation_required' : 'texts.registration_required');
						Session::flash('error', $errorMessage);
						Session::flash('message', $message);
					}
				}
	}

	private function save($publicId = null)
	{
		$action = Input::get('action');
		$entityType = Input::get('entityType');

		if ($action == 'archive' || $action == 'delete' || $action == 'mark')
		{

			return InvoiceController::bulk($entityType);
		}

		$input = json_decode(Input::get('data'));
		//echo "this is the result";
		$invoice = $input->invoice;
		//print_r($invoice);exit();


	    $branch = Branch::where('account_id', '=', Auth::user()->account_id)->where('id',Auth::user()->branch_id)->first();
		$today = new DateTime('now');
		$today = $today->format('Y-m-d');
		$datelimit = DateTime::createFromFormat('Y-m-d', $branch->deadline);
		$datelimit = $datelimit->format('Y-m-d');
		$first = explode ("-", $datelimit);
		$second = explode ("-", $today);
		$first_day    = $first[2];
		$first_month  = $first[1];
		$first_year   = $first[0];
		$second_day   = $second[2];
		$second_month = $second[1];
		$second_year  = $second[0];
		$a = gregoriantojd($first_month, $first_day, $first_year);
		$b = gregoriantojd($second_month, $second_day, $second_year);
		$errorS = "Expiró la Fecha Límite de " . $branch->name;
		if($a - $b < 0)
		{
			Session::flash('error', $errorS);
			return Redirect::to("{$entityType}s/create")
				->withInput();
		}
		else
		{

		$last_invoice = Invoice::where('account_id', '=', Auth::user()->account_id)->first();
		if ($last_invoice)
		{
			$yesterday = $last_invoice->invoice_date;
			$today = date("Y-m-d", strtotime($invoice->invoice_date));
			$errorD = "La fecha de la factura es incorrecta";
			$yesterday = new DateTime($yesterday);
			$today = new DateTime($today);

			if($yesterday > $today)
			{
				Session::flash('error', $errorD);
				return Redirect::to("{$entityType}s/create")
					->withInput();

			}
		}

		if (false && $errors = $this->invoiceRepo->getErrors($invoice))
		{
			Session::flash('error', trans('texts.invoice_error'));

			return Redirect::to("{$entityType}s/create")
				->withInput()->withErrors($errors);
		}
		else
		{
			//$this->taxRateRepo->save($input->tax_rates);

			$clientData = (array) $invoice->client;
			$clientData['branch'] = $branch->id;
			$client = $this->saveClient($invoice->client->public_id, $clientData);

			$invoiceData = (array) $invoice;
			$invoiceData['branch_id'] = $branch->id;
			$invoiceData['client_id'] = $client->id;
			$invoiceData['client_nit'] = $client->nit;
			$invoiceData['client_name'] = $client->name;
			$invoiceData['action'] = $action;

			//$invoice = $this->invoiceRepo->save($publicId, $invoiceData, $entityType);

			$account = Auth::user()->account;

			$client->load('contacts');
			$sendInvoiceIds = [];

			foreach ($client->contacts as $contact)
			{
				if ($contact->send_invoice || count($client->contacts) == 1)
				{
					$sendInvoiceIds[] = $contact->id;
				}
			}

			/*foreach ($client->contacts as $contact)
			{
				$invitation = Invitation::scope()->whereContactId($contact->id)->whereInvoiceId($invoice->id)->first();

				if (in_array($contact->id, $sendInvoiceIds) && !$invitation)
				{
					$invitation = Invitation::createNew();
					$invitation->invoice_id = $invoice->id;
					$invitation->contact_id = $contact->id;
					$invitation->invitation_key = str_random(RANDOM_KEY_LENGTH);
					$invitation->save();
				}
				else if (!in_array($contact->id, $sendInvoiceIds) && $invitation)
				{
					$invitation->delete();
				}
			}*/

			$message = trans($publicId ? "texts.updated_{$entityType}" : "texts.created_{$entityType}");
			if ($input->invoice->client->public_id == '-1')
			{
				$message = $message . ' ' . trans('texts.and_created_client');

				$url = URL::to('clients/' . $client->public_id);
				Utils::trackViewed($client->getDisplayName(), ENTITY_CLIENT, $url);
			}


			if ($action == 'email')
			{
				$aux = 0;
				foreach ($client->contacts as $contact)
				{
					if ($contact->email)
					{
						$aux = 1;
					}
				}
				if($aux == 0)
				{
					$errorMessage = trans('El cliente no tiene Correo Electrónico.');
					Session::flash('error', $errorMessage);
				}
				else
				{
					if (Auth::user()->confirmed && !Auth::user()->isDemo())
					{
						$message = trans("texts.emailed_{$entityType}");
						$this->mailer->sendInvoice($invoice);
						Session::flash('message', $message);
					}
					else
					{
						$errorMessage = trans(Auth::user()->registered ? 'texts.confirmation_required' : 'texts.registration_required');
						Session::flash('error', $errorMessage);
						Session::flash('message', $message);
					}
				}


			}
			else if ($action == 'savepay')
			{

		        $payment = Payment::createNew();
		        $payment->client_id = $client->id;
		        $payment->invoice_id = $invoice->id;
		        $payment->payment_type_id = 1;
		        $payment->payment_date = $invoice->invoice_date;
		        $payment->amount = $invoice->amount;
		        $payment->save();
				$message = trans("texts.savepay_{$entityType}");
				Session::flash('message', $message);


			}
			else if ($action == 'savepaycredit')
			{

		        $payment = Payment::createNew();

	            $credits = Credit::scope()->where('client_id', '=', $client->id)
	            			->where('balance', '>', 0)->orderBy('created_at')->get();
	            $applied = 0;

	            foreach ($credits as $credit)
	            {
	                $applied += $credit->apply($invoice->amount);

	                if ($applied >= $invoice->amount)
	                {
	                    break;
	                }
	            }

		        $payment->client_id = $client->id;
		        $payment->invoice_id = $invoice->id;
		        $payment->payment_type_id = 2;
		        $payment->payment_date = $invoice->invoice_date;
		        $payment->amount = $invoice->amount;
		        $payment->save();
				$message = trans("texts.savepay_{$entityType}");
				Session::flash('message', $message);


			}
			else
			{
				Session::flash('message', $message);
			}

			//$url = "factura/" . $invoice->public_id . '/show';
			$url = "factura/1";
			return Redirect::to($url);
		}
		}
	}

	public function saveClient($publicId, $data, $notify = true)
	{
		if (!$publicId || $publicId == "-1")
		{
			$client = Client::createNew();
			$client->currency_id = 1;
			$contact = Contact::createNew();
			$contact->is_primary = true;
			$contact->send_invoice = true;
		}
		else
		{
			$client = Client::scope($publicId)->with('contacts')->first();
			$contact = $client->contacts()->where('is_primary', '=', true)->first();
		}


		if (isset($data['nit'])) {
			$client->nit = trim($data['nit']);
		}

		if (isset($data['name'])) {
			$client->name = trim($data['name']);
		}
        if (isset($data['business_name'])) {
			$client->business_name = trim($data['business_name']);
		}
		if (isset($data['work_phone'])) {
			$client->work_phone = trim($data['work_phone']);
		}
		if (isset($data['custom_value1'])) {
			$client->custom_value1 = trim($data['custom_value1']);
		}
		if (isset($data['custom_value2'])) {
			$client->custom_value2 = trim($data['custom_value2']);
		}
		if (isset($data['address1'])) {
			$client->address1 = trim($data['address1']);
		}
		if (isset($data['address2'])) {
			$client->address2 = trim($data['address2']);
		}
		if (isset($data['city'])) {
			$client->city = trim($data['city']);
		}
		if (isset($data['state'])) {
			$client->state = trim($data['state']);
		}
		if (isset($data['private_notes'])) {
			$client->private_notes = trim($data['private_notes']);
		}
		$client->save();

		$isPrimary = true;
		$contactIds = [];

		if (isset($data['contact']))
		{
			$info = $data['contact'];
			if (isset($info['email'])) {
				$contact->email = trim(strtolower($info['email']));
			}
			if (isset($info['first_name'])) {
				$contact->first_name = trim($info['first_name']);
			}
			if (isset($info['last_name'])) {
				$contact->last_name = trim($info['last_name']);
			}
			if (isset($info['phone'])) {
				$contact->phone = trim($info['phone']);
			}
			$contact->is_primary = true;
			$contact->send_invoice = true;
			$client->contacts()->save($contact);
		}
		else
		{
			foreach ($data['contacts'] as $record)
			{
				$record = (array) $record;

				if ($publicId != "-1" && isset($record['public_id']) && $record['public_id'])
				{
					$contact = Contact::scope($record['public_id'])->first();
				}
				else
				{
					$contact = Contact::createNew();
				}

				if (isset($record['email'])) {
					$contact->email = trim(strtolower($record['email']));
				}
				if (isset($record['first_name'])) {
					$contact->first_name = trim($record['first_name']);
				}
				if (isset($record['last_name'])) {
					$contact->last_name = trim($record['last_name']);
				}
				if (isset($record['phone'])) {
					$contact->phone = trim($record['phone']);
				}
				$contact->is_primary = $isPrimary;
				$contact->send_invoice = isset($record['send_invoice']) ? $record['send_invoice'] : true;
				$isPrimary = false;

				$client->contacts()->save($contact);
				$contactIds[] = $contact->public_id;
			}

			foreach ($client->contacts as $contact)
			{
				if (!in_array($contact->public_id, $contactIds))
				{
					$contact->delete();
				}
			}
		}

		$client->save();

		if (!$publicId || $publicId == "-1")
		{
			\Activity::createClient($client, $notify);
		}

		return $client;
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id=0)
	{
             $invoice = Invoice::where('account_id','=',Auth::user()->account_id)->where('id','=',$id)->first(
                array(
                'id',
                'user_id',
                'account_name',
                'account_nit',
                'account_uniper',
                'account_uniper',
                'address1',
                'address2',
                'terms',
                'importe_neto',
                'importe_total',
                'branch_name',
                'city',
                'client_id',
                'client_name',
                'client_nit',
                'control_code',
                'deadline',
                'discount',
                'economic_activity',
                'end_date',
                'invoice_date',
                'invoice_status_id',
                'invoice_number',
                'number_autho',
                'phone',
                'public_notes',
                'qr',
                'logo',
                'public_id',
                'note',
                'sfc',
                'type_third',
                'branch_id',
                'state',
                'law',
                'phone',
                'document_number')
                );
								// return $invoice;


		$account = Account::find(Auth::user()->account_id);
		//return $invoice['id'];
		$products = InvoiceItem::where('invoice_id',$invoice->id)->get();

		$invoice['invoice_items']=$products;
		$invoice['third']=$invoice->type_third;
		$invoice['is_uniper'] = $account->is_uniper;
		$invoice['uniper'] = $account->uniper;
		$invoice['logo'] = $invoice->logo;

		$client_id = $invoice->client_id;
		$client = DB::table('clients')->where('id','=', $client_id)->first();
		$contacts = Contact::where('client_id',$client->id)->get(array('id','is_primary','first_name','last_name','email'));
                $status=  InvoiceStatus::where('id',$invoice->invoice_status_id)->first();
		//echo $client_id;
		//print_r($contacts);
	//	return 0;
                if($invoice->note=="")
                    $nota = [];
                else
                    $nota = json_decode($invoice['note']);
                //$matriz = Branch::where('account_id','=',$invoice->account_id)->where('number_branch','=','0')->first();
                $matriz = Branch::where('id',2)->first();
                //return $nota;
                $user = User::where('id',$invoice->user_id)->first();
               // echo $user->username;
               // return 0;
		$data = array(
			'invoice' => $invoice,
			'account'=> $account,
			'products' => $products,
			'contacts' => $contacts,
                        'nota'      => $nota,
                        'copia'     => 0,
                        'matriz'    => $matriz,
                        'user'      => $user,
                        'status'    => $status->name=="Parcial"?"Parcialmente Pagado":$status->name,
		);
		return View::make('factura.show',$data);
	}

        public function preview()
        {
                 return  Response::json(Input::all());
                $account = DB::table('accounts')->where('id','=', Auth::user()->account_id)->first();
                //$matriz = Branch::where('account_id','=',Auth::user()->account_id)->where('number_branch','=',0)->first();
                $matriz = Branch::where('id',2)->first();
                $branch = Branch::where('id','=',Session::get('branch_id'))->first();
                $branchDocument = TypeDocumentBranch::where('branch_id','=',$branch->id)->first();
		$type_document =TypeDocument::where('id','=',$branchDocument->type_document_id)->first();

                $invoice =(object) [
			'id'=>'0',
			'account_name'=>$account->name,
			'account_nit'=>$account->nit,
			'account_uniper'=>$account->uniper,
			'address1'=>$branch->address1,
			'address2'=>$branch->address2,
			'terms'=>Input::get('terms'),
			'importe_neto'=>Input::get('subtotal'),
			'importe_total'=>Input::get('total'),
			'branch_name'=>$branch->name,
			'city'=>$branch->city,
			'client_id'=>Input::get('client'),
			'client_name'=>Input::get('nombre'),
			'client_nit'=>Input::get('nit'),
			'control_code'=>'00-00-00-00',
			'deadline'=>Input::get('due_date'),
			'descuento_total'=>Input::get('discount'),
			'economic_activity'=>$branch->economic_activity,
			'end_date'=>Input::get('due_date'),
			'invoice_date'=>Input::get('invoice_date'),
			'invoice_number'=>0,
			'number_autho'=>$branch->number_autho,
			'phone'=>$branch->work_phone,
			'public_notes'=>Input::get('public_notes'),
			'logo'=>$type_document->logo,
                         'sfc'=>$branch->sfc,
                        'type_third'=>$branch->type_third,
                        'branch_id'=>$branch->id,
                        'state'=>$branch->state,
                        'law'=>$branch->law,
                ];


                $products = array();

                foreach (Input::get('productos') as $producto)
                {
	    		$product = Product::where('account_id',Auth::user()->account_id)->where('product_key',$producto["'product_key'"])->first();
		    	if($product!=null){
                            $prod=(object) [
                                'product_key'=>$producto["'product_key'"],
                                'notes'=>$product->notes,
                                'cost'=>$producto["'cost'"],
                                'qty'=>$producto["'qty'"],
                            ];
                            array_push($products, $prod);
		      	}
                  }
               // $invoice = Input::all();

		$data = array(
			'invoice' => $invoice,
			'account'=> $account,
			'products' => $products,
                        'matriz'   => $matriz,
		);

		return View::make('factura.ver',$data);
        }

	public function verFactura($id)
        {
           $invoice = Invoice::where('account_id','=',Auth::user()->account_id)->where('id','=',$id)->first(
                    array(
                    'id',
                    'user_id',
                    'account_name',
                    'account_id',
                    'account_nit',
                    'account_uniper',
                    'account_uniper',
                    'address1',
                    'address2',
                    'terms',
                    'importe_neto',
                    'importe_total',
                    'branch_name',
                    'city',
                    'client_id',
                    'client_name',
                    'client_nit',
                    'control_code',
                    'deadline',
                    'discount',
                    'economic_activity',
                    'end_date',
                    'invoice_date',
                    'invoice_status_id',
                    'invoice_number',
                    'number_autho',
                    'phone',
                    'public_notes',
                    'qr',
                    'logo',
                     'sfc',
                    'type_third',
                    'branch_id',
                    'state',
                    'law',
                    'phone',
                    'javascript',
                    'document_number',
									  'importe_ice',
									  'debito_fiscal' )
                    );

            $account = Account::find(Auth::user()->account_id);
            $products = InvoiceItem::where('invoice_id',$invoice->id)->get();
						foreach ($products as $key => $product) {
								$product->units2 = Product::where('id', $product->product_id)->select('units')->first();
						}
						// return $products;
            $invoice['invoice_items']=$products;
            $invoice['third']=$invoice->type_third;
            $invoice['is_uniper'] = $account->is_uniper;
            $invoice['uniper'] = $account->uniper;
            $document=  TypeDocument::where("id",$invoice->javascript)->first();

           // echo $invoice->javascript."<<<";
           // return 0;
            if($invoice->logo=="1")
            $invoice->javascript = $document->javascript_web;
            else
            $invoice->javascript=  $document->javascript_pos;
            $invoice->logo = $document->logo;
            $client = DB::table('clients')->where('id','=', $client_id)->first();
            $contacts = Contact::where('client_id',$client->id)->get(array('id','is_primary','first_name','last_name','email'));
            if(Input::get('copia'))
                $copia=Input::get('copia');
            else
                $copia = 0;
            //$matriz = Branch::where('account_id','=',$invoice->account_id)->where('number_branch','=','0')->first();
            $matriz = Branch::where('id',2)->first();
            $user = User::where('id',$invoice->user_id)->first();
						$template = TypeDocumentBranch::where('branch_id', $invoice->branch_id)->select('template')->first();
						//return $invoice->branch_id;
			//echo $template->template;return ;
            $data = array(
                    'invoice' => $invoice,
                    'account'=> $account,
                    'products' => $products,
                    'contacts' => $contacts,
                    'matriz'    => $matriz,
                    'copia' => $copia,
                    'publicId' => $invoice->public_id,
                    'user'      => $user,
					'template' => $template
            );
            return View::make('factura.ver',$data);
	}

        public function verFacturaFiscal($publicId){
            $invoice = Invoice::where('account_id','=',Auth::user()->account_id)->where('public_id','=',$publicId)->first(
                    array(
                    'id',
                    'account_name',
                    'account_id',
                    'account_nit',
                    'account_uniper',
                    'account_uniper',
                    'address1',
                    'address2',
                    'terms',
                    'importe_neto',
                    'importe_total',
                    'branch_name',
                    'city',
                    'client_id',
                    'client_name',
                    'client_nit',
                    'control_code',
                    'deadline',
                    'discount',
                    'economic_activity',
                    'end_date',
                    'invoice_date',
                    'invoice_status_id',
                    'invoice_number',
                    'number_autho',
                    'phone',
                    'public_notes',
                    'qr',
                    'logo',
                     'sfc',
                    'type_third',
                    'branch_id',
                    'state',
                    'law',
                    'phone',
                    'javascript')
                    );
            $account = Account::find(Auth::user()->account_id);
            //return $invoice['id'];
            $products = InvoiceItem::where('invoice_id',$invoice->id)->get();

            $invoice['invoice_items']=$products;
            $invoice['third']=$invoice->type_third;
            $invoice['is_uniper'] = $account->is_uniper;
            $invoice['uniper'] = $account->uniper;
            $invoice['logo'] = $invoice->getLogo();

            $client_id = $invoice->getClient();
            $client = DB::table('clients')->where('id','=', $client_id)->first();
            $contacts = Contact::where('client_id',$client->id)->get(array('id','is_primary','first_name','last_name','email'));
            //echo $client_id;
            //print_r($contacts);
    //	return 0;

            $matriz = Branch::where('account_id','=',$invoice->account_id)->where('number_branch','=','0')->first();
            $data = array(
                    'invoice' => $invoice,
                    'account'=> $account,
                    'products' => $products,
                    'contacts' => $contacts,
                    'matriz'    => $matriz,
                    'copia' => 0,
                    'publicId' => $invoice->public_id,
            );
            return View::make('factura.ver2',$data);

        }

        public function verFacturaCliente($correo){
            $sent = base64_decode($correo);
            $array = json_decode($sent);
            $id_inv = $array->id;
            $invoice = Invoice::where('id','=',$id_inv)->first(
                    array(
                    'id',
                    'account_name',
                    'account_id',
                    'account_nit',
                    'account_uniper',
                    'account_uniper',
                    'address1',
                    'address2',
                    'terms',
                    'importe_neto',
                    'importe_total',
                    'branch_name',
                    'city',
                    'client_id',
                    'client_name',
                    'client_nit',
                    'control_code',
                    'deadline',
                    'discount',
                    'economic_activity',
                    'end_date',
                    'invoice_date',
                    'invoice_status_id',
                    'invoice_number',
                    'number_autho',
                    'phone',
                    'public_notes',
                    'qr',
                    'logo',
                     'sfc',
                    'type_third',
                    'branch_id',
                    'state',
                    'law',
                    'phone',
                    'javascript',
                    'importe_ice',
									  'debito_fiscal' )
                    );


           // $account = Account::find(Auth::user()->account_id);
            //return $invoice['id'];
            $products = InvoiceItem::where('invoice_id',$invoice->id)->get();

            $invoice['invoice_items']=$products;
            $invoice['third']=$invoice->type_third;
            $invoice['is_uniper'] = false;//$account->is_uniper;
            //$invoice['uniper'] = $account->uniper;
            $invoice['logo'] = $invoice->getLogo();

            $client_id = $invoice->getClient();
            $client = DB::table('clients')->where('id','=', $client_id)->first();
            $contacts = Contact::where('client_id',$client->id)->get(array('id','is_primary','first_name','last_name','email'));
            $this->addNote($invoice->id, "<b> Visto: </b> Cliente ".$invoice->client_name,3);
            //echo $client_id;
            //print_r($contacts);
    //	return 0;
            $matriz = Branch::where('account_id','=',$invoice->account_id)->where('number_branch','=','0')->first();
            $template = TypeDocumentBranch::where('branch_id', $invoice->branch_id)->select('template')->first();
            $document=  TypeDocument::where("id",1)->first();
            $invoice->logo = $document->logo;

            $data = array(
                    'invoice' => $invoice,
                    //'account'=> $account,
                    'products' => $products,
                    'contacts' => $contacts,
                    'matriz'    => $matriz,
                    'copia' => 0,//Input::get('copia'),
                    'publicId' => $invoice->public_id,
                    'template' => $template
            );
            return View::make('factura.ver',$data);
        }
        public function factura2()
        {	
        	// return  Response::json(Input::all());
                $account = DB::table('accounts')->where('id','=', Auth::user()->account_id)->first();
                //$matriz = Branch::where('number_branch','=',0)->first();
                $matriz = Branch::where('id',2)->first();
                $branch = Branch::where('id','=',Session::get('branch_id'))->first();
                $type_document =TypeDocument::where('account_id',Auth::user()->account_id)->where('master_id',Input::get('invoice_type'))->orderBy('id','DESC')->first();

                if(Input::get('printer_type')==1)
                    $js=$type_document->javascript_web;
                else
                    $js=$type_document->javascript_pos;
                $client = Client::where('id',Input::get('client'))->first();
                $invoice =(object) [
			'id'=>'0',
			'account_name'=>$account->name,
			'account_nit'=>$account->nit,
			'account_uniper'=>$account->uniper,
			'address1'=>$branch->address1,
			'address2'=>$branch->address2,
			'terms'=>Input::get('terms'),
			'importe_neto'=>Input::get('total'),
			'importe_total'=>Input::get('subtotal'),
			'discount'=>number_format(Input::get('subtotal')-Input::get('total'), 2, '.', ''),
			'importe_ice'=>number_format(Input::get('importe_ice'), 2, '.', ''),
			'debito_fiscal' => number_format(Input::get('importe_fiscal'), 2, '.', ''),
			'branch_name'=>$branch->name,
			'city'=>$branch->city,
			'client_id'=>Input::get('client'),
			'client_name'=>Input::get('nombre'),
			'client_nit'=>Input::get('nit'),
			'control_code'=>'00-00-00-00',
                        'deadline'=>$branch->deadline,
			'descuento_total'=>Input::get('discount'),
			'economic_activity'=>$branch->economic_activity,
			'end_date'=>Input::get('due_date'),
			'invoice_date'=>Input::get('invoice_date'),
			'invoice_number'=>0,
			'number_autho'=>$branch->number_autho,
			'phone'=>$branch->work_phone,
			'public_notes'=>Input::get('public_notes'),
			'logo'=>$type_document->logo,
                         'sfc'=>$branch->sfc,
                        'type_third'=>$branch->type_third,
                        'branch_id'=>$branch->id,
                        'state'=>$branch->state,
                        'law'=>$branch->law,
                        'javascript'=> $js,
                        'document_number'=>0,
                        'extralabel'=>$client->custom_value1
                ];

//                $document=  TypeDocument::where("id",$invoice->javascript)->first();
//                $invoice->logo = $document->logo;
//                $invoice->javascript = $invoice->logo?$document->javascript_web:$document->javascript_pos;
                $user = Auth::user();


                $products = array();

                foreach (Input::get('productos') as $producto)
                {
	    		$product = Product::where('account_id',Auth::user()->account_id)->where('product_key',$producto["'product_key'"])->first();
		    	if($product!=null){
                            $prod=(object) [
                                'product_key'=>$producto["'product_key'"],
                                'notes'=>$product->notes,
                                'cost'=>$producto["'cost'"],
                                'qty'=>$producto["'qty'"]/$product->units+$producto["'pack'"],
                            ];
                            array_push($products, $prod);
		      	}
                  }
               // $invoice = Input::all();
		$template = TypeDocumentBranch::where('branch_id', $invoice->branch_id)->select('template')->first();
		//print_r($template->template);

		//return 0;
		$data = array(
			'invoice' => $invoice,
			'account'=> $account,
			'products' => $products,
            'copia'     =>0,
            'matriz'   => $matriz,
            'user'  => $user,
			'template' => $template
		);
//                if(Input::get('printer_type')==0)
//                    return View::make('factura.ver2',$data);
                    return View::make('factura.ver',$data);
        }

        public function addNote($id,$note_sent,$status){
            $invoice = Invoice::where('id','=',$id)->first();
            if($invoice->note=="")
            {
                $nota = array();
                    $nota[0] = [
                        'date' => date('d-m-Y H:i:s'),
                        'note' => $note_sent
                    ];
            }
            else{
                $nota = json_decode($invoice->note);

                    $nota_add = [
                        'date' => date('d-m-Y H:i:s'),
                        'note' => $note_sent
                    ];
                array_push($nota, $nota_add);
            }
            $invoice->note = json_encode($nota);
            $invoice->invoice_status_id=$status;
            $invoice->save();
        }

        public function nuevanota($publicId){

            $invoice = Invoice::where('account_id','=',Auth::user()->account_id)->where('public_id','=',$publicId)->first();
                //[{"date":"04-11-2015 12:23:30","note":"kdfkjdjd ew we we ksdklsdl sklsd sdklsdsd"},{"date":"04-11-2015 12:23:30","note":"adfadf"}]

                //return $invoice->note;
                if($invoice->note=="")
                {
                    $nota = array();
                        $nota[0] = [
                            'date' => date('d-m-Y H:i:s'),
                            'note' => "<b>".Auth::user()->first_name." ".Auth::user()->last_name."</b>: ".trim(Input::get('nota'))
                        ];
                }
                else{
                    $nota = json_decode($invoice->note);

                        $nota_add = [
                            'date' => date('d-m-Y H:i:s'),
                            'note' => "<b>".Auth::user()->first_name." ".Auth::user()->last_name."</b>: ".trim(Input::get('nota'))
                        ];
                    array_push($nota, $nota_add);
                }
                $invoice->note = json_encode($nota);
                //print_r($invoice->note);
                //return 0;
                $invoice->save();

		$account = Account::find(Auth::user()->account_id);
		//return $invoice['id'];
		$products = InvoiceItem::where('invoice_id',$invoice->id)->get();

		$invoice['invoice_items']=$products;
		$invoice['third']=$invoice->type_third;
		$invoice['is_uniper'] = $account->is_uniper;
		$invoice['uniper'] = $account->uniper;
		$invoice['logo'] = $invoice->getLogo();

		$client_id = $invoice->getClient();
		$client = DB::table('clients')->where('id','=', $client_id)->first();
		$contacts = Contact::where('client_id',$client->id)->get(array('id','is_primary','first_name','last_name','email'));
                $nota = json_decode($invoice['note']);
		$data = array(
			'invoice' => $invoice,
			'account'=> $account,
			'products' => $products,
			'contacts' => $contacts,
                        'nota'      => $nota,
                        'copia'     => 0,
                        'matriz'    => Branch::scope(1)->first()
		);
		// return Response::json($data);
		return View::make('factura.show',$data);
        }

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($publicId)
	{
		return InvoiceController::save($publicId);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function bulk($entityType = ENTITY_INVOICE)
	{
		$action = Input::get('action');
		$statusId = Input::get('statusId');
		$ids = Input::get('id') ? Input::get('id') : Input::get('ids');
		if($action == 'delete')
		{
			$invoices = Invoice::withTrashed()->scope($ids)->get();
			foreach ($invoices as $invoice)
			{
				BookSale::deleteBook($invoice);
			}
		}
		//$count = $this->invoiceRepo->bulk($ids, $action, $statusId);

 		if ($count > 0)
 		{
 			$key = $action == 'mark' ? "updated_{$entityType}" : "{$action}d_{$entityType}";
			$message = Utils::pluralize($key, $count);
			Session::flash('message', $message);
		}

		return Redirect::to("{$entityType}");
	}






	public function listasCuenta()
        {
            $user_id = Auth::user()->getAuthIdentifier();

            $user = DB::table('users')->select('first_name','last_name')->where('id',$user_id)->first();

            $branch = DB::table('branches')->where('account_id',Auth::user()->account_id)->where('id','=',Auth::user()->branch_id)->first();

            $user->branch = $branch->name;

            $categories = DB::table('categories')->where('account_id',Auth::user()->account_id)->get(array('name'));

            $cats = $categories;

            $categories2 = DB::table('categories')->where('account_id',Auth::user()->account_id)->get();
            $products2 = DB::table('products')->where('account_id','=',Auth::user()->account_id)->get();

                    $aux=array();

            foreach ($categories2 as $category)
            {
                    foreach ($products2 as $product)
                    {

                                    $pts = DB::table('products')->where('category_id',$category->id)->where('account_id','=',Auth::user()->account_id)->get(array('id','product_key','notes','cost'));
                                    $prod = array($category->name => $pts);
                    }
                            $aux += $prod;
            }

            $mensaje = array(
                            'productos'=> $aux,
                            'categorias' => $categories,
                            'first_name'=>$user->first_name,
                            'last_name'=>$user->last_name,
                            'branch'=>$user->branch
                    );

            return Response::json($mensaje);

    }
    public function copia($id){
              $invoice = Invoice::where('id',$id)->first(
                array(
                'id',
                'account_name',
                'account_nit',
                'account_uniper',
                'account_uniper',
                'address1',
                'address2',
                'terms',
                'importe_neto',
                'importe_total',
                'branch_name',
                'city',
                'client_id',
                'client_name',
                'client_nit',
                'control_code',
                'deadline',
                'discount',
                'economic_activity',
                'end_date',
                'invoice_date',
                'invoice_status_id',
                'invoice_number',
                'number_autho',
                'phone',
                'public_notes',
                'qr',
                'logo',
                'public_id',
                'note',
                'sfc',
                'type_third',
                'branch_id',
                'state',
                'law',
                'phone',
                'javascript')
                );


		$account = Account::find(Auth::user()->account_id);
		//return $invoice['id'];
		$products = InvoiceItem::where('invoice_id',$invoice->id)->get();

		$invoice['invoice_items']=$products;
		$invoice['third']=$invoice->type_third;
		$invoice['is_uniper'] = $account->is_uniper;
		$invoice['uniper'] = $account->uniper;
		$invoice['logo'] = $invoice->getLogo();

		$client_id = $invoice->getClient();
		$client = DB::table('clients')->where('id','=', $client_id)->first();
		$contacts = Contact::where('client_id',$client->id)->get(array('id','is_primary','first_name','last_name','email'));
                $status=  InvoiceStatus::where('id',$invoice->invoice_status_id)->first();
		//echo $client_id;
		//print_r($contacts);
	//	return 0;
                if($invoice->note=="")
                    $nota = [];
                else
                    $nota = json_decode($invoice['note']);

		$data = array(
			'invoice' => $invoice,
			'account'=> $account,
			'products' => $products,
			'contacts' => $contacts,
                        'nota'      => $nota,
                        'copia'     => 1,
                        'matriz'    => Branch::scope(1)->first(),
                        'status'    => $status->name=="Parcial"?"Parcialmente Pagado":$status->name
		);
		// return Response::json($data);
		return View::make('factura.show',$data);

    }
    public function anular($publicId){
			// return $publicId;30
        $invoice = Invoice::where('account_id','=', Auth::user()->account_id)->where('id','=',$publicId)->first();
        $invoice->invoice_status_id = 6;
        $invoice->save();
				return Redirect::to('factura');
  //       $invoices = Invoice::where('account_id',Auth::user()->account_id)->orderBy('public_id', 'DESC')->get();
	// return View::make('factura.index', array('invoices' => $invoices));
    }

    public function importar(){
        return View::make('factura.import');
    }

    public function excel(){
        //print_r(Input::get('excel'));
        //return 0;
        //return View::make('factura.import');
        $dir = "files/excel/";
        $fecha = base64_encode("excel".date('d/m/Y-H:m:i'));
        $file_name = $fecha;
        //return $file_name;



        $file = Input::file('excel');
        $destinationPath = 'files/excel/';
        // If the uploads fail due to file system, you can try doing public_path().'/uploads'
        $filename = $file_name;//str_random(12);
        //$filename = $file->getClientOriginalName();
        //$extension =$file->getClientOriginalExtension();
        $upload_success = Input::file('excel')->move($destinationPath, $filename);

//        if( $upload_success ) {
//           return Response::json('success', 200);
//        } else {
//           return Response::json('error', 400);
//        }


//        return 0;
        $results = Excel::selectSheetsByIndex(0)->load($dir.$file_name)->get();
        $factura = array();
        $groups = array();
        //shattering file gotten
        $nit="";
       foreach ($results as $key => $res){
          $dato=[];
          //$nit = "";
          foreach ($res as $r){
              if($r!="")
                array_push($dato, $r);
          }
          //$nit = $dato[0];
          if($dato){

            if($nit == $dato[0]){
                array_push ($groups, $dato);
            }
            else{
                if($groups)
                {
                    $bbr = [
                            'id'=>$groups[0][0],
                            'nit'=>$groups[0][1],
                            'nota'=>$groups[0][5]
                        ];
                    $products=array();;
                    foreach ($groups as $gru)
                    {
                        $pro['product_key']=$gru[2];
                        $pro['description']=$gru[3];
                        $pro['cost']=$gru[4];
                        array_push($products, $pro);
                    }
                    $bbr['products']=$products;
                    array_push($factura, $bbr);
                }
                $groups = array();
                $nit = $dato[0];
                array_push ($groups, $dato);
            }
          }
        }
        if($groups)
        {
            $bbr = [
                    'id'=>$groups[0][0],
                    'nit'=>$groups[0][1],
                    'nota'=>$groups[0][5]
                ];
            $products=array();;
            foreach ($groups as $gru)
            {
                $pro['product_key']=$gru[2];
                $pro['description']=$gru[3];
                $pro['cost']=$gru[4];
                array_push($products, $pro);
            }
            $bbr['products']=$products;
            array_push($factura, $bbr);
        }

        $returnable = $this->validateShatterExcel($factura);
        if($returnable!=""){
            Session::flash('error',$returnable);
            return View::make('factura.import');
        }
        //echo $returnable;
        //return  0;
        $cont = 0;
        foreach ($factura as $fac){
            $this->saveLote($fac);
            $cont ++;
        }


        Session::flash('message','Se importaron '.$cont.' facturas exitósamente');
        $invoices = Invoice::where('account_id',Auth::user()->account_id)->where('branch_id',Session::get('branch_id'))->orderBy('public_id', 'DESC')->get();
        return View::make('factura.index', array('invoices' => $invoices));
        return 0;
    }

    private function saveLote($factura){
        $account = DB::table('accounts')->where('id','=', Auth::user()->account_id)->first();
        $branch = Branch::find(Session::get('branch_id'));
       // return $factura['nit'];
        $client=  Client::where('account_id','=', Auth::user()->account_id)->where('public_id',$factura['id'])->first();
        //if(!$client)
         //   return $factura['nit'];

        $invoice = Invoice::createNew();
        $invoice->setPublicNotes($factura['nota']);
        $invoice->setBranch(Session::get('branch_id'));
        $invoice->setInvoiceDate(date("Y-m-d"));
        $invoice->setClient($client->id);
        $invoice->setEconomicActivity($branch->economic_activity);
        $invoice->setDiscount(0);
        $invoice->setClientName($client->name);
        $invoice->setClientNit($client->nit);

        $invoice->setUser(Auth::user()->id);
        //$date=date("Y-m-d",strtotime(Input::get('invoice_date')));
        //$invoice->setInvoiceDate($date);
        $total_cost = 0;
        foreach ($factura['products'] as $producto)
        {
            //$pr = Product::where('account_id',Auth::user()->account_id)->where('product_key',$producto['product_key'])->first();
            //$total_cost+= $pr->cost*$producto['qty'];
            $total_cost+= $producto['cost'];
        }


        $invoice->importe_neto = trim($total_cost);
        $invoice->importe_total=trim($total_cost);

        //$invoice->note = trim(Input::get('nota'));


        $invoice->setAccountName($account->name);
        $invoice->setAccountNit($account->nit);
        $invoice->setBranchName($branch->name);
        $invoice->setAddress1($branch->address1);
        $invoice->setAddress2($branch->address2);
        $invoice->setPhone($branch->work_phone);
        $invoice->setCity($branch->city);
        $invoice->setState($branch->state);
        $invoice->setNumberAutho($branch->number_autho);
        $invoice->setKeyDosage($branch->key_dosage);
        $invoice->setTypeThird($branch->type_third);
        $invoice->setDeadline($branch->deadline);
        $invoice->setLaw($branch->law);

        $type_document =TypeDocument::where('account_id',Auth::user()->account_id)->first();
        $invoice->invoice_number = branch::getInvoiceNumber();

         $numAuth = $invoice->number_autho;
         $numfactura = $invoice->invoice_number;
         $nit = $invoice->client_nit;
         $fechaEmision =date("Ymd",strtotime($invoice->invoice_date));
         $total = $invoice->importe_total;
         $llave = $branch->key_dosage;
         $codigoControl = Utils::getControlCode($numfactura,$nit,$fechaEmision,$total,$numAuth,$llave);
        $invoice->setControlCode($codigoControl);
         $documents = TypeDocumentBranch::where('branch_id',$invoice->branch_id)->orderBy('id','ASC')->get();
        foreach ($documents as $document)
        {
            $actual_document = TypeDocument::where('id',$document->type_document_id)->first();
            if($actual_document->master_id==1)
            $id_documento = $actual_document->id;
        }
        $invoice->setJavascript($id_documento);
        $invoice->logo = 1;


        $invoice->sfc = $branch->sfc;
        //$invoice->qr =$invoice->account_nit.'|'.$invoice->invoice_number.'|'.$invoice->number_autho.'|'.$invoice->invoice_date.'|'.$invoice->importe_neto.'|'.$invoice->importe_total.'|'.$invoice->client_nit.'|'.$invoice->importe_ice.'|0|0|'.$invoice->descuento_total;
        if($account->is_uniper)
        {
                $invoice->account_uniper = $account->uniper;
        }

        //$invoice->logo = $type_document->logo;
        $invoice->save();

        foreach ($factura['products']  as $producto)
        {
            $product = Product::where('account_id',Auth::user()->account_id)->where('product_key',$producto['product_key'])->first();
            if($product!=null){

                    $invoiceItem = InvoiceItem::createNew();
                    $invoiceItem->setInvoice($invoice->id);
                    $invoiceItem->setProduct($product->id);
                    $invoiceItem->setProductKey($product->product_key);
                    $invoiceItem->setNotes($product->notes." ".$producto['description']);
                    $invoiceItem->setCost($producto['cost']);
                    $invoiceItem->setQty(1);
                    $invoiceItem->save();
            }
        }
    }
    public function controlCode(){
        $numAuth = Input::get('cc_auth');
        $numfactura = Input::get('cc_invo');
        $nit = Input::get('cc_nit');
        $fechaEmision = date("Ymd",strtotime(Input::get('cc_date')));
        $total = Input::get('cc_tota');
        $llave = Input::get('cc_key');
        //return json_encode(Input::all());
        $codigoControl = Utils::getControlCode($numfactura,$nit,$fechaEmision,$total,$numAuth,$llave);
        return $codigoControl;
    }

    public function export(){
        $date="11";
        $output = fopen('php://output','w') or Utils::fatalError();
        header('Content-Type:application/txt');
        header('Content-Disposition:attachment;filename=export.txt');
        $invoices=Invoice::select('client_nit','client_name','invoice_number','account_nit','invoice_date','importe_total','importe_ice','importe_exento','importe_neto','debito_fiscal','invoice_status_id','control_code')->where('account_id',Auth::user()->account_id)->where('invoice_date','LIKE','%'.$date.'%')->get();
        $p="|";

        foreach ($invoices as $i){
            if($i->invoice_status_id==6)$status="A";
        else $status="V";
            $datos = $i->client_nit.$p.$i->client_name.$p.$i->invoice_number.$p.$i->account_nit.$p.$i->invoice_date.$p.$i->importe_total.$p.$i->importe_ice.$p.$i->importe_exento.$p.$i->importe_neto.$p.$i->debito_fiscal.$p.$status.$p.$i->control_code."\r\n";
            fputs($output,$datos);
        }
        fclose($output);
		exit;
    }
    private function validateShatterExcel($invoices){
        $stackAstray = "";
        foreach($invoices as $invoice){
            $client = Client::where('account_id',Auth::user()->account_id)->where('public_id',$invoice['id'])->where('nit',$invoice['nit'])->first();
            if(!$client)
                $stackAstray.="El cliente ".$invoice['id']." con NIT: ".$invoice['nit']." no existe<br>";
            foreach ($invoice['products'] as $pro)
            {

                $product= Product::where('account_id',Auth::user()->account_id)->where('product_key',$pro['product_key'])->first();
                if(!$product)
                    $stackAstray.="El producto con codigo: ".$pro['product_key']." no existe<br>";
//                else
//                    echo $product->name;
            }
        }
        if($stackAstray !="")
            $stackAstray.="Revise el documento y vuelva a intentar.";
        return $stackAstray;
    }
    //index
	public function index($name = null, $numero = null, $fecha = null, $total = null, $estado = null)
	{

	 $name = Input::get('name');
	 $numero = Input::get('numero');
	 $fecha = Input::get('fecha');
	 $total = Input::get('total');
	 $estado = Input::get('estado');

	Session::put('sw','DESC');

	 if(!$numero && !$name && !$fecha && !$total && !$estado)
	 {
		$invoices= Invoice::join('invoice_statuses', 'invoices.invoice_status_id', '=', 'invoice_statuses.id')
												->select('invoices.client_id','invoices.invoice_number','invoices.id', 'invoices.client_name', 'invoices.invoice_date', 'invoices.importe_total', 'invoice_statuses.name')
												->where('invoices.branch_id',Session::get('branch_id'))
												->orderBy(DB::raw('convert(invoices.invoice_number,unsigned integer)'), 'DESC')
												->simplePaginate(50);
		return View::make('factura.index', array('invoices' => $invoices, 'sw'=>'ASC'));
	 }
	 if ($numero) {

		 $invoices= Invoice::join('invoice_statuses', 'invoices.invoice_status_id', '=', 'invoice_statuses.id')
 												->select('invoices.client_id','invoices.invoice_number','invoices.id', 'invoices.client_name', 'invoices.invoice_date', 'invoices.importe_total', 'invoice_statuses.name')
												->where('invoices.branch_id',Session::get('branch_id'))
												->where('invoices.invoice_number','like', $numero."%")
 												->orderBy(DB::raw('convert(invoices.invoice_number,unsigned integer)'), 'DESC')
 												->simplePaginate(50);

		$data = [
			'invoices' => $invoices,
			'numero' => $numero
		];
		return View::make('factura.index', $data, array('sw'=>'ASC'));
	}

	 if ($name) {

		 $invoices= Invoice::join('invoice_statuses', 'invoices.invoice_status_id', '=', 'invoice_statuses.id')
 											 ->select('invoices.client_id','invoices.invoice_number','invoices.id', 'invoices.client_name', 'invoices.invoice_date', 'invoices.importe_total', 'invoice_statuses.name')
											 ->where('invoices.branch_id',Session::get('branch_id'))
 											 ->where('invoices.client_name','like', $name."%")
 											 ->orderBy('invoices.client_name', 'DESC')
 											 ->simplePaginate(50);

		 	 $data = [
		 		 'invoices' => $invoices,
		 		 'name' => $name
		 	 ];
		 	 return View::make('factura.index', $data, array('sw'=>'ASC'));
		}

		if ($fecha) {
			$invoices= Invoice::join('invoice_statuses', 'invoices.invoice_status_id', '=', 'invoice_statuses.id')
												->select('invoices.client_id','invoices.invoice_number','invoices.id', 'invoices.client_name', 'invoices.invoice_date', 'invoices.importe_total', 'invoice_statuses.name')
												->where('invoices.branch_id',Session::get('branch_id'))
												->where('invoices.invoice_date','like', $fecha."%")
												->orderBy('invoices.invoice_date', 'DESC')
												->simplePaginate(50);

				$data = [
					'invoices' => $invoices,
					'fecha' => $fecha
				];
				return View::make('factura.index', $data, array('sw'=>'ASC'));
		}

		if ($total) {
			$invoices= Invoice::join('invoice_statuses', 'invoices.invoice_status_id', '=', 'invoice_statuses.id')
												->select('invoices.client_id','invoices.invoice_number','invoices.id', 'invoices.client_name', 'invoices.invoice_date', 'invoices.importe_total', 'invoice_statuses.name')
												->where('invoices.branch_id',Session::get('branch_id'))
												->where('invoices.importe_total','like', $total."%")
												->orderBy(DB::raw('convert(invoices.importe_total,unsigned integer)'), 'DESC')
												
												->simplePaginate(50);

				$data = [
					'invoices' => $invoices,
					'total' => $total
				];
				return View::make('factura.index', $data, array('sw'=>'ASC'));
		}

		if ($estado) {
			$invoices= Invoice::join('invoice_statuses', 'invoices.invoice_status_id', '=', 'invoice_statuses.id')
												->select('invoices.client_id','invoices.invoice_number','invoices.id', 'invoices.client_name', 'invoices.invoice_date', 'invoices.importe_total', 'invoice_statuses.name')
												->where('invoices.branch_id',Session::get('branch_id'))
												->where('invoice_statuses.name','like', $estado."%")
												->orderBy('invoice_statuses.name', 'DESC')
												->simplePaginate(50);
				$data = [
					'invoices' => $invoices,
					'estado' => $estado
				];
				return View::make('factura.index', $data, array('sw'=>'ASC'));
		}
 }

	 public function indexDown($name = null, $numero = null, $fecha = null, $total = null, $estado = null)
	{

		$name = Input::get('name');
		$numero = Input::get('numero');
		$fecha = Input::get('fecha');
		$total = Input::get('total');
		$estado = Input::get('estado');

		Session::put('sw','ASC');
		// if(Session::get('sw')=='DESC')
		// {
		// 	Session::put('sw','ASC');
		// }
		// else {
		// 	Session::put('sw','DESC');
		// }
		$sw = Session::get('sw');

		if(!$numero && !$name && !$fecha && !$total && !$estado)
 	 {
 		$invoices= Invoice::join('invoice_statuses', 'invoices.invoice_status_id', '=', 'invoice_statuses.id')
 												->select('invoices.client_id','invoices.invoice_number','invoices.id', 'invoices.client_name', 'invoices.invoice_date', 'invoices.importe_total', 'invoice_statuses.name')
												->where('invoices.branch_id',Session::get('branch_id'))
 												
 												->orderBy(DB::raw('convert(invoices.invoice_number,unsigned integer)'), $sw)
 												->simplePaginate(50);
 		return View::make('factura.index', array('invoices' => $invoices, 'sw'=>'ASC'));
 	 }
 	 if ($numero) {

 		 $invoices= Invoice::join('invoice_statuses', 'invoices.invoice_status_id', '=', 'invoice_statuses.id')
  												->select('invoices.client_id','invoices.invoice_number','invoices.id', 'invoices.client_name', 'invoices.invoice_date', 'invoices.importe_total', 'invoice_statuses.name')
													->where('invoices.branch_id',Session::get('branch_id'))
 											  	->where('invoices.invoice_number','like', $numero."%")
  												->orderBy(DB::raw('convert(invoices.invoice_number,unsigned integer)'), $sw)
  												->simplePaginate(50);

 		$data = [
 			'invoices' => $invoices,
 			'numero' => $numero
 		];
 		return View::make('factura.index', $data);
 	}

 	 if ($name) {

 		 $invoices= Invoice::join('invoice_statuses', 'invoices.invoice_status_id', '=', 'invoice_statuses.id')
  											 ->select('invoices.client_id','invoices.invoice_number','invoices.id', 'invoices.client_name', 'invoices.invoice_date', 'invoices.importe_total', 'invoice_statuses.name')
												 ->where('invoices.branch_id',Session::get('branch_id'))
  											 ->where('invoices.client_name','like', $name."%")
  											 ->orderBy('invoices.client_name', $sw)
  											 ->simplePaginate(50);

 		 	 $data = [
 		 		 'invoices' => $invoices,
 		 		 'name' => $name
 		 	 ];
 		 	 return View::make('factura.index', $data);
 		}

 		if ($fecha) {
 			$invoices= Invoice::join('invoice_statuses', 'invoices.invoice_status_id', '=', 'invoice_statuses.id')
 												->select('invoices.client_id','invoices.invoice_number','invoices.id', 'invoices.client_name', 'invoices.invoice_date', 'invoices.importe_total', 'invoice_statuses.name')
												->where('invoices.branch_id',Session::get('branch_id'))
 												->where('invoices.invoice_date','like', $fecha."%")
 												->orderBy('invoices.invoice_date', $sw)
 												->simplePaginate(50);

 				$data = [
 					'invoices' => $invoices,
 					'fecha' => $fecha
 				];
 				return View::make('factura.index', $data);
 		}

 		if ($total) {
 			$invoices= Invoice::join('invoice_statuses', 'invoices.invoice_status_id', '=', 'invoice_statuses.id')
 												->select('invoices.client_id','invoices.invoice_number','invoices.id', 'invoices.client_name', 'invoices.invoice_date', 'invoices.importe_total', 'invoice_statuses.name')
												->where('invoices.branch_id',Session::get('branch_id'))
 												->where('invoices.importe_total','like', $total."%")
 												->orderBy(DB::raw('convert(invoices.importe_total,unsigned integer)'), $sw)
 												
 												->simplePaginate(50);

 				$data = [
 					'invoices' => $invoices,
 					'total' => $total
 				];
 				return View::make('factura.index', $data);
 		}

 		if ($estado) {
 			$invoices= Invoice::join('invoice_statuses', 'invoices.invoice_status_id', '=', 'invoice_statuses.id')
 												->select('invoices.client_id','invoices.invoice_number','invoices.id', 'invoices.client_name', 'invoices.invoice_date', 'invoices.importe_total', 'invoice_statuses.name')
												->where('invoices.branch_id',Session::get('branch_id'))
 												->where('invoice_statuses.name','like', $estado."%")
 												->orderBy('invoice_statuses.name', $sw)
 												->simplePaginate(50);
 				$data = [
 					'invoices' => $invoices,
 					'estado' => $estado
 				];
 				return View::make('factura.index', $data);
 		}
	}
}
