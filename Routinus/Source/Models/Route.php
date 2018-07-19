<?php

//_____________________________________________________________________________________________
/**********************************************************************************************

	route model

	Author: Alexander Bassov
	Email: blackxes@gmx.de
	Github: https://www.github.com/Blackxes

/*********************************************************************************************/

namespace Routinus\Models;

//_____________________________________________________________________________________________
class Route {

	private $route;
	private $variables;
	private $selection;

	//_________________________________________________________________________________________
	public function __construct(
		string $route = "",
		array $variables = array(),
		array $selection = array() )
	{

		$this->route = $route;
		$this->variables = $variables;
		$this->selection = $selection;
	}

	//_________________________________________________________________________________________
	// basic setter/getter - didnt implement the possibility to define variables
	// post the construction
	//
	public function setRoute( string $route ) { $this->route = $route; }
	public function setSelection( array $selection ) { $this->selection = $selection; }
	//
	public function getRoute(): string { return $this->route; }
	public function getVariables(): array { return $this->variables; }
	public function getVariable( $key ): string { return $this->variables[$key]; }
	public function getSelection(): array { return $this->selection; }
}

//_____________________________________________________________________________________________
//