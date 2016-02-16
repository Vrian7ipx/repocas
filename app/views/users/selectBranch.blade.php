@extends('layout')

@section('title') Asignación de Sucursal @stop

@section('head')
	<script src="{{ asset('vendor/AdminLTE2/plugins/select2/select2.full.js')}}" type="text/javascript"></script>
    <script src="{{asset('vendor/AdminLTE2/plugins/select2/i18n/es.js')}}" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('vendor/AdminLTE2/plugins/select2/select2.css')}}">
    <style type="text/css">
    [class^='select2'] {
        border-radius: 0px !important;               
      } 
      </style>
@stop

@section('body')
	     
		
		 {{Former::framework('TwitterBootstrap3')}}
  {{ Former::open('sucursal')->method('post')->rules(array( 
        'branch_id' => 'required'
     
    )) }}
</br></br><br>
    <div class="content">

    	<div class="col-md-4">
    	</div>
    	<div class="col-md-4">


			<div class="panel panel-default">
			  <div class="panel-heading">
			    <h2 class="panel-title">{{ strtoupper(Auth::user()->account->name)}} </h2>
			  </div>
			  <div class="panel-body">
			   
			   	<legend>Asignación de Sucursal</legend>
			     {{-- {{ Former::legend('Asignacion de Sucursal') }} --}}
			
			     <p> {{Auth::user()->first_name}}, por favor selecciona una sucursal para facturar:</p>
			     
				 <select id="branches" name="branch_id"class="form-control">                          
                 </select>
                 <p></p> 

	              {{Former::large_primary_submit('Continuar')}}
	              {{ Former::close() }}            
	           
			   
			  </div>
			</div>
		</div>
		<div class="col-md-4">
    	</div>

    </div>
   	<script type="text/javascript">
   	 
 	 var sucursales = <?php echo json_encode($sucursales) ?>;
 	 console.log(sucursales);
     $('#branches').select2({
 		 language: "es",
        data: sucursales	
     })
   	</script>
    	
<!--script type="text/javascript">

	$(".dropdown-menu li a").click(function(){
	  var selText = $(this).text();
	  $(this).parents('.btn-group').find('.dropdown-toggle').html(selText+' <span class="caret"></span>');
	});
</script-->

@stop