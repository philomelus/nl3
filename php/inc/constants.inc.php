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

	define('DQ', '"');
	define('NL', "\n");
	define('SQ', '\'');
	
// Billing states
	define('BILL_PENDING', 0);							// Ready to run actual billing
	define('BILL_RUNNING', 1);							// During actual billing (after 1st, before nth)
	define('BILL_GENERATED', 2);						// Automated process complete
	define('BILL_COMBINED', 3);							// Combined bills have been combined
	define('BILL_COMPLETE', 4);							// Files have been downloaded at least 1 time
	
// Configuration Value Types
	define('CFG_BOOLEAN',	'BOOLEAN');
	define('CFG_COLOR',		'COLOR');
	define('CFG_ENUM',		'ENUM');
	define('CFG_FLOAT',		'FLOAT');
	define('CFG_INTEGER',	'INTEGER');
	define('CFG_MONEY',		'MONEY');
	define('CFG_PERIOD',	'IID');
	define('CFG_ROUTE',		'RID');
	define('CFG_TELEPHONE',	'TELEPHONE');
	define('CFG_TYPE',		'TID');
	define('CFG_STRING',	'STRING');
	define('CFG_LIST',		'LIST');
	
// Customer Addresses Sequence
	define('ADDR_C_DELIVERY',	1);						// Delivery address
	define('ADDR_C_BILLING',	101);					// Billing address
	
// Customer titles
	define('NAME_T_NONE',		'');
	define('NAME_T_MR',			'Mr');
	define('NAME_T_MRS',		'Mrs');
	define('NAME_T_MS',			'Ms');
	define('NAME_T_MISS',		'Miss');
	
// Customer Names Sequence
	define('NAME_C_DELIVERY1',	1);						// Primary delivery name
	define('NAME_C_DELIVERY2',	2);						// Alternate delivery name
	define('NAME_C_BILLING1',	101);					// Primary billing name
	define('NAME_C_BILLING2',	102);					// Alternate billing name

// Customer Rate Types
	define('RATE_STANDARD',		'STANDARD');			// Standard rate
	define('RATE_REPLACE',		'REPLACE');				// Replace the standard rate
	define('RATE_SURCHARGE',	'SURCHARGE');			// Add amount to standard rate (credit or debit)
	
// Customer Telephone Sequence
	define('TEL_C_DELIVERY1',	1);						// Delivery Telephone 1
	define('TEL_C_DELIVERY2',	2);						// Delivery Telephone 2
	define('TEL_C_DELIVERY3',	3);						// Delivery Telephone 3
	define('TEL_C_BILLING1',	101);					// Billing Telephone 1
	define('TEL_C_BILLING2',	102);					// Billing Telephone 2
	define('TEL_C_BILLING3',	103);					// Billing Telephone 3

// Customer Service Types
	define('SERVICE_STOP', 	'STOP');					// Stop delivery
	define('SERVICE_START',	'START');					// Restart delivery
	
// Customer Complaint Results
	define('RESULT_NOTHING',		'NONE');			// No action taken
	define('RESULT_CREDIT1DAILY',	'CREDITDAILY');		// Credited customer 1 daily paper
	define('RESULT_CREDIT1SUNDAY',	'CREDITSUNDAY');	// Credited customer 1 sunday paper
	define('RESULT_REDELIVERED',	'REDELIVERED');		// Paper was redeliverd to customer
	define('RESULT_CREDIT',			'CREDIT');			// Gave customer some amount
	define('RESULT_CHARGE',			'CHARGE');			// Charged customer some amount

// Customer Complaint Types
	define('BITCH_MISSED',	'MISSED');					// Missed paper
	define('BITCH_WET', 	'WET');						// Wet paper
	define('BITCH_DAMAGED',	'DAMAGED');					// Damaged paper
	
// Payment types
	define('PAYMENT_CHECK',			0);					// Payment was by check
	define('PAYMENT_MONEYORDER',	1);					// Payment was by cashiers check or money order
	define('PAYMENT_CASH',			2);					// Payment was by cash (discourage use of this!)
	define('PAYMENT_CREDIT',		3);					// Payment by credit card
	
// Sequence ID for newly added customer
	define('CUSTOMER_ADDSEQUENCE', 99999);				// Initial sequnce for a new customer
?>
