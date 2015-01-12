<?php

//Test Function
Route::match( array('GET','POST'), '/hi', array('uses' => 'HomeController@hello'));

//Twilio SMS Responder
Route::match( array('GET','POST'), '/twilio/response', array('uses' => 'HomeController@twilio_response'));

//Web/App UI-populating functions
Route::match( array('GET','POST'), '/web/leaderboard', array('uses' => 'WebController@leaderboard'));
Route::match( array('GET','POST'), '/web/profile', array('uses' => 'WebController@profile'));
Route::match( array('GET','POST'), '/web/profile/add', array('uses' => 'WebController@addProfile'));
Route::match( array('GET','POST'), '/web/uploadProfilePic', array('uses' => 'WebController@uploadProfilePic'));
?>