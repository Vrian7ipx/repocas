@extends('header')
@section('title')Ver Factura @stop
@section('head')
                <style>
			#section {
    		width:350px;
    		float:left;
    		padding:10px;
    		background-color:#eeeeee;
			}
			#savesection {
    		width:350px;
    		float:left;
    		padding:10px;
    		background-color:#5cb85c;
			}
		</style>


@stop
@section('encabezado') FACTURAS @stop
@section('encabezado_descripcion') Ver Factura @stop
@section('nivel') <li><a href="{{URL::to('factura')}}"><i class="fa fa-files-o"></i> Facturas</a></li>
            <li class="active">Ver</li> @stop

@section('content')
<div class="box box-info">
   <div class="box-header with-border">
    <h3 class="box-title">Factura: {{ $invoice->invoice_number }}</h3>
    <div class="box-tools pull-right">
      <div class="cerrar" align="right">
        <!-- <a type="button"  class="btn btn-default btn-sm" href="{{asset('factura')}}" role="button"><span class="glyphicon glyphicon-circle-arrow-left" aria-hidden="true"></span>&nbsp;Salir</a> -->
    </div>
    </div>

  </div>
    <div class="box-body">


      <br><br>
      <form action="{{asset('enviarCorreo')}}" method="POST" name="correo" id="correo">
<!--      <div class="col-xs-12">-->

      <input type="hidden" name="id" value="{{ $invoice->id }}">
      <input type="hidden"  name="client" value="{{ $invoice->client_id }}">
      <input type="hidden"  name="date" value="{{ $invoice->invoice_date }}">
      <input type="hidden"  name="nit" value="{{ $invoice->client_nit }}">

      <div class="col-xs-12">
        <div class="btn-group btn-group-justified" role="group">
            <div class="btn-group col-xs-1"></div>
            <div class="btn-group col-xs-3" role="group">
              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#contacts">Enviar &nbsp;<span class="glyphicon glyphicon-send" aria-hidden="true"></span> </button>
            </div>
            <div class="btn-group col-xs-3" role="group">
              <a type="button"  class="btn btn-success" href="{{ URL::to('pagoCliente/'.$invoice->client_id.'/'.$invoice->id) }}" role="button" >Pagar &nbsp;<span class="glyphicon glyphicon-usd" aria-hidden="true"></span></a>
            </div>
            <div class="btn-group col-xs-3" role="group">
              <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#anular_modal">Anular&nbsp;&nbsp;<span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span></button>
            </div>
            <div class="btn-group col-xs-2"></div>
            <!--<div class="btn-group" role="group">
              <a type="button"  class="btn btn-default" href="{{asset('factura')}}" role="button" ><span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>&nbsp;Cerrar</a></div>
            </div>-->
        </div>
      </div>




      <br><br>
      <br><br>


        <div class="modal fade" id="contacts" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h4 class="modal-title" id="myModalLabel">ENVIAR A LOS SIGUIENTES CORREOS</h4>
                </div>
                <div class="modal-body col-xs-12">
                  <div id="contacts_row">
                    <input type='hidden' id='contact_id' value='' name='contactos[0][id]'>
                    <input  id='contact_name' placeholder="Nombre" value=''name='contactos[0][name]'>
                    <input  id='contact_mail' placeholder="Correo" value=''name='contactos[0][mail]'>
                    <input type='checkbox' name='contactos[0][checked]'>
                    <br><br>
                  </div>
                 </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button class="col-md-2 btn btn-small btn-primary" type="submit" >Enviar</button>
                    <!--<button class="col-md-2 btn btn-small btn-primary" type="submit" data-dismiss="modal">Enviar</button>-->
                  </div>
            </div>
           </div>
        </div>
      </form>
      <div class="col-xs-12">
          	<iframe id="theFrame" type="text/html" src="{{asset('verFactura/'.$invoice->public_id.'?copia='.$copia)}}"  frameborder="1" width="100%" height="1180"></iframe>
      </div>
      <div class="col-md-6">
          <div class="box box-solid">
            <div class="box-header with-border">
              <h3 class="box-title"><b>NOTAS INTERNAS</b></h3>
            </div>
           <?php if($nota){$i=0; foreach($nota as $note){ ?>
            <div class="box-body">
              <div id="accordion" class="box-group">
                <div class="panel box box-primary">
                  <div class="box-header with-border">
                    <h3 class="box-title">
                        <b>Nota #{{$i+1}}</b>
                            <?php
//                            $lenguage = 'es_ES.UTF-8';
//                            putenv("LANG=$lenguage");
//                            setlocale(LC_ALL, $lenguage);
//                            $date = DateTime::createFromFormat("d-m-Y H:m:i", $note->date);
//                            $fecha = strftime("%d de %B de %Y a las %H:%m:%i",$date->getTimestamp());
//                            echo $fecha;
                            ?> {{$note->date}}
                      </a>
                    </h3>
                  </div>
                    <div class="box-body">
                      {{$note->note}}
                    </div>
                </div>
              </div>
            </div>
           <?php $i++;} }?>
              <form action="{{asset('nuevanota/'.$invoice->public_id)}}" method="POST">
              <textarea id="nota"  name="nota" class="form-control" placeholder="Nota interna" rows="2"></textarea><p></p>
              <div  class="col-xs-2"><button class="btn btn-small btn-primary" type="submit" >Guardar Nota</button></div>
             </form>
        </div>
    </div>
</div>

 <div class="modal" id="anular_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="myModalLabel">Está seguro de anular factura?</h4>
          </div>
          <div class="modal-body col-md-12">
              La factura esta en estado: {{$status}}
          </div>
            <div class="modal-footer center">
                <br>
              <a type="button"  class="btn btn-large btn-default" href="{{ URL::to('anular/'.$invoice->public_id.'/') }}" role="button" >Anular</a>
              <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            </div>
      </div>
     </div>
  </div>
<script type="text/javascript">

$("#send").click(function(){
  $( "#correo" ).submit();
});

contacts = {{ $contacts }};
console.log("this is adding a contact");
console.log(contacts);
ind_con = 1;
contacts.forEach(function(res){
  console.log("this is us");
  addContactToSend(res['id'],res['first_name']+" "+res['last_name'],res['email'],ind_con) ;
  ind_con++;
});



function addContactToSend(id,name,mail,ind_con){
  div ="";// "<div class='col-md-12' id='sendcontacts'>";
  ide = "<input type='hidden' id='contact_id' value='"+id+"' name='contactos["+ind_con+"][id]'>";
  nombre = "<input   id='contact_name' value='"+name+"'name='contactos["+ind_con+"][name]'>&nbsp;";
  correo = "<input    id='contact_mail' value='"+mail+"'name='contactos["+ind_con+"][mail]'>";
  send = "<input  type='checkbox' name='contactos["+ind_con+"][checked]'>";
  findiv = "<br><br>";//</div>";
  $("#contacts_row").append(div+ide+nombre+correo+send+findiv);

}

</script>
@stop
