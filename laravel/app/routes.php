<?php

//Test Function
Route::match( array('GET','POST'), '/hi', array('uses' => 'HomeController@hello'));

//Twilio SMS Responder
Route::match( array('GET','POST'), '/twilio/response', array('uses' => 'HomeController@twilio_response'));

// JobBoard
Route::match( array('GET','POST'), '/web/JobBoard/addJob', array('uses' => 'JobBoardController@addJob'));
Route::match( array('GET','POST'), '/web/JobBoard/getJobById', array('uses' => 'JobBoardController@getJobById'));
Route::match( array('GET','POST'), '/web/JobBoard/getJobsByZipcode', array('uses' => 'JobBoardController@getJobsByZipcode'));
Route::match( array('GET','POST'), '/web/JobBoard/getJobsByPosterId', array('uses' => 'JobBoardController@getJobsByPosterId'));
Route::match( array('GET','POST'), '/web/JobBoard/getAllJobOffers', array('uses' => 'JobBoardController@getAllJobOffers'));
Route::match( array('GET','POST'), '/web/JobBoard/getAllJobOffersInZipcode', array('uses' => 'JobBoardController@getAllJobOffersInZipcode'));
Route::match( array('GET','POST'), '/web/JobBoard/getAllJobRequests', array('uses' => 'JobBoardController@getAllJobRequests'));
Route::match( array('GET','POST'), '/web/JobBoard/markJobCompleted', array('uses' => 'JobBoardController@markJobCompleted'));
Route::match( array('GET','POST'), '/web/JobBoard/markJobNotCompleted', array('uses' => 'JobBoardController@markJobNotCompleted'));

//Web/App UI-populating functions
//leaderboard
Route::match( array('GET','POST'), '/web/leaderboard', array('uses' => 'WebController@leaderboard'));
Route::match( array('GET','POST'), '/web/getGP', array('uses' => 'WebController@GPinfo'));

//profile
Route::match( array('GET','POST'), '/web/profile', array('uses' => 'ProfileController@profile'));
Route::match( array('GET','POST'), '/web/profile/add', array('uses' => 'ProfileController@addProfile'));
Route::match( array('GET','POST'), '/web/uploadProfilePic', array('uses' => 'ProfileController@uploadProfilePic'));

//transactions
Route::match( array('GET','POST'), '/web/transactions/mine', array('uses' => 'TransactionController@getMyTransactions'));
Route::match( array('GET','POST'), '/web/transactions/latest', array('uses' => 'TransactionController@getLatestTransactions'));
Route::match( array('GET','POST'), '/web/transactions/card', array('uses' => 'TransactionController@getTransactionsByCardId'));
Route::match( array('GET','POST'), '/web/transactions/phone', array('uses' => 'TransactionController@getTransactionsByPhoneId'));
Route::match( array('GET','POST'), '/web/transaction/details', array('uses' => 'TransactionController@getTransactionInfo'));
Route::match( array('GET','POST'), '/web/transaction/uploadMedia', array('uses' => 'TransactionController@uploadMedia'));
Route::match( array('GET','POST'), '/web/uploadFirstMedia', array('uses' => 'TransactionController@uploadFirstMedia'));

//THANK YOU CARDS
Route::match(array('POST'),'/tyc/login', array('uses' => 'TycController@login'));
Route::match(array('POST'),'/tyc/signup', array('uses' => 'TycController@signup'));
Route::match(array('POST'),'/tyc/forgot', array('uses' => 'TycController@forgot'));
Route::match(array('POST'),'/tyc/submitCardID', array('uses' => 'TycController@submitCardID'));

//batch
//Route::match( array('GET','POST'), '/cards/batchadd', array('uses' => 'HomeController@batchAdd2'));
Route::match( array('GET','POST'), '/qr/ownerForCard', array('uses' => 'HomeController@qrowner'));
Route::match( array('GET','POST'), '/qr/submit', array('uses' => 'HomeController@qrscan'));
?>
