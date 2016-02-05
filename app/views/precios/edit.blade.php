@extends('header')
@section('title')Editar Precio @stop
  @section('head') @stop
@section('encabezado')  PRECIOS @stop
@section('encabezado_descripcion') Editar Precio  @stop 
@section('nivel') <li><a href="{{URL::to('productos')}}"><i class="fa fa-cube"></i> Productos</a></li><li>Precios</li>
        <li class="active"> Editar </li> @stop

@section('content')

{{Former::framework('TwitterBootstrap3')}}


<div class="box box-primary">
   <div class="box-header with-border">
      <h3 class="box-title">Datos del Precio</h3>
      <div class="box-tools pull-right">
        <!-- Buttons, labels, and many other things can be placed here! -->
        <!-- Here is a label for example -->
        
      </div><!-- /.box-tools -->
    </div><!-- /.box-header -->
  
  <div class="box-body">
     {{ Former::open("precios/".$nombre->id)->method('put')}}

        <div class="row">
            <div class="col-md-8">                
                <div class="col-md-8">
                    <label>Nombre:</label>
                     <input type="text" name="name" class="form-control" value='{{$nombre->name}}' aria-describedby="sizing-addon2" title="Ingrese el nombre del precio" pattern=".{1,}" required> 
                </div>
            </div>
            <br>
                <!--<label>Asignación</label>-->
            <div class="col-md-8">                
                <br>
                <label>Asignación</label>
                <hr>
                <div class="col-md-5">
                    <?php foreach ($precios as $precio){?>
                    <label>{{$precio->product_key." - ".$precio->notes}}:</label>
                     <input type="number" name="price-{{$precio->id}}" step="any" class="form-control" value='{{$precio->cost}}' aria-describedby="sizing-addon2" title="Ingrese el precio" pattern=".{1,}" required> 
                    <?php }?>
                </div>
            </div>
      </div>
            <br><br>
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-2">
                     <a href="{{ url('precios/') }}" class="btn btn-default btn-sm btn-block">Cancelar&nbsp;<span class="glyphicon glyphicon-remove">  </span></a>
                </div>
                <div class="col-md-1"></div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success dropdown-toggle btn-sm btn-block"> Guardar&nbsp;<span class="glyphicon glyphicon-floppy-disk"></span></button>
                </div>
            </div>
           

        {{ Former::close() }}
  
  <div class="box-footer">
   
  </div><!-- box-footer -->
</div><!-- /.box -->


@stop
