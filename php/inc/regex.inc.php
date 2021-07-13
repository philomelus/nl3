<?php
/*
	Copyright 2005, 2006, 2007, 2008 Russell E. Gibson

    This file is part of NewsLedger.

    NewsLedger is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    NewsLedger is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with NewsLedger; see the file LICENSE.  If not, see
	<http://www.gnu.org/licenses/>.
*/

	//  1    2   3        5
	// (###) ###-#### ext @@@@@@@@@@
	define('RE_TEL_AREACODE', '^[[:digit:]]{3}$');
	define('RE_TEL_PREFIX', '^[[:digit:]]{3}$');
	define('RE_TEL_NUMBER', '^[[:digit:]]{4}$');
	define('RE_TEL_EXTENSION', '^[[:alnum:]]{0,10}$');
	define('RE_TELEPHONE', '^\(([[:digit:]]{3})\) ([[:digit:]]{3})-([[:digit:]]{4})( [Ee][Xx][Tt] ([[:alnum:]]{0,10}))?$');

	define('RE_MONEY', '^[+-]?([[:digit:]]+\.?)|([[:digit:]]+\.?[[:digit:]]{0,2})$');	// Not a very good version...
	
	define('RE_ID_C_COMPLAINT', '^[[:digit:]]{1,8}$');			// BUGBUG:  Will pass 0 as valid
	define('RE_ID_CUSTOMER', '^[[:digit:]]{1,8}$');				// BUGBUG:  Will pass 0 as valid
	define('RE_ID_ROUTE', '^[[:digit:]]{1,8}$');				// BUGBUG:  Will pass 0 as valid
	define('RE_ID_C_SERVICE', '^[[:digit:]]{1,8}$');			// BUGBUG:  Will pass 0 as valid
	define('RE_ID_C_SERVICETYPE', '^[[:digit:]]{1,8}$');		// BUGBUG:  Will pass 0 as valid
	define('RE_ID_C_TYPE', '^[[:digit:]]{1,8}$');				// BUGBUG:  Will pass 0 as valid
	
// These 4 are deprecated
	define('RE_ID_COMPLAINT', '^[[:digit:]]{1,8}$');			// BUGBUG:  Will pass 0 as valid
	define('RE_ID_SERVICE', '^[[:digit:]]{1,8}$');				// BUGBUG:  Will pass 0 as valid
	define('RE_ID_SERVICETYPE', '^[[:digit:]]{1,8}$');			// BUGBUG:  Will pass 0 as valid
	
	define('RE_ADDRESS', '^.+$');
	define('RE_CITY', '^.+$');
	define('RE_STATE', '^[A-Z]{2}$');
	define('RE_ZIP', '^[0-9]{5}(-[0-9]{4})?$');
	
?>
