@extends('header')
@section('title')Editar Cliente @stop
  @section('head') 
    <script src="{{ asset('vendor/AdminLTE2/plugins/select2/select2.full.js')}}" type="text/javascript"></script>
    <script src="{{asset('vendor/AdminLTE2/plugins/select2/i18n/es.js')}}" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/AdminLTE2/plugins/select2/select2.css')}}">
  @stop
@section('encabezado') CLIENTES @stop
@section('encabezado_descripcion') Editar Cliente:  {{$client->name}} @stop 
@section('nivel') <li><a href="{{URL::to('clientes')}}"><i class="ion-person-stalker"></i> Clientes</a></li>
            <li class="active">Editar </li> @stop
@section('content')

{{Former::framework('TwitterBootstrap3')}}

<div class="box box-primary">
  <div class="box-header with-border">
    <h3 class="box-title">Datos del Cliente </h3>
    <div class="box-tools pull-right">
      <!-- Buttons, labels, and many other things can be placed here! -->
      <!-- Here is a label for example -->
      {{-- <span class="label label-primary">Label</span> --}}
    </div><!-- /.box-tools -->
  </div><!-- /.box-header -->
  <div class="box-body">
    {{ Former::open($url)->method('PUT') }}
		<div class="row">
			<div class="col-md-4">			
				<p>
					<label>Nombre *</label>
					<input type="text" name="name" id="name" class="form-control" placeholder="Nombre del Cliente" aria-describedby="sizing-addon2" title="Ingrese el nombre del cliente"pattern="[a-zA-ZÑñÇç. ].{2,}" value='{{$client->name}}' required>
				</p>				
				<p>					
					<label >Teléfono</label>
					<input type="text" name="work_phone" id="work_phone"class="form-control" placeholder="Teléfono del Cliente" aria-describedby="sizing-addon2" title="Ingrese el número telefónico del cliente" value='{{$client->work_phone}}'>				  
				</p>

				 @if($cuenta->custom_client_label1)
                                    <p>
                                        <label>{{$cuenta->custom_client_label1}}</label>
                                        <input type="text" name="l1" class="form-control" value="{{$client->custom_value1}}" placeholder="{{$cuenta->custom_client_label1}}" aria-describedby="sizing-addon2">
                                    </p>
                                @endif
                                @if($cuenta->custom_client_label2)
                                    <p>
                                        <label>{{$cuenta->custom_client_label2}}</label>
                                        <input type="text" name="l2" class="form-control" value="{{$client->custom_value2}}"placeholder="{{$cuenta->custom_client_label2}}" aria-describedby="sizing-addon2">
                                    </p>
                                @endif
                                @if($cuenta->custom_client_label3)
                                    <p>
                                        <label>{{$cuenta->custom_client_label3}}</label>
                                        <input type="text" name="l3" class="form-control" value="{{$client->custom_value3}}"placeholder="{{$cuenta->custom_client_label3}}" aria-describedby="sizing-addon2">
                                    </p>
                                @endif
                                @if($cuenta->custom_client_label4)
                                    <p>
                                        <label>{{$cuenta->custom_client_label4}}</label>
                                        <input type="text" name="l4" class="form-control"value="{{$client->custom_value4}}" placeholder="{{$cuenta->custom_client_label4}}" aria-describedby="sizing-addon2">
                                    </p>
                                @endif
                                @if($cuenta->custom_client_label5)
                                    <p>
                                        <label>{{$cuenta->custom_client_label5}}</label>
                                        <input type="text" name="l5" class="form-control" value="{{$client->custom_value5}}"placeholder="{{$cuenta->custom_client_label5}}" aria-describedby="sizing-addon2">
                                    </p>
                                @endif
                                @if($cuenta->custom_client_label6)
                                    <p>
                                        <label>{{$cuenta->custom_client_label6}}</label>
                                        <input type="text" name="l6" class="form-control" value="{{$client->custom_value6}}"placeholder="{{$cuenta->custom_client_label6}}" aria-describedby="sizing-addon2">
                                    </p>
                                @endif								
				<legend>Datos para Facturar</legend>
				<p>				
					<label>Razón Social *</label>
					<input type="text" name="business_name" id="business_name" class="form-control" placeholder="Razón Social del Cliente" aria-describedby="sizing-addon2" title="Ingrese la Razón Social"  value='{{$client->business_name}}' required>				  
				</p>				
				<p>				
					<label >NIT/CI *</label>
					<input type="text" name="nit" id="work_phone"class="form-control" placeholder="NIT o CI del Cliente" aria-describedby="sizing-addon2" title="Ingrese el NIT"  value='{{$client->nit}}' required>				  
				</p>		
				<legend>Dirección</legend>
				<p>
 					<label>Zona/Barrio</label>
 					<!--<input type="text" name="address1" id="address1" class="form-control" placeholder="Dirección de la Zona/Barrio del Cliente" aria-describedby="sizing-addon2" >-->
                                         <select id="zone" name="zone"class="form-control select2">
                                             <option></option>
                                             <?php foreach($zonas as $zona){?>
                                                <option value="{{$zona->id}}">{{$zona->name}}</option>
                                             <?php }?>                                                                                        
                                        </select>
                                        <label>Otro</label>
                                        <input type="text" name="other" id="other" value="{{$client->other}}"class="form-control" placeholder="otro" aria-describedby="sizing-addon2" >
 					<label>Dirección</label>
 					<input type="text" name="address2" value="{{$client->address2}}"class="form-control" id="address2" placeholder="Dirección del Cliente" aria-describedby="sizing-addon2" >
                                        <label>Ciudad</label>
                                        <select id="city" name="city"class="form-control select2">
                                            <option></option>
                                             <?php foreach($deptos as $depto){?>
                                                <option >{{$depto}}</option>
                                             <?php }?>                                                                                        
                                        </select>

				</p>		

			</div>
			<div class="col-md-1"></div>
			<div class="col-md-5">
				<legend>Contactos</legend>								
				<table class="col-md-9">
						<tbody  data-bind="foreach: setContactos">
							<tr>
								<td><input type="hidden" name="contactos[id][]"class="form-control " data-bind="value: id" /></td>
							</tr>
		    				<tr>	 
		    						<td > <label>Nombres </label> <input name="contactos[first_name][]"  class="form-control " data-bind="value: nombres" placeholder="Nombre del Contacto" pattern="[a-zA-ZÑñÇç. ].{2,}"/> </td>
		            
		    				</tr>
		    				<tr><td><p></p></td></tr>
				            <tr>	
				            	 
				                <tr>	 
		    						<td > <label>Apellidos </label> <input name="contactos[last_name][]"  class="form-control " data-bind="value: apellidos" placeholder="Apellidos del Contacto" pattern="[a-zA-ZÑñÇç. ].{2,}"/> </td>
		            
		    				</tr>
				            
				            </tr>
				            <tr><td><p></p></td></tr>
				            <tr>
				            	 
				                <td><label>Correo </label><input name="contactos[email][]" class="form-control " data-bind="value: correo" placeholder="Correo del Contacto" type=email/> </td>
				            
				            </tr>
				            <tr><td><p></p></td></tr>
				            <tr>
				            	 
				                <td><label>Télefono </label><input name="contactos[phone][]" class="form-control " data-bind="value: telefono" placeholder="Teléfono del Contacto"/> </td>
				            
				            </tr>
		          
		    				<tr><td><p></p><center><a href="#" data-bind="click: $root.removerContacto"> - Eliminar Contacto</a></center></td></tr>
		    				<tr><td><p></p></td></tr>		    					      				
		    			</tbody>
				</table>			
				<div class="col-md-10">
					<a href="#" data-bind="click: addContacto"> + Añadir Contacto</a>
				</div>				
				<label>Antecedentes</label><br>
				<textarea name="private_notes" class="form-control" cols="50" rows="3"placeholder="Ingrese Antecedentes">{{$client->private_notes}}</textarea>				
                                                                <div class="col-md-10">
                                    <legend>Informaci&oacute;n Adicional</legend>
                                    <label>Agrupaci&oacute;n</label><br>                                    
                                    <select id="group" name="group" class="form-control select2">
                                        <option></option>
                                         <?php foreach($grupos as $grupo){?>
                                        <option value="{{$grupo->id}}">{{$grupo->name}}</option>
                                         <?php }?>                                                                                        
                                    </select>
                                    <label>Clasificaci&oacute;n</label><br>
                                    <select id="business" name="business" class="form-control select2">
                                        <option></option>
                                         <?php foreach($negocios as $negocio){?>
                                        <option value="{{$negocio->id}}">{{$negocio->name}}</option>
                                         <?php }?>                                                                                        
                                    </select>
                                    <label>Frecuencia</label><br>
                                    <input type="checkbox" name="d1" value="1" <?php if($d[0]){?> checked="checked" <?php }?> > &nbsp;Lunes<br>
                                    <input type="checkbox" name="d2" value="2" <?php if($d[1]){?> checked="checked" <?php }?> > &nbsp;Martes<br>
                                    <input type="checkbox" name="d3"value="3" <?php if($d[2]){?> checked="checked" <?php }?> > &nbsp;Mi&eacute;rcoles<br>
                                    <input type="checkbox" name="d4"value="4" <?php if($d[3]){?> checked="checked" <?php }?> > &nbsp;Jueves<br>
                                    <input type="checkbox" name="d5"value="5" <?php if($d[4]){?> checked="checked" <?php }?> > &nbsp;Viernes<br>
                                    <input type="checkbox" name="d6"value="6" <?php if($d[5]){?> checked="checked" <?php }?> > &nbsp;S&aacute;bado<br>
                                    <input type="checkbox" namne="d7"value="7" <?php if($d[6]){?> checked="checked" <?php }?> > &nbsp;Domingo<br>
				
				</div>  
			</div>
		</div>
		<p></p>
                <hr>
		<div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-2">
                 <a href="{{ url('clientes') }}" class="btn btn-default btn-sm btn-block">Cancelar&nbsp;<span class="glyphicon glyphicon-remove">  </span></a>                 
            </div>            
            <div class="col-md-2">
                <button type="submit" class="btn btn-success dropdown-toggle btn-sm btn-block"> Guardar&nbsp;<span class="glyphicon glyphicon-floppy-disk"></span></button>
            </div>
        </div>

		

		{{ Former::close() }}
  </div><!-- /.box-body -->
  <div class="box-footer">
    Nota: (*) Campos Requeridos.
  </div><!-- box-footer -->
