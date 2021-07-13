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

// Admin
define('S_ADMIN',       '01');

// Admin root
define('SA_AUDIT',      S_ADMIN . '01');
define('SA_BILLING',    S_ADMIN . '02');
define('SA_CONFIG',     S_ADMIN . '03');
define('SA_FORMS',      S_ADMIN . '04');
define('SA_GROUPS',     S_ADMIN . '05');
define('SA_PERIODS',    S_ADMIN . '06');
define('SA_SECURITY',   S_ADMIN . '08');
define('SA_USERS',      S_ADMIN . '09');
define('SA_ROUTES',     S_ADMIN . '10');
define('SA_CUSTOMERS',  S_ADMIN . '11');

// Admin / Customers "C"
define('SAC_BILLING',   SA_CUSTOMERS . '01');
define('SAC_RATES',     SA_CUSTOMERS . '02');
define('SAC_TYPES',     SA_CUSTOMERS . '03');

// Admin / Customers / Rates "R"
define('SACR_ADD',      SAC_RATES . '01');
define('SACR_EDIT',     SAC_RATES . '02');
define('SACR_VIEW',     SAC_RATES . '03');

// Admin / Customers / Types "T"
define('SACT_ADD',      SAC_TYPES . '01');
define('SACT_EDIT',     SAC_TYPES . '02');
define('SACT_VIEW',     SAC_TYPES . '03');

// Admin / Groups "G"
define('SAG_ADD',       SA_GROUPS . '01');
define('SAG_EDIT',      SA_GROUPS . '02');

// Admin / Periods "P"
define('SAP_ADD',       SA_PERIODS . '01');
define('SAP_EDIT',      SA_PERIODS . '02');

// Admin / Routes "R"
define('SAR_ADD',       SA_ROUTES . '01');
define('SAR_EDIT',      SA_ROUTES . '02');

// Admin / Users "U"
define('SAU_ADD',       SA_USERS . '01');
define('SAU_EDIT',      SA_USERS . '02');

//-------------------------------------------

// Customers    
define('S_CUSTOMERS',       '03');

// Customers root "C"
define('SC_ADDNEW',         S_CUSTOMERS . '01');
define('SC_BILLING',        S_CUSTOMERS . '04');
define('SC_EDIT',           S_CUSTOMERS . '05');
define('SC_FLAGSTOPS',      S_CUSTOMERS . '06');
define('SC_SEARCH',         S_CUSTOMERS . '07');
define('SC_PAYMENTS',       S_CUSTOMERS . '08');
define('SC_REPORTS',        S_CUSTOMERS . '09');
define('SC_SEQUENCING',     S_CUSTOMERS . '10');
define('SC_VIEW',           S_CUSTOMERS . '11');
define('SC_POPUP',          S_CUSTOMERS . '12');
define('SC_COMBINED',       S_CUSTOMERS . '13');

// Customers / Admin / Combined "C"
define('SCC_ADD',           SC_COMBINED . '01');
define('SCC_EDIT',          SC_COMBINED . '02');

// Customers / Billing "B"
define('SCB_BILL',          SC_BILLING . '02');
define('SCB_DOWNLOAD',      SC_BILLING . '03');
define('SCB_LOG',           SC_BILLING . '04');
define('SCB_REPAIR',        SC_BILLING . '05');

// Customers / Billing / Bill "B"
define('SCBB_STEP1',        SCB_BILL . '01');
define('SCBB_STEP2',        SCB_BILL . '02');
define('SCBB_STEP3',        SCB_BILL . '03');
define('SCBB_STEP4',        SCB_BILL . '04');
define('SCBB_STEP5',        SCB_BILL . '05');
define('SCBB_STEP6',        SCB_BILL . '06');

// Customers / Edit "E"
define('SCE_ADDRESSES',     SC_EDIT . '01');
define('SCE_ADJUSTMENTS',   SC_EDIT . '02');
define('SCE_BILLS',         SC_EDIT . '03');
define('SCE_BILLLOG',       SC_EDIT . '04');
define('SCE_COMPLAINTS',    SC_EDIT . '05');
define('SCE_MAP',           SC_EDIT . '06');
define('SCE_NOTES',         SC_EDIT . '07');
define('SCE_PAYMENTS',      SC_EDIT . '08');
define('SCE_SERVICE',       SC_EDIT . '09');
define('SCE_SERVICETYPES',  SC_EDIT . '10');
define('SCE_SUMMARY',       SC_EDIT . '11');
define('SCE_BILLING',       SC_EDIT . '12');

// Customers / Payments "P"
define('SCP_ADD',           SC_PAYMENTS . '01');
define('SCP_LOOKUP',        SC_PAYMENTS . '03');

// Customers / Reports "R"
define('SCR_AHEAD',         SC_REPORTS . '01');
define('SCR_BEHIND',        SC_REPORTS . '02');
define('SCR_ORDERS',        SC_REPORTS . '04');
define('SCR_STOPPED',       SC_REPORTS . '05');

