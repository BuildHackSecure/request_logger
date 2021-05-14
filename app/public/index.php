<?php
include_once('../Autoload.php');
include_once('../Route.php');
include_once('../Output.php');
include_once('../View.php');
\Controller\Domain::set( trim(file_get_contents('../domain.txt')) );
Route::load();
Route::run();