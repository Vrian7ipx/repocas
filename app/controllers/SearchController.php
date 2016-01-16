<?php

class SearchController extends \BaseController {

public function getClients(){

  $clientes= Client::where('account_id', Auth::user()->account_id)->select('id', 'name', 'nit', 'custom_value4', 'work_phone')->orderBy('name', 'ASC')->get();
  //$clientes= Client::where('account_id', Auth::user()->account_id)->orderBy('name', 'ASC')->get();
  //return $clientes;
  //return Response::json($clientes);
  foreach ($clientes as $key => $client) {
    $client->name2 = "<a href='clientes/$client->public_id'>$client->name</a>";
    $client->nit2 = "<a href='clientes/$client->public_id'>$client->nit</a>";
    $client->campo = "<a href='clientes/$client->public_id'>$client->custom_value4</a>";
    $client->button = "<a class='btn btn-primary btn-xs' data-task='view' href='clientes/$client->public_id'  style='text-decoration:none;color:white;'><i class='glyphicon glyphicon-eye-open'></i></a> <a class='btn btn-warning btn-xs' href='clientes/$client->public_id/edit' style='text-decoration:none;color:white;'><i class='glyphicon glyphicon-edit'></i></a>";

}
  $clientJson = ['data'=>$clientes];

  return Response::json($clientJson);

}

public function getProducts(){
  $products = Product::select('id','product_key', 'notes', 'pack_types',  'is_product', 'ice','units','cc')->orderBy('product_key', 'ASC')->get();
  foreach ($products as $key => $product) {                  
      $product->pack_types = $product->pack_types==1?"Vidrio":"Plástico";
      $product->ice = $product->ice?"SÍ":"NO";
      $product->accion = "<a class='btn btn-primary btn-xs' data-task='view' href='productos/$product->id'  style='text-decoration:none;color:white;'><i class='glyphicon glyphicon-eye-open'></i></a> <a class='btn btn-warning btn-xs' href='productos/$product->id/edit' style='text-decoration:none;color:white;'><i class='glyphicon glyphicon-edit'></i></a>";
  }
  $productJson = ['data'=>$products];
  return Response::json($productJson);
}

public function getInvoices(){
  $invoices = Invoice::where('account_id', Auth::user()->account_id)->where('branch_id', Session::get('branch_id'))->select('public_id','invoice_status_id','client_id', 'invoice_number','invoice_date','importe_total','branch_name')->orderBy('public_id','DESC')->get();
  foreach ($invoices as $key => $invoice) {
      $invoice_razon = Client::where('account_id', Auth::user()->account_id)->select('name')->where('id', $invoice->client_id)->first();
      $invoice->razon = $invoice_razon->name;
      $estado = InvoiceStatus::where('id', $invoice->invoice_status_id)->first();
      $invoice->estado = $estado->name;
      $invoice->accion = "<a class='btn btn-primary btn-xs' data-task='view' href='factura/$invoice->public_id'  style='text-decoration:none;color:white;'><i class='glyphicon glyphicon-eye-open'></i></a> <a class='btn btn-warning btn-xs' href='copia/$invoice->public_id' style='text-decoration:none;color:white;'><i class='glyphicon glyphicon-duplicate'></i></a>";
  }
  $invoiceJson = ['data'=>$invoices];
  return Response::json($invoiceJson);

}

public function llenarClients(){

    for ($i=0; $i < 50; $i++) {
      $client = Client::createNew();
        $client->setNit($i);
        $client->setName('cliente'.$i);
        $client->setBussinesName('nuevo'.$i);
        $client->setWorkPhone('22454545');
        $client->save();
   }
  return $client;
  }
}
