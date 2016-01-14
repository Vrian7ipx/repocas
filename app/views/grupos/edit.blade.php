@extends('header')
@section('title')Editar Grupo @stop
  @section('head') @stop
@section('encabezado')  GRUPOS @stop
@section('encabezado_descripcion') Editar Grupo  @stop
@section('nivel') <li><a href="{{URL::to('grupos')}}"><i class="fa fa-cube"></i> Grupos</a></li><li>Grupos</li>
        <li class="active"> Editar </li> @stop

@section('content')

{{Former::framework('TwitterBootstrap3')}}


<div class="box box-primary">
   <div class="box-header with-border">
      <h3 class="box-title">Datos del Grupo</h3>
      <div class="box-tools pull-right">
        <!-- Buttons, labels, and many other things can be placed here! -->
        <!-- Here is a label for example -->

      </div><!-- /.box-tools -->
    </div><!-- /.box-header -->

  <div class="box-body">
     {{ Former::open("grupos/".$grupo->id)->method('put')}}

        <div class="row">
              <div class="col-md-5">
                         <label>Código *</label><br>
                         <input type="text" name="code" class="form-control" value = "{{ $grupo->code }}" placeholder="Código" aria-describedby="sizing-addon2"  pattern=".{1,}" required>
                          <label>Nombre *</label><br>
                          <input type="text" name="name" class="form-control" value = "{{ $grupo->name }}" placeholder="Nombre del Grupo" aria-describedby="sizing-addon2"  pattern=".{1,}" required>
                          <label>Datos Adicionales *</label><br>
                          <textarea  name="datos" class="form-control" cols="50" rows="3"   placeholder="Datos Adicionales" aria-describedby="sizing-addon2" >{{ $grupo->delimitaciones }}</textarea>

              </div>
        </div>
            <br><br>
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-2">
                     <a href="{{ url('grupos/') }}" class="btn btn-default btn-sm btn-block">Cancelar&nbsp&nbsp&nbsp&nbsp<span class="glyphicon glyphicon-remove">  </span></a>
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
