<?
// script to load initial data into database

date_default_timezone_set('America/Denver');
require '../vendor/autoload.php';

// initialize RedBean
R::setup('sqlite:../dbase.sqlite');

include 'init.php';
include 'production.php';