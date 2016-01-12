@extends('header')

@section('title') Editar Usuario @stop

@section('head') @stop

@section('encabezado') USUARIOS @stop
@section('encabezado_descripcion') Editar Usuario: {{$usuario->first_name}} @stop 
@section('nivel') <li><a href="{{URL::to('usuarios')}}"><i class="fa fa-users"></i> Usuarios</a></li>
            <li class="active">Editar</li>@stop

@section('content')
	


	{{Former::framework('TwitterBootstrap3')}}
  {{ Former::open('usuarios/'.$usuario->id)->method('put')->rules(array(
      'name' => 'required|min:3',
      'sucursales' => 'required'
      //'nit' => 'required|Numeric|min:5',
      //'username' => 'required|min:4',
      //'password' => 'required',
      //'password_confirmation' => 'required'
      
      )) }}



	<div class="box box-primary">
	  <div class="box-header with-border">
	    <h3 class="box-title">Información de Usuario</h3>
	    <div class="box-tools pull-right">	      	      
	    </div><!-- /.box-tools -->
	  </div><!-- /.box-header -->
	  <div class="box-body">
	    
	  	 <div class="row">
		    <div class="col-md-6">
			      <div class="col-md-7">
				     	
				     	<label>Nombre (s) *</label>
				     	<input type="text" name="first_name" class="form-control" placeholder="Nombre del Usuario" aria-describedby="sizing-addon2" title="Ingrese el nombre del Usuario"pattern="[a-zA-ZÑñÇç. ].{2,}" value="{{$usuario->first_name}}" required>
				     	<label>Apellido *</label>
				     	<input type="text" name="last_name" class="form-control" placeholder="Apellido del Usuario" aria-describedby="sizing-addon2" title="Ingrese el Apellido del Usuario"pattern="[a-zA-ZÑñÇç. ].{2,}"  value="{{$usuario->last_name}}" required>
				     	<label>Email *</label>
				     	<input type="email" name="email" class="form-control" placeholder="Email" aria-describedby="sizing-addon2" title="Ingrese el nombre del cliente" value="{{$usuario->email}}" required>
				     	<label>Télefono/Celular *</label>
				     	<input type="text" name="phone" class="form-control" placeholder="Núm Telefónico del Usuario" aria-describedby="sizing-addon2" title="Ingrese un Núm Telefónico"pattern="([0-9]).{6,11}"  value="{{$usuario->phone}}" required>
			     	</div>
			</div>
		   
         <script>
            sucs= [];
            grus = [];
            s = 0;
            g = 0;
        </script>	
		    @if(Auth::user()->is_admin)
                     <div class="col-md-5">
                            <legend>Datos de Ingreso</legend>
			    	<div class="col-md-8">
			    		<label>Carnet de Identidad *</label>
			    		<input type="text" name="username" class="form-control" placeholder="Nombre de Usuario" aria-describedby="sizing-addon2" pattern=".{2,}" value="{{$usuario->username}}" required>
			    		<label>Contraseña *</label>
			    		<input type="password" name="password" class="form-control" placeholder="Contraseña del Usuario" aria-describedby="sizing-addon2" pattern=".{4,}" value="**********" required>
			    		<label>Repetir Contraseña *</label>
			    		<input type="password" name="password_confirm" class="form-control" placeholder="Contraseña del Usuario" aria-describedby="sizing-addon2" pattern=".{5,}"  value="**********" required>   		
                                </div>
			    <div class="col-md-2"></div>
                        <div class="col-md-9">
                            			    <p></p>
			    <div class="col-md-9">
			    	<legend>Tipo de Usuario</legend>
                                Administrador:
                                <input type="radio" name="admin" value="1" <?php if($usuario->is_admin==1){ ?> checked <?php }?> >
                                <br>
                                Facturador:
                                <input type="radio" name="admin" value="0" <?php if($usuario->is_admin==0){ ?> checked <?php }?>>
                                
                                <legend>Sucursal</legend>
                                <select id="branch" name="branch[]" multiple="multiple" class="form-control select2">
                                        <option></option>
                                         <?php foreach($sucursales as $key=>$sucursal){ ?>
                                        <script type="text/javascript">                                            
                                            <?php if(in_array($sucursal->id, $sucs)){ ?>
                                            sucs[s++]={{$sucursal->id}};
                                         <?php }?>
                                        </script>
                                        <option value="{{$sucursal->id}}">{{$sucursal->name}}</option>
                                         <?php }?>                                                                    
                                    </select>	        
			    </div>
                            <div class="col-md-9">			    	
                                <legend>Tipo de Precio</legend>
                                <select id="price" name="price" class="form-control select2">
                                        <option></option>
                                         <?php foreach  ($precios as $precio){?>
                                        <option value="{{$precio->id}}" <?php if($usuario->price_type_id==$precio->id){ ?> selected <?php }?> >{{$precio->name}}</option>
                                         <?php }?>                                                                                        
                                    </select>
			        
			    </div>
                            <div class="col-md-9">			    	
                                <legend>Grupos Asignados</legend>
                                
                                <select id="groups" multiple="multiple" name="groups[]" class="form-control select2 multiple">
                                        
                                         <?php foreach  ($grupos as $grupo){?>
                                    
                                    <?php if(in_array($grupo->id, $grus)){ ?>
                                        <script type="text/javascript">                                            
                                            grus[g++]={{$grupo->id}};
                                        </script>
                                        <?php }?>                             
                                        <option value="{{$grupo->id}}">{{$grupo->name}}</option>
                                         <?php }?>                                                                                        
                                    </select>
			        
			    </div>
                        </div>
                     </div>
		    @endif

		  </div>
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
            
            //console.log(grus);
            $("#branch").val(sucs).trigger("change");
            $("#groups").val(grus).trigger("change");
	</script>
@stop
