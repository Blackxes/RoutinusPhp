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

namespace Routinus;

const ROUTINUS_ROOT = __DIR__;
const DS = DIRECTORY_SEPARATOR;

require_once( ROUTINUS_ROOT . DS . "Dependencies" . DS . "Logfile" . DS . "Logfile.php" );
require_once( ROUTINUS_ROOT . DS . "Configuration.php" );
require_once( ROUTINUS_ROOT . DS . "Source" . DS . "Models" . DS . "Route.php" );

//_____________________________________________________________________________________________
class Routinus {

	private $route;
	private $routePieces; // the pieces are used by multiple functions
		// thats why they are defines as class members

	public $logfile;

	//_________________________________________________________________________________________
	public function __construct() {

		$argname = \Routinus\Configuration\ROUTINUS_ROUTE_ARGUMENTNAME;

		$this->route = ( \Routinus\Configuration\ROUTINUS_ROUTE_METHOD == "get" )
			? isset($_GET[$argname]) ? $_GET[$argname] : ""
			: isset($_POST[$argname]) ? $_POST[$argname] : "";

		$this->routePieces = array_filter( explode( "/", $this->route ) );
		
		$this->logfile = new \Logfile\Logfile();
	}

	//_________________________________________________________________________________________
	// parses the route and returns a route object
	//
	// param1 (callable) expects the callback which is called to get an array
	//		containing the possible routes the requested route might match
	//
	//		Arguments: ( $segmentRegex )
	//			param1 (string) expects the regex that matches a route that has the same
	//				amount of segments the requested route has
	//
	// return \Routinus\Models\Route - the parsed route
	// return null - when selection empty/ no route is given/ no route matched
	//		check the internal logfile for specific information
	//
	public function parse( callable $callback ) {

		if ( !$this->route )
			return $this->logfile->logReturn( "Routinus: no route requested", null );
		
		// build regex to grab the segment count matched routes
		$selection = $callback( $this->buildGrabex() );

		if ( !$selection || !is_array($selection) )
			return $this->logfile->logReturn( "Routinus: selection array is empty or not array", null );
		
		// get requested route
		$requestedRoute = $this->selectRoute( $selection );

		return $requestedRoute ? $requestedRoute : $this->logfile->logReturn( "Routinus: no route matched", null );
	}

	//_________________________________________________________________________________________
	// builds and returns a regex based on how many segments the requested route has
	// that regex matches a route containing the same number of segments
	//
	private function buildGrabex(): string {

		// split route and build regex that matches the equal segment count
		$segments = array_fill( 0, count($this->routePieces), "(?:[^\/]*)" );
		
		return "/^" . implode( "\/+", $segments ) . "[\/]*$/";
	}

	//_________________________________________________________________________________________
	// selets (when matched) the requested route from the given selection
	//
	// param1 (array) expects an array containing routes that may match current requested route
	//
	// return \Routinus\Models\Route - the parsed object
	// return null - when no route matched
	//
	private function selectRoute( $routes ) {

		// check absolute match
		$absMatch = array_search( $this->route, $routes );

		if ( $absMatch ) return new \Routinus\Models\Route( $routes[$absMatch] );

		foreach( $routes as $i => $route ) {

			$result = $this->checkRoute( $route );
		
			if ( $result ) {
				
				$result->setSelection( $routes );
				return $result;
			}
		}

		return null;
	}

	//_________________________________________________________________________________________
	// checks the given route against the requested route
	//
	// param1 (string) expects the route thats being checked
	//
	// return null | \Routinus\Models\Route
	// 		\Routinus\Models\Route - route matches
	//		null - route doesnt match
	//
	private function checkRoute( $route ) {

		$variables = array();
	
		// check given route with user route segment by segment variable
		// segments will match but have less prio than absolute segments
		foreach( explode("/", $route) as $i => $piece ) {
			
			// absolute match
			if ( $piece == $this->routePieces[$i] ) continue;

			// // variable match
			preg_match( "/^{(.*)}$/", $piece, $result );
			
			if ( $result ) {

				$variables[$result[1]] = $this->routePieces[$i];
				continue;
			}

			// // when nothing resolve to true the route doesnt match
			return null;
		}
		
		// at this point the route matches since everything has been continued
		return new \Routinus\Models\Route( $route, $variables );
	}
}

//_____________________________________________________________________________________________
//