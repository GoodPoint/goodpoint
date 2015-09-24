<?php
	$array1 = array(0,0);
	$array2 = array(0,0);

	for ($x = 0; $x < 100; $x++) {
	  $array1[mt_rand(0,1)] ++;
	  $array2[rand(0,1)] ++;
	}

	print_r($array1);
	print_r($array2);
?>