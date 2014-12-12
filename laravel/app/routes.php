<?php

//Test Function
Route::match( array('GET','POST'), '/hi', array('uses' => 'HomeController@hello'));

//Twilio SMS Responder
Route::match( array('GET','POST'), '/twilio/response', array('uses' => 'HomeController@twilio_response'));

//Web/App UI-populating functions

?>