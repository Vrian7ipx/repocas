<?php

class ProductController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$products =  Product::join('categories', 'categories.id', '=', 'products.category_id')				
				->where('categories.deleted_at', '=', null)
				->select('products.public_id', 'products.product_key', 'products.notes','products.is_product', 'products.cost','categories.name as category_name','categories.id as category_id')->get();
                $products = Product::get();
		// print_r($products);
		// return 0;
	    return View::make('productos.index', array('products' => $products));
	}



	/**
	 * Show the form for creating a new product.
	 *
	 * @return Response
	 */
	public function create()
	{
	   $priceTypes = PriceType::orderBy('id','ASC')->get();
           $data=[
               'precios'=>$priceTypes,
           ];
	   return View::make('productos.create',$data);

	}

	/**
	 * Show the form for creating a new service
	 *
	 * @return Response
	 */
	public function createservice()
	{
		// return "entro a servicios";
	    if(Auth::check() || Session::has('account_id')){
	    	$categories = Category::where('account_id',Auth::user()->account_id)->orderBy('public_id')->get();
			return View::make('productos.createservice')->with('categories',$categories);
	    }
	    else{
	    	return Redirect::to('/');
	    }
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		// return Response::json(Input::all());
                //print_r(Input::all());
                //return 0;
		$product = Product::createNew();
		$product -> setProductKey(trim(Input::get('product_key')));
		$product -> setNotes(trim(Input::get('notes')));
                $product ->setUnits(Input::get('units'));
                $product->setCc(Input::get('volume'));
                $product->setPackTypes(Input::get('type'));
                $product->setIce(Input::get('ice'));
                $typePrices = PriceType::orderBy('id','ASC')->get();
                $default_cost=Input::get('price-1');
                $product->save();
                foreach ($typePrices as $type){

                    $price = Price::createNew();
                    $prices->account_id = 1;
                    //if(Input::get('price-'.$type->id))
                        $price->cost=Input::get('price-'.$type->id);
                    //else
                      //  $price =$default_cost;
                    $price->price_type_id = $type->id;
                    $price->product_id = $product->id;
                    $price->save();
                }
                Session::flash('message',"Producto creado con exito");
		return Redirect::to('productos');

		//$product -> setCost(trim(Input::get('cost')));
		//$product -> setQty(trim(Input::get('qty')));
		//$product -> setCategory(trim(Input::get('category_id')));
                $error = $product->guardar();
		if(Input::get('json')=="1")
		{
                        if($error)
                            return json_encode($error);
			$product -> setIsProduct(1);
			$product->save();
			return json_encode(0);
		}
		if(Input::get('json')=="2")
		{
                        if($error)
                            return json_encode($error);
                        //$resultado = $product->guardar();
			$product -> setIsProduct(0);
			$product->save();
			return json_encode(0);
		}
		//$product->is_product =trim(Input::get('is_product'));
		//$product->unidad_id =trim(Input::get('unidad_id'));


		//$product -> setPublicId(trim(Input::get('')));
		//$product->setAccount(trim(Input::get('')));
		//$product->setUser(trim(Input::get('')));
		$resultado = $product->guardar();

		if(!$resultado){
			$message = $product->is_product?"Producto creado con éxito":"Servicio creado con éxito";
			$product->save();
		}
		else
		{
			$url = 'productos/create';
			Session::flash('error',	$resultado);
	        return Redirect::to($url)
	          ->withInput();
		}



		// $product ->	product_key =	;
		// $product ->	notes		=	trim(Input::get('notes'));
		// $product -> cost 		=	trim(Input::get(''));
		// $product ->	category_id =	trim(Input::get('category_id'));

		// $product ->	save();
		//if(null!=Input::get('json'));
	//		return Response::json(array());



		Session::flash('message',	$message);
		return Redirect::to('productos');

	}
	public function storage2()
	{
		//return "brian";
		//return $this->save();
		$productId = null;
		$product = Product::createNew();
		$product -> setProductKey(null);
		$product -> setNotes(null);
		$product -> setCost(null);
		$product -> setQty(null);
		$product -> setCategory(null);
		$product -> setPublicId(null);
		$product->setAccount(null);
		$product->setUser(null);
		$resultado = $product->guardar();
		print_r($product);echo "<br><br>";
		return $resultado;
		// $product ->	setProduct_key =	trim(Input::get('product_key'));
		// $product ->	notes		=	trim(Input::get('notes'));
		// $product -> cost 		=	trim(Input::get('cost'));
		// $product ->	category_id =	trim(Input::get('category_id'));

		$product ->	save();
		if(null!=Input::get('json'));
			return Response::json(array());

		$message = "Producto creado con éxito";

		Session::flash('message',	$message);
		return Redirect::to('productos/' . $product -> public_id);

	}

	 // esto puede funcionar pero no confio $rules = ['product_key' => 'unique:products,product_key,' . $productId . ',id,account_id,' . Auth::user()->account_id. ',deleted_at,NULL'];



	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
            $product = Product::where('id',$id)->first();            
            $prices = Price::where('product_id',$id)->orderBy('price_type_id','ASC')->get();
            $new_prices = array();
            foreach ($prices as $price){
                $types = PriceType::where('id',$price->price_type_id)->first();
                $price->name = $types->name;
                array_push($new_prices, $price);
            }
	    $data = array(	    	
	    	'product' => $product,
                'precios' => $new_prices,
	    );

	    return View::make('productos.show', $data);
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$product = Product::where('id',$id)->first();
                $prices = Price::where('product_id',$id)->orderBy('price_type_id','ASC')->get();
                $new_prices = array();
                foreach ($prices as $price){

                    $types = PriceType::where('id',$price->price_type_id)->first();
                    $price->name = $types->name;
                    array_push($new_prices, $price);
                }

		//$categories = Category::where('account_id',Auth::user()->account_id)->orderBy('id')->get();
		$data = [
		'product' => $product,
		'method' => 'PUT',
		'url' => 'productos/' . $id,
		'title' => 'Editar Producto',
		//'categories' => $categories
                'precios'=>$new_prices,
		];
		return View::make('productos.edit', $data);

	}

	public function getViewModel(){
		return 0;
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$product = Product::where('id',$id)->firstOrFail();

		// return Response::json(Input::all());


		$product -> setProductKey(trim(Input::get('product_key')));
		$product -> setNotes(trim(Input::get('notes')));
                $product ->setUnits(Input::get('units'));
                $product->setCc(Input::get('volume'));
                $product->setPackTypes(Input::get('type'));
                $product->setIce(Input::get('ice'));
                $typePrices = PriceType::orderBy('id','ASC')->get();
                $default_cost=Input::get('price-1');
                $product->save();
                foreach ($typePrices as $type){

                    $price = Price::where('product_id',$product->id)->where('price_type_id',$type->id)->first();
                    $prices->account_id = 1;                    
                    
                    //if(Input::get('price-'.$price->id))
                        $price->cost=Input::get('price-'.$price->id);
                   // else
                     //   $price =$default_cost;
                    $price->price_type_id = $type->id;
                    $price->product_id = $product->id;
                    $price->save();
                }
                Session::flash('message',"Producto actualizado con exito");
		return Redirect::to('productos');
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function  destroy($id)
	{

                $product = Product::where('id',$id)->first();
		$product->delete();
		$message = "Producto eliminado con éxito";
		Session::flash('message', $message);
		return Redirect::to('productos');
	}



}