// Customers / View "V"
define('SCV_ADDRESSES',     SC_VIEW . '01');
define('SCV_ADJUSTMENTS',   SC_VIEW . '02');
define('SCV_BILLS',         SC_VIEW . '03');
define('SCV_BILLLOG',       SC_VIEW . '04');
define('SCV_COMPLAINTS',    SC_VIEW . '05');
define('SCV_MAP',           SC_VIEW . '06');
define('SCV_NOTES',         SC_VIEW . '07');
define('SCV_PAYMENTS',      SC_VIEW . '08');
define('SCV_SERVICE',       SC_VIEW . '09');
define('SCV_SERVICETYPES',  SC_VIEW . '10');
define('SCV_SUMMARY',       SC_VIEW . '11');
define('SCV_BILLING',       SC_VIEW . '12');

// Customers / popups "W"
define('SCW_ADJUSTMENT',    SC_POPUP . '01');
define('SCW_CHANGETYPE',    SC_POPUP . '02');
define('SCW_COMPLAINT',     SC_POPUP . '03');
define('SCW_STOPSTART',     SC_POPUP . '04');
define('SCW_STARTSTOP',     SC_POPUP . '05');

//-------------------------------------------

// root
define('S_HOME',    '04');

// Home root
define('SM_HOME',   S_HOME . '01');

//-------------------------------------------

define('S_ADJUSTMENT',  '06');

define('SD_ADD',        S_ADJUSTMENT . '01');
define('SD_EDIT',       S_ADJUSTMENT . '02');
define('SD_VIEW',       S_ADJUSTMENT . '03');

//-------------------------------------------

define('S_BILL',    '07');

define('SL_ADD',    S_BILL . '01');
define('SL_EDIT',   S_BILL . '02');
define('SL_VIEW',   S_BILL . '03');

//-------------------------------------------

define('S_SERVICE', '08');

define('SV_ADD',    S_SERVICE . '01');
define('SV_EDIT',   S_SERVICE . '02');
define('SV_VIEW',   S_SERVICE . '03');

//-------------------------------------------

define('S_PAYMENT', '09');

define('SP_ADD',    S_PAYMENT . '01');
define('SP_EDIT',   S_PAYMENT . '02');
define('SP_VIEW',   S_PAYMENT . '03');

//-------------------------------------------

define('S_SERVICETYPE', '10');

define('SW_ADD',        S_SERVICETYPE . '01');
define('SW_EDIT',       S_SERVICETYPE . '02');
define('SW_VIEW',       S_SERVICETYPE . '03');

//-------------------------------------------

define('S_SECURITY',    '11');

define('ST_ADD',        S_SECURITY . '01');
define('ST_EDIT',       S_SECURITY . '02');

//-------------------------------------------

define('S_CONFIG',          '12');

define('SI_ADD',            S_CONFIG . '01');
define('SI_EDIT',           S_CONFIG . '02');

//-------------------------------------------

define('S_PERIOD',  '13');

define('SO_ADD',    S_PERIOD . '01');
define('SO_EDIT',   S_PERIOD . '02');

//-------------------------------------------

// NOTE:  S_ROUTES and S_ROUTE should be merged ...
//        Former refers to menu, latter to popups

define('S_ROUTE',   '15');

define('SU_ADD',    S_ROUTE . '01');
define('SU_EDIT',   S_ROUTE . '02');

//-------------------------------------------

define('S_GROUP',   '17');

define('SJ_ADD',    S_GROUP . '01');
define('SJ_EDIT',   S_GROUP . '02');

//-------------------------------------------

define('S_USER',    '18');

define('SS_ADD',    S_USER . '01');
define('SS_EDIT',   S_USER . '02');

//-------------------------------------------

define('S_PROFILE', '19');

define('SK_ADD',    S_PROFILE . '01');
define('SK_EDIT',   S_PROFILE . '02');

//-------------------------------------------

// Stores
define('S_STORES', '20');

// Stores root "R"
define('SR_STORES', S_STORES . '01');

//-------------------------------------------

define('S_COMPLAINTS',  '21');

define('SQ_ADD',        S_COMPLAINTS . '01');
define('SQ_EDIT',       S_COMPLAINTS . '02');
define('SQ_VIEW',       S_COMPLAINTS . '03');

//-------------------------------------------

// Routes
define('S_ROUTES',      '22');

// Routes root "E"
define('SE_REPORTS',    S_ROUTES . '01');
define('SE_SEQUENCING', S_ROUTES . '02');

// Routes / Reports "R"
define('SER_CHANGES',   SE_REPORTS . '01');
define('SER_DRAW',      SE_REPORTS . '02');
define('SER_ROUTE',     SE_REPORTS . '03');
define('SER_STATUS',    SE_REPORTS . '04');
define('SER_TIPS',      SE_REPORTS . '05');

// Routes / Reports / Changes "C"
define('SERC_HISTORY',  SER_CHANGES . '01');
define('SERC_NOTES',    SER_CHANGES . '02');
define('SERC_REPORT',   SER_CHANGES . '03');

//-------------------------------------------

define('S_MAINTENANCE', '23');

?>
