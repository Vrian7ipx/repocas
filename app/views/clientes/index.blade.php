@extends('header')
@section('title') Gestión de Clientes @stop
@section('head') @stop
@section('encabezado') CLIENTES @stop
@section('encabezado_descripcion') Gestión de Clientes @stop
@section('nivel') <li><a href="#"><i class="ion-person-stalker"></i> Clientes</a></li> @stop

@section('content')

<div class="panel panel-default">
  <div class="box-header with-border">
    <h3 class="box-title"><a href="{{ url('clientes/create') }}" class="btn btn-success" role="button">Nuevo Cliente &nbsp<span class="glyphicon glyphicon-plus-sign"></span></a>  </h3>
        <div class="box-body">
              <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
              <div class="row"><div class="col-sm-6">

              </div>
              <div class="col-sm-6"></div>
              </div>
              <div class="row">
              <div class="col-sm-12">
              <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                <thead>
                  <tr>
                      <td><input placeholder="Código" id="numero" value="{{ $numero }}"></input></td>
                      <td><input placeholder="NIT/CI" id="nit" value="{{ $nit }}"></input></td>
                      <td><input placeholder="Razon Social" id="name" value="{{ $name }}"></input></td>
                      <td><input placeholder="Contacto" id="contact" value="{{ $contact }}"></input></td>
                      <td><input placeholder="Agrupación" id="group" value="{{ $group }}"></input></td>
                      <td  style="display:none">Acción</td>
                  </tr>
                </thead>
                <thead>
                          <tr>
                              <th>Código <button  style="text-decoration:none;color:#000;" id="dnumero"> <i class="glyphicon glyphicon-sort"></i></button></th>
                              <th>NIT/CI <button  style="text-decoration:none;color:#000;" id="dnit"><i class="glyphicon glyphicon-sort"></i></button></th>
                              <th>Razón Social <button style="text-decoration:none;color:#000;" id="dname"><i class="glyphicon glyphicon-sort"></i></button></th>
                              <th>Contacto <button  style="text-decoration:none;color:#000;" id="dcontact"><i class="glyphicon glyphicon-sort"></i></button></th>
                              <th>Agrupación <button  style="text-decoration:none;color:#000;" id="dgroup"><i class="glyphicon glyphicon-sort"></i></button></th>
                              <th style = "display:block">&nbsp;&nbsp;&nbsp;&nbsp;Acción</th>
                          </tr>
                  </thead>
                <tbody>

                @foreach($clients as $client)
                    <tr role="row">
                        <td>{{ $client->id }}</td>
                        <td><a href="{{URL::to('clientes/'.$client->id)}}">{{ $client->nit}}</a></td>
                        <td><a href="{{URL::to('clientes/'.$client->id)}}">{{ $client->business_name }}</a></td>
                        <td>{{ $client->contacto_first_name }} {{ $client->contacto_last_name}}</td>
                        <!-- <td>{{ $client->group_id }}</td> -->
                        <td>{{ Group::find($client->group_id)->name }}</td>
                        <td>
                          <a class="btn btn-primary btn-xs" data-task="view" href="{{ URL::to("clientes/".$client->id) }}"  style="text-decoration:none;color:white;"><i class="glyphicon glyphicon-eye-open"></i></a>
                          <a class="btn btn-warning btn-xs" href="{{ URL::to("clientes/".$client->id.'/edit') }}" style="text-decoration:none;color:white;"><i class="glyphicon glyphicon-edit"></i></a>
                          <!--no <a class="btn btn-danger btn-xs" onclick="$(this).closest('form').submit()" type="submit" style="text-decoration:none;color:white;"><i class="glyphicon glyphicon-remove"></i></a> -->
                        </td>

                    </tr>

                @endforeach

              <!-- </tbody>
                <tfoot>
                <tr><th rowspan="1" colspan="1">Rendering engine</th>
                <th rowspan="1" colspan="1">Browser</th>
                <th rowspan="1" colspan="1">Platform(s)</th>
                <th rowspan="1" colspan="1">Engine version</th>
                <th rowspan="1" colspan="1">CSS grade</th></tr>
                </tfoot> -->
              </table>

              @if($numero != "")
              <center><div class="pagination"> {{ $clients->appends(array('numero' => $numero))->links(); }} </div></center>
              @endif
              @if($nit != "")
              <center><div class="pagination"> {{ $clients->appends(array('nit' => $nit))->links(); }} </div></center>
              @endif
              @if($name != "")
              <center><div class="pagination"> {{ $clients->appends(array('name' => $name))->links(); }} </div></center>
              @endif
              @if($group != "")
              <center><div class="pagination"> {{ $clients->appends(array('group' => $group))->links(); }} </div></center>
              @endif
              @if($contact != "")
              <center><div class="pagination"> {{ $clients->appends(array('contact' => $contact))->links(); }} </div></center>
              @endif
              @if($numero == "" && $name == "" && $nit == "" && $group == "" && $contact == "")
              <center><div class="pagination"> {{ $clients->links(); }} </div></center>
              @endif
              </div>
          </div>
        </div>
      </div>



  </div>
</div>


<script>

$('#dnumero').click(function(){

  numero = $("#numero").val();

  var sw = '{{Session::get('sw')}}';
  console.log('variable sw '+sw);

  // console.log(numero);
  window.open('{{URL::to('clientesDown')}}'+'?numero='+numero, "_self");
});

$('#dname').click(function(){
  name = $("#name").val();
  var sw = '{{Session::get('sw')}}';
  console.log('variable sw '+sw);
  window.open('{{URL::to('clientesDown')}}'+'?name='+name, "_self");
});


$('#dnit').click(function(){
  nit = $("#nit").val();
  var sw = '{{Session::get('sw')}}';
  console.log('variable sw '+sw);
  window.open('{{URL::to('clientesDown')}}'+'?nit=' +nit, "_self");
});

$('#dgroup').click(function(){
  grupo = $("#group").val();
  var sw = '{{Session::get('sw')}}';
  console.log('variable sw '+sw);
  window.open('{{URL::to('clientesDown')}}'+'?group=' +grupo, "_self");
});

$('#dcontact').click(function(){
  contact = $("#contact").val();
  var sw = '{{Session::get('sw')}}';
  console.log('variable sw '+sw);
  window.open('{{URL::to('clientesDown')}}'+'?contact=' +contact, "_self");
});



$('#name').keypress(function(event){
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if(keycode == '13'){
        // alert('You pressed a "enter" key in textbox');
        console.log("Enter");
        name = $("#name").val();
        window.open('{{URL::to('clientes')}}'+'?name=' +name, "_self");
    }
});

$('#numero').keypress(function(event){
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if(keycode == '13'){
        console.log("Enter");
        numero = $("#numero").val();
        window.open('{{URL::to('clientes')}}'+'?numero=' +numero, "_self");
    }
});

$('#nit').keypress(function(event){
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if(keycode == '13'){
        console.log("Enter");
        nit = $("#nit").val();
        window.open('{{URL::to('clientes')}}'+'?nit=' +nit, "_self");
    }
});

$('#group').keypress(function(event){
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if(keycode == '13'){
        grupo = $("#group").val();
        window.open('{{URL::to('clientes')}}'+'?group=' +grupo, "_self");
    }
});

$('#contact').keypress(function(event){
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if(keycode == '13'){
        contact = $("#contact").val();
        window.open('{{URL::to('clientes')}}'+'?contact=' +contact, "_self");
    }
});



</script>


@stop
