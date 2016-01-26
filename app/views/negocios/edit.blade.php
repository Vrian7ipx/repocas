@extends('header')
@section('title')Editar Tipo de Negocio @stop
  @section('head') @stop
@section('encabezado')  NEGOCIOS @stop
@section('encabezado_descripcion') Editar Tipo de Negocio  @stop
        <li class="active"> Editar </li> @stop

@section('content')

{{Former::framework('TwitterBootstrap3')}}


<div class="box box-primary">
   <div class="box-header with-border">
      <h3 class="box-title">Datos del tipo de Negocio</h3>
      <div class="box-tools pull-right">
        <!-- Buttons, labels, and many other things can be placed here! -->
        <!-- Here is a label for example -->

      </div><!-- /.box-tools -->
    </div><!-- /.box-header -->

  <div class="box-body">
     {{ Former::open("negocios/".$business->id)->method('put')}}

        <div class="row">
              <div class="col-md-5">
                         <label>Código *</label><br>
                         <input type="text" name="code" class="form-control" value = "{{ $business->cod }}" placeholder="Código" aria-describedby="sizing-addon2"  pattern=".{1,}" required>
                          <label>Nombre *</label><br>
                          <input type="text" name="name" class="form-control" value = "{{ $business->name }}" placeholder="Nombre del Tipo de Negocio" aria-describedby="sizing-addon2"  pattern=".{1,}" required>
              </div>
        </div>
            <br><br>
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-2">
                     <a href="{{ url('negocios/') }}" class="btn btn-default btn-sm btn-block">Cancelar&nbsp&nbsp&nbsp&nbsp<span class="glyphicon glyphicon-remove">  </span></a>
                </div>
                <div class="col-md-1"></div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success dropdown-toggle btn-sm btn-block"> Guardar&nbsp&nbsp&nbsp&nbsp<span class="glyphicon glyphicon-floppy-disk"></span></button>
                </div>
            </div>


        {{ Former::close() }}

  <div class="box-footer">

  </div><!-- box-footer -->
</div><!-- /.box -->


@stop
