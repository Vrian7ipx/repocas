@extends('header')
@section('title')Tipo de Negocio @stop
  @section('head') @stop
@section('encabezado')  NEGOCIO @stop
@section('encabezado_descripcion') Gestion del Tipo de Negocio @stop
         <li class="active"> Negocio </li> @stop

@section('content')

<div class="box">
  <div class="box-header with-border">
    <h3 class="box-title"><a href="{{ url('negocios/create') }}" class="btn btn-success" role="button">Nuevo Tipo de Negocio &nbsp<span class="glyphicon glyphicon-plus-sign"></span></a></h3>
    <div class="box-tools pull-right">
      <!-- Buttons, labels, and many other things can be placed here! -->
      <!-- Here is a label for example -->
      <span class="label label-info">Gestión del tipo de Negocio</span>
    </div><!-- /.box-tools -->
  </div><!-- /.box-header -->
  <div class="table-responsive">
     <table id="datatable" class="table table-striped table-hover" cellspacing="0" cellpadding="0" width="100%" style="margin-left:24px;">
          <thead>
              <tr>
                  <td>Código</td>
                  <td>Nombre</td>
                  <td style="display:none;">Acción</td>
              </tr>
          </thead>
			<thead>
              <tr>
                  <th>Código</th>
                  <th>Nombre</th>
                  <th>Acción</th>
              </tr>
          </thead>
          <tbody>

          @foreach($businesses as $business )
              <tr>
                  <td>{{ $business->cod }}</td>
                  <td>{{ $business->name }}</td>
                  <td>
                    <div class="dropdown">
			            <a class="btn btn-primary btn-xs" data-task="view" href="{{ URL::to("negocios/".$business->id) }}"  style="text-decoration:none;color:white;"><i class="glyphicon glyphicon-eye-open"></i></a>
                  <a class="btn btn-warning btn-xs" data-task="view" href="{{ URL::to("negocios/".$business->id.'/edit') }}"  style="text-decoration:none;color:white;"><i class="glyphicon glyphicon-edit"></i></a>
              </td>
              </tr>
          @endforeach
          </tbody>
        </table>
  </div><!-- /.box-body -->
  {{-- <div class="box-footer">
    The footer of the box
  </div><!-- box-footer --> --}}
</div><!-- /.box -->



<script type="text/javascript">
	$(document).ready(function() {
    // Setup - add a text input to each footer cell
     //Setup - add a text input to each footer cell
    $('#datatable thead td').each( function () {
        var title = $('#datatable thead td').eq( $(this).index() ).text();
		var tamaño = 10;
		if (title == 'Nº') {
		  tamaño = 3;
		  $(this).html('<div class="form-group  has-feedback"><input size="'+tamaño+'" placeholder="'+title+'" type="text" class="form-control" id="place"><span style="text-decoration:none;color:#D3D3D3;" class="glyphicon glyphicon-search form-control-feedback"></span></div>');

		}
		else{
		tamaño = 10;
        $(this).html('<div class="form-group has-feedback"><input size="'+tamaño+'" placeholder="'+title+'" type="text" class="form-control" id="place"><span style="text-decoration:none;color:#D3D3D3;" class="glyphicon glyphicon-search form-control-feedback"></span></div>' );
		}
    } );

    // DataTable
	$('#datatable').DataTable(
      {
      "language": {
		"zeroRecords": "&nbsp;&nbsp;&nbsp;No se encontro el registro",
        "sLengthMenu":    "&nbsp;&nbsp;&nbsp;Mostrar _MENU_ registros",
        "sZeroRecords":   "&nbsp;&nbsp;&nbsp;No se encontraron resultados",
        "sEmptyTable":    "&nbsp;&nbsp;&nbsp;Ningún dato disponible en esta tabla",
        "info": "&nbsp;&nbsp;&nbsp;Mostrando página _PAGE_ de _PAGES_",
        "infoEmpty": "&nbsp;&nbsp;&nbsp;No hay registros disponibles",
        "sInfoFiltered":  "(filtrado de un total de _MAX_ registros)",
        "sUrl":           "",
        "sInfoThousands":  ",",
        "sLoadingRecords": "Cargando...",
        "oPaginate": {
            "sFirst":    "Primero",
            "sLast":    "Último",
            "sNext":    "Siguiente",
            "sPrevious": "Anterior"
        }

    }
   });

   $('#formConfirm').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget);
      var public_id = button.data('id');
      var name = button.data('name');
	  //alert(name);

      var modal = $(this);
      modal.find('.modal-body').text('¿ Está seguro de borrar el Grupo ' + name + ' ?');
      document.getElementById("public_id").value = public_id;
  });

    var table = $('#datatable').DataTable();

    // Apply the search
    table.columns().every( function () {
        var that = this;

        $( 'input', this.header() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );
		$("#datatable_filter").css("display", "none");
    } );
} );

</script>

@stop
