<?php

//Test Function
Route::match( array('GET','POST'), '/hi', array('uses' => 'HomeController@hello'));

//Twilio SMS Responder
Route::match( array('GET','POST'), '/twilio/response', array('uses' => 'HomeController@twilio_response'));

//Web/App UI-populating functions
//leaderboard
Route::match( array('GET','POST'), '/web/leaderboard', array('uses' => 'WebController@leaderboard'));
//profile
Route::match( array('GET','POST'), '/web/profile', array('uses' => 'ProfileController@profile'));
Route::match( array('GET','POST'), '/web/profile/add', array('uses' => 'ProfileController@addProfile'));
Route::match( array('GET','POST'), '/web/uploadProfilePic', array('uses' => 'ProfileController@uploadProfilePic'));

//transactions
Route::match( array('GET','POST'), '/web/transactions/mine', array('uses' => 'TransactionController@getMyTransactions'));
Route::match( array('GET','POST'), '/web/transactions/latest', array('uses' => 'TransactionController@getLatestTransactinos'));
Route::match( array('GET','POST'), '/web/transactions/card', array('uses' => 'TransactionController@getTransactionsByCardId'));
Route::match( array('GET','POST'), '/web/transactions/phone', array('uses' => 'TransactionController@getTransactionsByPhoneId'));
Route::match( array('GET','POST'), '/web/transaction/details', array('uses' => 'TransactionController@getTransactionInfo'));
Route::match( array('GET','POST'), '/web/transaction/uploadMedia', array('uses' => 'TransactionController@uploadMedia'));

//batch
Route::match( array('GET','POST'), '/cards/batchadd', array('uses' => 'HomeController@batchAdd'));
Route::match( array('GET','POST'), '/qr/ownerForCard', array('uses' => 'HomeController@qrowner'));
Route::match( array('GET','POST'), '/qr/submit', array('uses' => 'HomeController@qrscan'));
?>