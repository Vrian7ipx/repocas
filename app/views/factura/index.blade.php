@extends('header')
@section('title')Gestión de Facturas @stop
  @section('head') @stop
@section('encabezado')  FACTURAS @stop
@section('encabezado_descripcion') Gestión de Facturas  @stop
@section('nivel') <li><a href="#"><i class="fa fa-files-o"></i> Facturas</a></li> @stop

@section('content')

<div class="panel panel-default">
  <div class="box-header with-border">
     <h3 class="box-title"><a href="{{ url('factura/create') }}" class="btn btn-success" role="button">Nueva Factura&nbsp<span class="glyphicon glyphicon-plus-sign"></span></a></h3>
    <div class="box-tools pull-right">
    </div><!-- /.box-tools -->
  </div><!-- /.box-header -->


  <div class="table-responsive">
		<table id="datatable" class="table table-striped table-hover" cellspacing="0" cellpadding="0" width="100%" style="margin-left:24px;">
          <thead>
              <tr>
                  <!--<td><input class="selectAll" type="checkbox"></td>-->
                  <td><input placeholder="Nº" id="numero" value="{{ $numero }}"></td>
                  <td><input placeholder="Cliente" id="name" value="{{ $name }}"></input></td>
                  <td><input placeholder="fecha" id="fecha" value="{{ $fecha }}"></input></td>
                  <td><input placeholder="total" id="total" value="{{ $total }}"></input></td>
                  <td><input placeholder="estado" id="estado" value="{{ $estado }}"></input></td>
                  <td style = "display:none">Acción</td>

              </tr>
          </thead>
			<thead>
              <tr>

                  <th id="numero2">Código <button  style="text-decoration:none;color:#000;" id="dnumero"> <i class="glyphicon glyphicon-sort"></i></button></th>
                  <th id="name2">Cliente<button  style="text-decoration:none;color:#000;" id="dname"> <i class="glyphicon glyphicon-sort"></i></button></th>
                  <th id="fecha2">Fecha<button  style="text-decoration:none;color:#000;" id="dfecha"> <i class="glyphicon glyphicon-sort"></i></button></th>
                  <th id="total2">Total<button  style="text-decoration:none;color:#000;" id="dtotal"> <i class="glyphicon glyphicon-sort"></i></button></th>
                  <th id="estado2">Estado<button  style="text-decoration:none;color:#000;" id="destado"> <i class="glyphicon glyphicon-sort"></i></button></th>
                  <th style = "display:block">&nbsp;Acción</th>

              </tr>
          </thead>
           <tbody>

          @foreach($invoices as $invoice)
              <tr class="active">

                  <td>{{ $invoice->id}}</td>
                  <td ><a href="{{URL::to('clientes/'.Client::find($invoice->client_id)->id)}}">{{ $invoice->client_name }}</a></td>
                  <td>{{ $invoice->created_at }}</td>
                  <td>{{ $invoice->importe_total }}</td>

                  <td>{{ $invoice->name }}</td>

                  <td>
        		<a id="{{$invoice->invoice_number}}" class="btn btn-primary btn-xs jae" data-task="view" href="{{ URL::to("factura/".$invoice->id) }}"  style="text-decoration:none;color:white;"><i class="glyphicon glyphicon-eye-open" title="hola" ></i></a>
  		    	<a class="btn btn-warning btn-xs" data-task="view" data-toggle="tooltip" data-original-title="Default tooltip" href="{{ URL::to("copia/".$invoice->id) }}"  style="text-decoration:none;color:white;"><i class="glyphicon glyphicon-duplicate"></i></a>
                  </td>
              </tr>
          @endforeach
          </tbody>
        </table>

        @if($numero != "")
        <center><div class="pagination"> {{ $invoices->appends(array('numero' => $numero))->links(); }} </div></center>
        @endif
        @if($name != "")
        <center><div class="pagination"> {{ $invoices->appends(array('name' => $name))->links(); }} </div></center>
        @endif
        @if($fecha != "")
        <center><div class="pagination"> {{ $invoices->appends(array('fecha' => $fecha))->links(); }} </div></center>
        @endif
        @if($total != "")
        <center><div class="pagination"> {{ $invoices->appends(array('total' => $total))->links(); }} </div></center>
        @endif
        @if($estado != "")
        <center><div class="pagination"> {{ $invoices->appends(array('estado' => $estado))->links(); }} </div></center>
        @endif
        @if($numero == "" && $name == "" && $fecha == "" && $total == "" && $estado == "")
        <center><div class="pagination"> {{ $invoices->links(); }} </div></center>
        @endif

    </div>
</div>


<script type="text/javascript">

$('#numero').keypress(function(event){
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if(keycode == '13'){
        // alert('You pressed a "enter" key in textbox');
        console.log("Enter");
        numero = $("#numero").val();
        window.open('{{URL::to('factura')}}'+'?numero=' +numero, "_self");
    }
});

$('#name').keypress(function(event){
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if(keycode == '13'){
        // alert('You pressed a "enter" key in textbox');
        console.log("Enter");
        name = $("#name").val();
        window.open('{{URL::to('factura')}}'+'?name=' +name, "_self");
    }
});

$('#fecha').keypress(function(event){
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if(keycode == '13'){
        // alert('You pressed a "enter" key in textbox');
        console.log("Enter");
        fecha = $("#fecha").val();
        window.open('{{URL::to('factura')}}'+'?fecha=' +fecha, "_self");
    }
});

$('#total').keypress(function(event){
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if(keycode == '13'){
        // alert('You pressed a "enter" key in textbox');
        console.log("Enter");
        total = $("#total").val();
        window.open('{{URL::to('factura')}}'+'?total=' +total, "_self");
    }
});

$('#estado').keypress(function(event){
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if(keycode == '13'){
        // alert('You pressed a "enter" key in textbox');
        console.log("Enter");
        estado = $("#estado").val();
        window.open('{{URL::to('factura')}}'+'?estado=' +estado, "_self");
    }
});

$('#dnumero').click(function(){
  numero = $("#numero").val();
  var sw = '{{Session::get('sw')}}';
  window.open('{{URL::to('facturaDown')}}'+'?numero='+numero, "_self");
});
$('#dname').click(function(){
  name = $("#name").val();
  var sw = '{{Session::get('sw')}}';
  window.open('{{URL::to('facturaDown')}}'+'?name='+name, "_self");
});
$('#dfecha').click(function(){
  fecha = $("#fecha").val();
  var sw = '{{Session::get('sw')}}';
  window.open('{{URL::to('facturaDown')}}'+'?fecha='+fecha, "_self");
});
$('#dtotal').click(function(){
  total = $("#total").val();
  var sw = '{{Session::get('sw')}}';
  window.open('{{URL::to('facturaDown')}}'+'?total='+total, "_self");
});
$('#destado').click(function(){
  estado = $("#estado").val();
  var sw = '{{Session::get('sw')}}';
  window.open('{{URL::to('facturaDown')}}'+'?estado='+estado, "_self");
});


</script>

@stop
