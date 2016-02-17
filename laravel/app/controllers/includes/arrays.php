<?php

class Arrays {
	public static function vendorArr(){ 
		return array(0=>"IANS",
					1=>"TEDDYWEDGERS",
					2=>"PhitnessPlus",
					3=>"BEHOPPY");
	}
	/*1. 1 slice of cheese or pepperoni pizza - 5GP
			2. Small Salad - 5 GP
			3. Premium slice - 10 GP";*/
	public static function vendorItemsArr(){
		return array(
		"IANS"=>array(array("name"=>"1 slice of cheese or pepperoni pizza","cost"=>5,"code"=>"IANSSLICE1_"),array("name"=>"Small Salad","cost"=>5,"code"=>"IANSSALAD1_"),array("name"=>"Premium Slice","cost"=>10,"code"=>"IANSSLICE2_")),
		"TEDDYWEDGERS"=>array(array("name"=>"Half Pasty (of your choice)","cost"=>3,"code"=>"TEDDYHALF"),array("name"=>"Full Pasty","cost"=>5,"code"=>"TEDDYFULL")),
		"PhitnessPlus"=>array(array("name"=>"Class Schedule","cost"=>"http://bitsy.in/6116b","code"=>""),array("name"=>"A) Yoga Class","cost"=>6,"code"=>"A"),array("name"=>"B) C.H.A.M.P Camp","cost"=>6,"code"=>"B"),array("name"=>"C) F.I.T.T Canp","cost"=>6,"code"=>"C"),array("name"=>"D) WERQ Class","cost"=>6,"code"=>"D"),array("name"=>"E) Meditation Clinic","cost"=>6,"code"=>"E")),
		"BEHOPPY"=>array(array("name"=>"A) Postcard","cost"=>1, "code"=>"A"),
		);
	}
	public static function minimumPurchaseArr(){
		return array("IANS"=>5, "TEDDYWEDGERS"=>3, "PhitnessPlus"=>6, "BEHOPPY"=>1);
	}
}
?>