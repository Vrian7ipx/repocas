@extends('header')
@section('title')Nueva Sucursal @stop
  @section('head') @stop
@section('encabezado') SUCURSALES @stop
@section('encabezado_descripcion') Nueva Sucursal @stop 
@section('nivel') <li><a href="{{URL::to('sucursales')}}"><i class="glyphicon glyphicon-home"></i> Sucursales</a></li>
            <li class="active"> Nueva </li> @stop
@section('content')
      {{ Former::open('sucursales')->method('post')->rules(array( 
            'branch_name' => 'required'         
        )) }}

        <!-- Apply any bg-* class to to the info-box to color it -->
      <div class="box box-success">
        <div class="box-header with-border">
          <h3 class="box-title">Datos Sucursal</h3>
          
        </div><!-- /.box-header -->
        <div class="box-body">
         
            <div class="row">
                <div class="col-md-4"> 
                       <div class="col-md-12"> 
                            <label>Nombre de la Sucursal *</label>
                            <input type="text" name ="branch_name" class="form-control" placeholder="Escriba el Nombre de la Nueva Sucursal"  required>
                            <p></p>
                            <label>Número de la Sucursal asignada por Impuestos *</label>
                            <input type="text" name ="number_branch" class="form-control" placeholder="Escriba Número de la Sucursal "  required>
                             
                            <p></p>
                            <label>Tipo de Facturación *</label>                              
                               <div class="list-group">                                  
                                   <li class="list-group-item"><input type="radio" name="type_fac"  value="0"> Facturación Web<br></li>
                                  <li class="list-group-item"><input type="radio" name="type_fac"  value="2"> Facturación POS<br></li>
                                  <li class="list-group-item"><input type="radio" name="type_fac" value="1" checked> Facturación por Terceros<br></li>                                  
                                </div>
                            <p></p>
                            <label>Actividad Económica*</label>
                             <textarea class="form-control" rows="1" name="economic_activity" placeholder="Actividad Económica"  required></textarea>
                             <p></p>
                             <label>Leyenda Ley Nº 453 *</label>
                              <input type="text" name ="law" class="form-control" placeholder="Escriba la Leyenda Ley N° 453"  required>
                              <p></p>
                              <label>SFC*</label>
                              <input type="text" name ="sfc" class="form-control" placeholder="SFC" pattern=".{3,}" required> <p></p>

                   </div>
                      
                </div> 
                <div class="col-md-5">
                    <legend>Dosificación</legend>
                    {{-- {{ Former::legend('Dosificación') }} --}}
                    <div class="col-md-12">
                        <label>Número de Trámite *</label>
                        <input type="text" name ="number_process" class="form-control" placeholder="Núm. de Trámite"  required><p></p>                        
                        <label>Número de Autorización *</label>
                        <input type="text" name ="number_autho" class="form-control" placeholder="Núm. de Autorización" required><p></p>
                         <label>Fecha límite de Emisión *</label>

                         <div class="input-group">              
                                                      <input class="form-control pull-right" name ="deadline" name="invoice_date" id="date" type="text" placeholder="Fecha Límite de Emisión" required>
                                                      <div class="input-group-addon">          
                                                      <i class="fa fa-calendar"></i>
                                                      </div>
                                                    </div><!-- /.input group -->



                        <label>Llave de Dosificación *</label>
                        <input type="text" name ="key_dosage" class="form-control" placeholder="Llave de Dosificación"  pattern=".{3,}" required><p></p>
                        <input type="file" id="exampleInputFile" >
                        <p class="help-block">Archivo proporcionado por Impuestos .</p>
                    </div>

                  

                
                  
                </div>

                <div class="col-md-5">    
                  <legend>Dirección</legend>
                 
                  <label>Zona/Barrio *</label>
                  <input type="text" name ="address1" class="form-control" placeholder="Zona/Barrio " required><p></p>
                   <label>Dirección *</label>
                  <input type="text" name ="address2" class="form-control" placeholder="Dirección de la Sucursal"  required><p></p>
                  <label>Teléfono *</label>
                  <input type="text" name ="work_phone" class="form-control" placeholder="Teléfono de la Sucursal"  required><p></p>
                  <label>Cuidad *</label>
                  <input type="text" name ="city" class="form-control" placeholder="Ciudad" required><p></p>
                  <label>Municipio *</label>
                  <input type="text" name ="state" class="form-control" placeholder="Municipio" required><p></p>
                  
                  {{-- Former::file('dosage')->label('Archivo con la Llave (*)')->inlineHelp(trans('texts.dosage_help')) --}}
                 
                 

                  {{-- Former::legend('Leyendas') --}}

                  {{-- Former::textarea('law')->label('leyenda Genérica  (*)') --}}
                
                  </div>
              </div> 

        <p></p>
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-2">
                 <a href="{{ url('sucursales') }}" class="btn btn-default btn-sm btn-block">Cancelar&nbsp&nbsp&nbsp&nbsp<span class="glyphicon glyphicon-remove">  </span></a>
            </div>
            {{-- <div class="col-md-1"></div> --}}
            <div class="col-md-2">
                <button type="submit" class="btn btn-success dropdown-toggle btn-sm btn-block"> Guardar &nbsp&nbsp&nbsp&nbsp<span class="glyphicon glyphicon-floppy-disk"></span></button>
            </div>
        </div>
        <p></p>
        &nbspTodos los Campos son Requeridos (*)

         {{ Former::close() }}
        </div><!-- /.box-body -->
        <div class="box-footer">
        
        </div><!-- box-footer -->
      </div><!-- /.box -->



<script type="text/javascript">
   $("#date").datepicker();
        $('#date').on('changeDate', function(ev){
            $(this).datepicker('hide');
        });

      $("form").submit(function() {
          $(this).submit(function() {
              return false;
          });
          return true;
      });
       $("#date").datepicker();
        $('#date').on('changeDate', function(ev){
            $(this).datepicker('hide');
        });
  </script>
@stop 
