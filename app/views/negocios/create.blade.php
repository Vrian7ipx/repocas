@extends('header')
@section('title')Nuevo Tipo de Negocio @stop
  @section('head') @stop
@section('encabezado')  NEGOCIO @stop
@section('encabezado_descripcion') Nuevo Tipo de Negocio  @stop
        <li class="active"> Nuevo </li> @stop

@section('content')

{{Former::framework('TwitterBootstrap3')}}

<div class="box box-success">
   <div class="box-header with-border">
      <h3 class="box-title">Datos del Tipo de Negocio</h3>
      <div class="box-tools pull-right">
        <!-- Buttons, labels, and many other things can be placed here! -->
        <!-- Here is a label for example -->

      </div><!-- /.box-tools -->
    </div><!-- /.box-header -->

  <div class="box-body">

      {{ Former::open("negocios")->method('post')->addClass('col-md-8  warn-on-exit')->rules(array(
        'name' => 'required|match:/[a-zA-Z. ]+/',
    )); }}

        <div class="row">
            <div class="col-md-8">


                       <label>Código *</label><br>
                       <input type="text" name="code" class="form-control" placeholder="Código" aria-describedby="sizing-addon2"  pattern=".{1,}" required>
                        <label>Nombre *</label><br>
                        <input type="text" name="name" class="form-control" placeholder="Nombre del Tipo de Negocio" aria-describedby="sizing-addon2"  pattern=".{1,}" required>
            </div>

        </div>

        <br>
        <div class="row">
            {{-- <div class="col-md-1"></div> --}}
                <div class="col-md-3">
                     <a href="{{ url('negocios/') }}" class="btn btn-default btn-sm btn-block">Cancelar&nbsp&nbsp&nbsp&nbsp<span class="glyphicon glyphicon-remove">  </span></a>
                </div>
            <div class="col-md-1"></div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-success dropdown-toggle btn-sm btn-block"> Guardar&nbsp&nbsp&nbsp&nbsp<span class="glyphicon glyphicon-floppy-disk"></span></button>
                </div>
        </div>
        {{ Former::close() }}
  </div><!-- /.box-body -->
  <div class="box-footer">

  </div><!-- box-footer -->
</div><!-- /.box -->


@stop