</div><!-- /.box -->

<script type="text/javascript">
        
        $("#zone").select2();        
        $("#zone").select2("val",'{{$client->zone_id}}');;
        $("#city").select2()                
        $("#city").select2("val","{{$client->city}}");
        $("#group").select2();
        $("#group").select2("val",'{{$client->group_id}}');;
        $("#business").select2();
        $("#business").select2("val",'{{$client->business_type_id}}');
	function Contacto(id,nombres,apellidos,correo,telefono)
	{
		var self = this;
		  self.id = id;
		  self.nombres = nombres;
		  self.apellidos = apellidos;
		  self.correo = correo;
		  self.telefono = telefono;			
	}
	function Contactos()
	{
		var self = this;
		self.contactos = <?php echo json_encode($contactos);?>;
		console.log(self.contactos);
		
		console.log(self.contactos.length)

		self.setContactos = ko.observableArray();
		if(self.contactos.length>0)
		{
			for (var i = 0; i < self.contactos.length; i++) {
			console.log(self.contactos[i]);
			self.setContactos.push(new Contacto(self.contactos[i].id,self.contactos[i].nombres,self.contactos[i].apellidos,self.contactos[i].email,self.contactos[i].phone));
			};	
		}
		


		self.addContacto = function() {
		        self.setContactos.push(new Contacto("","","","",""));
		    };

        self.removerContacto = function(contacto){
            self.setContactos.remove(contacto);
          };
	}
	


	// Activates knockout.js
	ko.applyBindings(new Contactos());

</script>

@stop
