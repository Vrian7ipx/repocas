@extends('header')

@section('title') Nuevo Usuario @stop

@section('head') 

    <script src="{{ asset('customs/bootstrap-switch.js')}}" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('customs/bootstrap-switch.css')}}">
@stop
@section('encabezado') USUARIOS @stop
@section('encabezado_descripcion') Nuevo Usuario @stop 
@section('nivel') <li><a href="{{URL::to('usuarios')}}"><i class="fa fa-users"></i> Usuarios</a></li>
            <li class="active">Nuevo</li>@stop
@section('content')
	 
	 
	{{Former::framework('TwitterBootstrap3')}}
  {{ Former::open('usuarios')->rules(array(
      'name' => 'required|min:3',
      'username' => 'required',

      'first_name' => 'required',
      
      'password' => 'required',
      'password_confirm' => 'required'
      
      )) }}


      	<div class="box box-success	">
      		<div class="box-header with-border">
			    <h3 class="box-title">Datos de Usuario</h3>
			</div><!-- /.box-header -->
		  <div class="box-body">
		  	 <div class="row">
			     <div class="col-md-4">
			     	{{-- <legend>Datos del Usuario</legend> --}}
			     	<div class="col-md-9">
				     	
				     	<label>Nombre (s) *</label>
				     	<input type="text" name="first_name" class="form-control" placeholder="Nombre del Usuario" aria-describedby="sizing-addon2" pattern="[a-zA-Z. ].{2,}"  required>
				     	<label>Apellido *</label>
				     	<input type="text" name="last_name" class="form-control" placeholder="Apellido del Usuario" aria-describedby="sizing-addon2" pattern="[a-zA-Z. ].{2,}"  required>
				     	<label>Email *</label>
				     	<input type="email" name="email" class="form-control" placeholder="Email" aria-describedby="sizing-addon2" required>
				     	<label>Télefono/Celular *</label>
				     	<input type="text" name="phone" class="form-control" placeholder="Núm Telefónico del Usuario" aria-describedby="sizing-addon2" pattern="([0-9]).{5,11}"  required>
			     	</div>
                              </div>
			    <div class="col-md-5">
			    	<legend>Datos de Ingreso</legend>
			    	<div class="col-md-8">
			    		<label>Carnet de Identidad *</label>
			    		<input type="text" name="username" class="form-control" placeholder="Nombre de Usuario" aria-describedby="sizing-addon2" pattern=".{2,}"  required>
			    		<label>Contraseña *</label>
			    		<input type="password" name="password" class="form-control" placeholder="Contraseña del Usuario" aria-describedby="sizing-addon2" pattern=".{4,}"  required>
			    		<label>Repetir Contraseña *</label>
			    		<input type="password" name="password_confirm" class="form-control" placeholder="Contraseña del Usuario" aria-describedby="sizing-addon2" pattern=".{5,}"  required>		         		
                                </div>
			    <div class="col-md-2"></div>
			    <p></p>
			    <div class="col-md-9">
			    	<legend>Tipo de Usuario</legend>
                                Administrador:
                                <input type="radio" name="admin" value="1">
                                <br>
                                Facturador:
                                <input type="radio" name="admin" value="0" checked>
                                
                                <legend>Sucursal</legend>
                                <select id="branch" name="branch[]" multiple="multiple" class="form-control select2">
                                        <option></option>
                                         <?php foreach($sucursales as $sucursal){ ?>
                                        <option value="{{$sucursal->id}}">{{$sucursal->name}}</option>
                                         <?php }?>                                                                                        
                                    </select>	        
			    </div>
                            <div class="col-md-9">			    	
                                <legend>Tipo de Precio</legend>
                                <select id="price" name="price" class="form-control select2">
                                        <option></option>
                                         <?php foreach  ($precios as $precio){?>
                                        <option value="{{$precio->id}}">{{$precio->name}}</option>
                                         <?php }?>                                                                                        
                                    </select>
			        
			    </div>
                            <div class="col-md-9">			    	
                                <legend>Grupos Asignados</legend>
                                
                                <select id="groups" multiple="multiple" name="groups[]" class="form-control select2 multiple">
                                        
                                         <?php foreach  ($grupos as $grupo){?>
                                        <option value="{{$grupo->id}}">{{$grupo->name}}</option>
                                         <?php }?>                                                                                        
                                    </select>
			        
			    </div>
                            </div>
                         </div>    
                      <br><br>
                      <hr>
			<div class="row">
		            <div class="col-md-4"></div>
		            <div class="col-md-2">
		                 <a href="{{ url('usuarios/') }}" class="btn btn-default btn-sm btn-block">Cancelar&nbsp;<span class="glyphicon glyphicon-remove">  </span></a>
		            </div>
		            <div class="col-md-1"></div>		            
		            <div class="col-md-2">
		                <button type="submit" class="btn btn-success dropdown-toggle btn-sm btn-block"> Guardar&nbsp;<span class="glyphicon glyphicon-floppy-disk"></span></button>
		            </div>
	        	</div>
                         
			  {{ Former::close()  }}
		  </div><!-- /.box-body -->
		  <div class="box-footer">
		
		  </div><!-- box-footer -->
		</div><!-- /.box -->

	<script type="text/javascript">
            $("#branch").select2();
            $("#price").select2();
            $("#groups").select2();
            //$("#groups").val(items).trigger("change");
			$("form").submit(function() {
			    $(this).submit(function() {
			        return false;
			    });
			    return true;
			});
	</script>


  
@stop