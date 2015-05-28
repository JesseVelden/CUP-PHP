<?php
error_reporting(E_ERROR);
require "api.php";

//First initialize
$viewstate = ""; //The viewstate of the homepage of your CUP's Site. This must be set!!
$cookieFolder = '/tmp/'; //The folder you want to save the cookies to.
cupphp\initialize($viewstate, $cookieFolder);

$filter='Hoek'; //Put here the name
$password='1234'
$schoolUrl='ed.cupweb6.nl'; //the URL of your School's CUP website.

$names=cupphp\getNames($filter,$schoolUrl);
var_dump($names);
echo '<br><br>';
var_dump(cupphp\getTimeTable($names->names[0]->username, $password, $schoolUrl, $names->sessionId, $names->eventvalidation)); //Get the Timetable of the first User that can be called with the name: 'Hoek' with using the password: '1234'
//Make sure the $EventValidation variable is url encoded when you get this variable from a get request for example.
?>
