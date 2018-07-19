<?php

//_____________________________________________________________________________________________
/**********************************************************************************************

	Let the routing begin

	Routinus just takes the url addition (the part without the base)
	and selects out of a set of given routes the one which fits the most
	with respect to some rules - listed below

	routes are specifically identified and initially raw selected by their count of segments
	each segments represents a "folder-level"
	/product/view -> 2 segments
	/product/32/view -> 3 segments
	/user/876/settings/edit -> 4 segments .. etc

	placeholder within the routes provide the possibility to dynamically select only one route
	and pass the placeholder value given within the route to the callback/class
	whatever you want to do with it

	this class only selects the route and returns a \Routinus\Route instance
	containing (when provided) the defined placeholder as $placeholder => $value

	the route selection priority is defined by the position of the placeholder
	-> absolute routes have the highest priority
	-> placeholder defined in closer segments have less prio than defined further away

	So.. lets have fun routing around
	- Routinus 2018

	Author: Alexander Bassov
	Email: blackxes@gmx.de
	Github: https://www.github.com/Blackxes

/*********************************************************************************************/


require_once( __DIR__ . "/Routinus/Routinus.php" );

$router = new \Routinus\Routinus();

$route = $router->parse( function($regex) {

	$result = preg_grep( $regex, array(
		"user/23",									// olaf
		"user/view",								// general overview of the users
		"user/{userid}",							// dynamic user view / maybe peter? or even maria?
		"product/993562/edit",						// quickly changing the image
		"product/{productid}/details/{reportid}",
		"product/{productid}",						// looks nice, but.. hm..
		"product/23",								// absolute product
		"products"									// lets look for something else
	));

	return $result;
});

foreach( $router->logfile->getOpenLogs() as $index => $log )
	print_r($log->getMessage());

print_r($route);

//_____________________________________________________________________________________________
//