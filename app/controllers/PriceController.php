<?php

class PriceController extends \BaseController {
    public function index(){
        $prices = PriceType::get();
        $data = [
            'precios'=> $prices,
        ];
        return View::make('precios.index', $data);
    }
    
    public function create(){
        $products = Product::orderBy('id','ASC')->get();
        $data = [
            'productos'=>$products
        ];
        return View::make('precios.create', $data);
    }
    
    public function store(){
        $priceType = PriceType::createNew();
        $priceType->name = Input::get('name');
        $priceType->save();
        $products = Product::get();
        $default_cost=Input::get('price-1');
        foreach($products as $product)
        {
            $price = Price::createNew();
            $price->account_id = 1;
            //if(Input::get('price-'.$product->id))                    
                $price->cost=Input::get('price-'.$priceType->id);                    
            //else                    
              // $price =$default_cost;       
            $price->price_type_id = $priceType->id;
            $price->product_id = $product->id;            
            $price->save();
        }
        
        Session::flash('message',"Precio creado con exito");
        return Redirect::to('precios');        
    }
    public function edit($id){
        $prices = Price::where('price_type_id',$id)->get();
        $prices =  Price::join('products', 'products.id', '=', 'prices.product_id') 
                ->where('price_type_id',$id)
                ->select('prices.id', 'prices.cost', 'prices.price_type_id','prices.product_id', 'products.notes','products.product_key')->get();
        
        
        
        $name = PriceType::where('id',$id)->first();
        $data=[
            'precios'=> $prices,
            'nombre'=> $name,
        ];
        return View::make('precios.edit', $data);
    }
    
    public function update($id){
         $priceType = PriceType::where('id',$id)->first();
        $priceType->name = Input::get('name');
        $priceType->save();
        $products = Product::get();
        //$default_cost=Input::get('price-1');
        //$type
        $prices = Price::where('price_type_id',$id)->get();
        
        foreach($prices as $price)
        {
            $pri = Price::where('id',$price->id)->first();
            
            //if(Input::get('price-'.$pri->id))                    
                $price->cost=Input::get('price-'.$pri->id);                                      
            //$price->price_type_id = $priceType->id;
            //$price->product_id = $product->id;            
            $price->save();
        }
        
        Session::flash('message',"Precio actualizado con exito");
        return Redirect::to('precios'); 
    }
}
?>
