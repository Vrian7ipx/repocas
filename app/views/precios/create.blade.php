@extends('header')
@section('title')Nuevo Precio @stop
  @section('head') @stop
@section('encabezado')  PRECIOS @stop
@section('encabezado_descripcion') Nuevo Precio  @stop 
@section('nivel') <li><a href="{{URL::to('precios')}}"><i class="fa fa-cube"></i> Productos</a></li><li>Precio</li>
        <li class="active"> Nuevo </li> @stop

@section('content')

{{Former::framework('TwitterBootstrap3')}}

<div class="box box-success">
   <div class="box-header with-border">
      <h3 class="box-title">Datos del precio</h3>
      <div class="box-tools pull-right">
        <!-- Buttons, labels, and many other things can be placed here! -->
        <!-- Here is a label for example -->
        
      </div><!-- /.box-tools -->
    </div><!-- /.box-header -->
  
  <div class="box-body">
      {{ Former::open("precios")->method('post')->rules(array( 
        'name' => 'required|match:/[a-zA-Z. ]+/',
    )); }}

        <div class="row">
            <div class="col-md-8">
                <div class="col-md-8">
                     <p>
                        <label>Nombre *</label><br>
                        <input type="text" name="name" class="form-control" placeholder="Nombre del Precio" aria-describedby="sizing-addon2" title="Ingrese el nombre del precio" pattern=".{1,}" required>                    
                    </p>
                </div>
            </div>
            <div class="col-md-8">                
                <br>
                <label>Asignación</label>
                <hr>
                <div class="col-md-4">
                    <?php foreach ($productos as $producto){?>
                    <label>{{$producto->product_key." - ".$producto->notes}}:</label>
                     <input type="number" name="price-{{$producto->id}}" class="form-control" step="any" aria-describedby="sizing-addon2" title="Ingrese el precio" required> 
                    <?php }?>
                </div>
            </div>

        </div>
        <br>
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
  </div><!-- /.box-body -->
  <div class="box-footer">
    
  </div><!-- box-footer -->
</div><!-- /.box -->


@stop
