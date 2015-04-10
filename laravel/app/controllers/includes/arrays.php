<?php

class Arrays {
	public static function vendorArr(){ 
		return array(0=>"IANS");
	}
	/*1. 1 slice of cheese or pepperoni pizza - 5GP
			2. Small Salad - 5 GP
			3. Premium slice - 10 GP";*/
	public static function vendorItemsArr(){
		return array("IANS"=>array(array("name"=>"1 slice of cheese or pepperoni pizza","cost"=>5,"code"=>"IANSSLICE1_"),array("name"=>"Small Salad","cost"=>5,"code"=>"IANSSALAD1_"),array("name"=>"Premium Slice","cost"=>10,"code"=>"IANSSLICE2_")));
	}
	public static function minimumPurchaseArr(){
		return array("IANS"=>5);
	}
}
?>