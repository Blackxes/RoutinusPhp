<?php

//_____________________________________________________________________________________________
/**********************************************************************************************

	routinus configuration file

	Author: Alexander Bassov
	Email: blackxes@gmx.de
	Github: https://www.github.com/Blackxes

/*********************************************************************************************/

namespace Routinus\Configuration;

//_____________________________________________________________________________________________
// defines the parameter name
const ROUTINUS_ROUTE_ARGUMENTNAME = "r";

// defines how the route should be pulled
// tbh i dont know a situation where you could use post but just in case
//
// INFO: when the value is not get - post is used - doesnt matter whats written there
// since there are only 2 methods checking for both doesnt make sense when one is not defined
const ROUTINUS_ROUTE_METHOD = "get";

//_____________________________________________________________________________________________
//