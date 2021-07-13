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

	class SecurityDataBase
	{
		protected function initialize(&$v)
		{
			$v = array
				(
					S_ADMIN => array
						(
							'page' => 'Allowed to access Admin',
						),
					S_CUSTOMERS => array
						(
							'page' => 'Allowed to access Customers',
						),
					S_HOME => array
						(
							'page' => 'Allowed to access Home',
						),
					S_ADJUSTMENT => array
						(
							'page' => 'Allowed to access Adjustment',
						),
					S_BILL => array
						(
							'page' => 'Allowed to access Bill',
						),
					S_SERVICE => array
						(
							'page' => 'Allowed to access Service',
						),
					S_PAYMENT => array
						(
							'page' => 'Allowed to access Payment',
						),
					S_SERVICETYPE => array
						(
							'page' => 'Allowed to access ServiceType',
						),
					S_SECURITY => array
						(
							'page' => 'Allowed to access Security',
						),
					S_CONFIG => array
						(
							'page' => 'Allowed to access Configuration',
						),
					S_PERIOD => array
						(
							'page' => 'Allowed to access Period',
						),
					S_ROUTE => array
						(
							'page' => 'Allowed to access Route',
						),
					S_GROUP => array
						(
							'page' => 'Allowed to access Group',
						),
					S_USER => array
						(
							'page' => 'Allowed to access User',
						),
					S_PROFILE => array
						(
							'page' => 'Allowed to access Profile',
						),
					S_STORES => array
						(
							'page' => 'Allowed to access Stores',
						),
					S_COMPLAINTS => array
						(
							'page' => 'Allowed to access Complaints',
						),
					S_ROUTES => array
						(
							'page' => 'Allowed to access Routes',
						),
					SA_AUDIT => array
						(
							'page' => 'Allowed to access Admin / Audit',
						),
					SA_BILLING => array
						(
							'page' => 'Allowed to access Admin / Billing',
						),
                    SA_CUSTOMERS => array
                        (
                            'page' => 'Allowed to access Admin / Customers',
                        ),
					SA_CONFIG => array
						(
							'page' => 'Allowed to access Admin / Configuration',
						),
                    SAC_BILLING => array
                        (
                            'page' => 'Allowed to access Admin / Customers / Billing',
                        ),
                    SAC_RATES => array
                        (
                            'page' => 'Allowed to access Admin / Customers / Rates',
                            'period-edit' => 'Allowed to edit period',
                            'period-view' => 'Allowed to view period',
                        ),
                    SAC_TYPES => array
                        (
                            'page' => 'Allowed to access Admin / Customers / Types',
                        ),
                    SACR_ADD => array
                        (
                            'page' => 'Allowed to access Admin / Customers / Rates / Add',
                        ),
                    SACR_EDIT => array
                        (
                            'page' => 'Allowed to access Admin / Customers / Rates / Edit',
                        ),
                    SACR_VIEW => array
                        (
                            'page' => 'Allowed to access Admin / Customers / Rates / View',
                        ),
                    SACT_ADD => array
                        (
                            'page' => 'Allowed to access Admin / Customers / Types / Add',
                        ),
                    SACT_EDIT => array
                        (
                            'page' => 'Allowed to access Admin / Customers / Types / Edit',
                        ),
                    SACT_VIEW => array
                        (
                            'page' => 'Allowed to access Admin / Customers / Types / View',
                        ),
					SA_FORMS => array
						(
							'page' => 'Allowed to access Admin / Forms',
						),
					SA_GROUPS => array
						(
							'page' => 'Allowed to access Admin / Groups',
						),
					SA_PERIODS => array
						(
							'page' => 'Allowed to access Admin / Periods',
						),
                    SA_ROUTES => array
                        (
                            'page' => 'Allowed to access Admin / Routes',
                        ),
					SA_SECURITY => array
						(
							'page' => 'Allowed to access Admin / Security',
						),
					SA_USERS => array
						(
							'page' => 'Allowed to access Admin / Users',
						),
					SAG_ADD => array
						(
							'page' => 'Allowed to access Admin / Groups / Add',
						),
					SAG_EDIT => array
						(
							'page' => 'Allowed to access Admin / Groups / Edit',
						),
					SAP_ADD => array
						(
							'page' => 'Allowed to access Admin / Periods / Add',
						),
					SAP_EDIT => array
						(
							'page' => 'Allowed to access Admin / Periods / Edit',
						),
                    SAR_ADD => array
                        (
                            'page' => 'Allowed to access Admin / Routes / Add',
                        ),
                    SAR_EDIT => array
                        (
                            'page' => 'Allowed to access Admin /Routes / Edit',
                        ),
					SAU_ADD => array
						(
							'page' => 'Allowed to access Admin / Users / Add',
						),
					SAU_EDIT => array
						(
							'page' => 'Allowed to access Admin / Users / Edit',
						),
					SC_ADDNEW => array
						(
							'page' => 'Allowed to access Customers / New',
						),
					SC_BILLING => array
						(
							'page' => 'Allowed to access Customers / Billing',
						),
					SC_EDIT => array
						(
							'page' => 'Allowed to access Customers / Edit',
						),
					SC_FLAGSTOPS => array
						(
							'page' => 'Allowed to access Customers / Flag Stops',
						),
					SC_SEARCH => array
						(
							'page' => 'Allowed to access Customers / Search',
						),
					SC_PAYMENTS => array
						(
							'page' => 'Allowed to access Customers / Payments',
						),
					SC_REPORTS => array
						(
							'page' => 'Allowed to access Customers / Reports',
						),
					SC_SEQUENCING => array
						(
							'page' => 'Allowed to access Customers / Sequencing',
						),
					SC_VIEW => array
						(
							'page' => 'Allowed to access Customers / View',
						),
					SC_POPUP => array
						(
							'page' => 'Allowed to access Customer popups',
						),
                    SC_COMBINED => array
                        (
                            'page' => 'Allowed to access Customers / Admin / Combined',
                            'customer-edit' => 'Allowed to edit customer',
                            'customer-view' => 'Allowed to view customer',
                        ),
                    SCC_ADD => array
                        (
                            'page' => 'Allowed to access Customers / Combined / Add',
                        ),
                    SCC_EDIT => array
                        (
                            'page' => 'Allowed to access Customers / Combined / Edit',
                        ),
					SCB_BILL => array
						(
							'page' => 'Allowed to access Customers / Billing / Bill',
						),
					SCB_DOWNLOAD => array
						(
							'page' => 'Allowed to access Customers / Billing / Download',
						),
					SCB_LOG => array
						(
							'page' => 'Allowed to access Customers / Billing / Log',
							'period-edit' => 'Allowed to edit period',
							'period-view' => 'Allowed to view period',
							'customer-edit' => 'Allowed to edit customer',
							'customer-view' => 'Allowed to view customer',
						),
					SCB_REPAIR => array
						(
							'page' => 'Allowed to access Customers / Billing / Repair',
						),
					SCBB_STEP1 => array
						(
							'page' => 'Allowed to access Customers / Billing / Bill / Step 1',
						),
					SCBB_STEP2 => array
						(
							'page' => 'Allowed to access Customers / Billing / Bill / Step 2',
						),
					SCBB_STEP3 => array
						(
							'page' => 'Allowed to access Customers / Billing / Bill / Step 3',
						),
					SCBB_STEP4 => array
						(
							'page' => 'Allowed to access Customers / Billing / Bill / Step 4',
						),
					SCBB_STEP5 => array
						(
							'page' => 'Allowed to access Customers / Billing / Bill / Step 5',
						),
					SCBB_STEP6 => array
						(
							'page' => 'Allowed to access Customers / Billing / Bill / Step 6',
						),
					SCE_ADDRESSES => array
						(
							'page' => 'Allowed to access Customers / Edit / Addresses',
						),
					SCE_ADJUSTMENTS => array
						(
							'page' => 'Allowed to access Customers / Edit / Adjustments',
						),
					SCE_BILLS => array
						(
							'page' => 'Allowed to access Customers / Edit / Bills',
						),
					SCE_BILLLOG => array
						(
							'page' => 'Allowed to access Customers / Edit / Bill Log',
						),
					SCE_COMPLAINTS => array
						(
							'page' => 'Allowed to access Customers / Edit / Complaints',
						),
					SCE_MAP => array
						(
							'page' => 'Allowed to access Customers / Edit / Map',
						),
					SCE_NOTES => array
						(
							'page' => 'Allowed to access Customers / Edit / Notes',
						),
					SCE_PAYMENTS => array
						(
							'page' => 'Allowed to access Customers / Edit / Payments',
						),
					SCE_SERVICE => array
						(
							'page' => 'Allowed to access Customers / Edit / Service',
						),
					SCE_SERVICETYPES => array
						(
							'page' => 'Allowed to access Customers / Edit / ServiceType',
						),
					SCE_SUMMARY => array
						(
							'page' => 'Allowed to access Customers / Edit / Summary',
							'bill-balance' => 'Balance after last time customer was billed.  This is used as the starting balance for the billing of this customer.',
							'bill-stopped' => 'Delivered status after last time customer was billed.  This is used as the starting delivery status the next time this customer is billed.',
							'bill-type' => 'Delivery type after last time customer was billed.  This is used as the starting delivery type the next time this customer is billed.',
						),
					SCE_BILLING => array
						(
							'page' => 'Allowed to access Customers / Edit / Billing',
						),
					SCP_ADD => array
						(
							'page' => 'Allowed to access Customers / Payments / Add',
						),
					SCP_LOOKUP => array
						(
							'page' => 'Allowed to access Customers / Payments / Search',
						),
					SCR_AHEAD => array
						(
							'page' => 'Allowed to access Customers / Reports / Ahead',
						),
					SCR_BEHIND => array
						(
							'page' => 'Allowed to access Customers / Reports / Behind',
						),
					SCR_ORDERS => array
						(
							'page' => 'Allowed to access Customers / Reports / Orders',
						),
					SCR_STOPPED => array
						(
							'page' => 'Allowed to access Customers / Reports / Stopped',
						),
					SCV_ADDRESSES => array
						(
							'page' => 'Allowed to access Customers / View / Addresses',
						),
					SCV_ADJUSTMENTS => array
						(
							'page' => 'Allowed to access Customers / View / Adjustments',
						),
					SCV_BILLS => array
						(
							'page' => 'Allowed to access Customers / View / Bills',
						),
					SCV_BILLLOG => array
						(
							'page' => 'Allowed to access Customers / View / Bill Log',
						),
					SCV_COMPLAINTS => array
						(
							'page' => 'Allowed to access Customers / View / Complaints',
						),
					SCV_MAP => array
						(
							'page' => 'Allowed to access Customers / View / Map',
						),
					SCV_NOTES => array
						(
							'page' => 'Allowed to access Customers / View / Notes',
						),
					SCV_PAYMENTS => array
						(
							'page' => 'Allowed to access Customers / View / Payments',
						),
					SCV_SERVICE => array
						(
							'page' => 'Allowed to access Customers / View / Service',
						),
					SCV_SERVICETYPES => array
						(
							'page' => 'Allowed to access Customers / View / ServiceType',
						),
					SCV_SUMMARY => array
						(
							'page' => 'Allowed to access Customers / View / Summary',
						),
					SCV_BILLING => array
						(
							'page' => 'Allowed to access Customers / View / Billing',
						),
					SCW_ADJUSTMENT => array
						(
							'page' => 'Allowed to access Customers / Popup / Adjustment',
						),
					SCW_CHANGETYPE => array
						(
							'page' => 'Allowed to access Customers / Popup / ChangeType',
						),
					SCW_COMPLAINT => array
						(
							'page' => 'Allowed to access Customers / Popup / Complaint',
						),
					SCW_STOPSTART => array
						(
							'page' => 'Allowed to access Customers / Popup / StopStart',
						),
					SCW_STARTSTOP => array
						(
							'page' => 'Allowed to access Customers / Popup / StartStop',
						),
					SM_HOME => array
						(
							'page' => 'Allowed to access Home',
						),
					SD_ADD => array
						(
							'page' => 'Allowed to access Adjustment / Add',
						),
					SD_EDIT => array
						(
							'page' => 'Allowed to access Adjustment / Edit',
						),
					SD_VIEW => array
						(
							'page' => 'Allowed to access Adjustment / View',
						),
					SL_ADD => array
						(
							'page' => 'Allowed to access Bill / Add',
						),
					SL_EDIT => array
						(
							'page' => 'Allowed to access Bill / Edit',
						),
					SL_VIEW => array
						(
							'page' => 'Allowed to access Bill / View',
						),
					SV_ADD => array
						(
							'page' => 'Allowed to access Service / Add',
						),
					SV_EDIT => array
						(
							'page' => 'Allowed to access Service / Edit',
						),
					SV_VIEW => array
						(
							'page' => 'Allowed to access Service / View',
						),
					SP_ADD => array
						(
							'page' => 'Allowed to access Payment / Add',
						),
					SP_EDIT => array
						(
							'page' => 'Allowed to access Payment / Edit',
						),
					SP_VIEW => array
						(
							'page' => 'Allowed to access Payment / View',
						),
					SW_ADD => array
						(
							'page' => 'Allowed to access ServiceType / Add',
						),
					SW_EDIT => array
						(
							'page' => 'Allowed to access ServiceType / Edit',
						),
					SW_VIEW => array
						(
							'page' => 'Allowed to access ServiceType / View',
						),
					ST_ADD => array
						(
							'page' => 'Allowed to access Security / Add',
						),
					ST_EDIT => array
						(
							'page' => 'Allowed to access Security / Edit',
						),
					SI_ADD => array
						(
							'page' => 'Allowed to access Configuration / Add',
						),
					SI_EDIT => array
						(
							'page' => 'Allowed to access Configuration / Edit',
						),
					SO_ADD => array
						(
							'page' => 'Allowed to access Period / Add',
						),
					SO_EDIT => array
						(
							'page' => 'Allowed to access Period / Edit',
						),
					SU_ADD => array
						(
							'page' => 'Allowed to access Route / Add',
						),
					SU_EDIT => array
						(
							'page' => 'Allowed to access Route / Edit',
						),
					SJ_ADD => array
						(
							'page' => 'Allowed to access Group / Add',
						),
					SJ_EDIT => array
						(
							'page' => 'Allowed to access Group / Edit',
						),
					SS_ADD => array
						(
							'page' => 'Allowed to access User / Add',
						),
					SS_EDIT => array
						(
							'page' => 'Allowed to access User / Edit',
						),
					SK_ADD => array
						(
							'page' => 'Allowed to access Profile / Add',
						),
					SK_EDIT => array
						(
							'page' => 'Allowed to access Profile / Edit',
						),
					SQ_ADD => array
						(
							'page' => 'Allowed to access Complaints / Add',
						),
					SQ_EDIT => array
						(
							'page' => 'Allowed to access Complaints / Edit',
						),
					SQ_VIEW => array
						(
							'page' => 'Allowed to access Complaints / View',
						),
					SE_REPORTS => array
						(
							'page' => 'Allowed to access Routes / Reports',
						),
					SE_SEQUENCING => array
						(
							'page' => 'Allowed to access Routes / Sequencing',
						),
					SER_CHANGES => array
						(
							'page' => 'Allowed to access Routes / Reports / Changes',
						),
					SER_DRAW => array
						(
							'page' => 'Allowed to access Routes / Reports / Draw',
						),
					SER_ROUTE => array
						(
							'page' => 'Allowed to access Routes / Reports / Route',
						),
					SER_STATUS => array
						(
							'page' => 'Allowed to access Routes / Reports / Status',
						),
					SER_TIPS => array
						(
							'page' => 'Allowed to access Routes / Reports / Tips',
						),
					SERC_HISTORY => array
						(
							'page' => 'Allowed to access Routes / Reports / Changes / History',
						),
					SERC_NOTES => array
						(
							'page' => 'Allowed to access Routes / Reports / Changes / Notes',
						),
					SERC_REPORT => array
						(
							'page' => 'Allowed to access Routes / Reports / Changes / Report',
						),
				);
		}
	
		protected function _pages(&$v)
		{
			$v = array
				(
					S_ADMIN => 'Admin',
					S_CUSTOMERS => 'Customers',
					S_HOME => 'Home',
					S_ADJUSTMENT => 'Adjustment',
					S_BILL => 'Bill',
					S_SERVICE => 'Service',
					S_PAYMENT => 'Payment',
					S_SERVICETYPE => 'ServiceType',
					S_SECURITY => 'Security',
					S_CONFIG => 'Configuration',
					S_PERIOD => 'Period',
					S_ROUTE => 'Route',
					S_GROUP => 'Group',
					S_USER => 'User',
					S_PROFILE => 'Profile',
					S_STORES => 'Stores',
					S_COMPLAINTS => 'Complaints',
					S_ROUTES => 'Routes',
					SA_AUDIT => 'Admin / Audit',
					SA_BILLING => 'Admin / Billing',
					SA_CONFIG => 'Admin / Configuration',
                    SA_CUSTOMERS => 'Admin / Customers',
					SA_FORMS => 'Admin / Forms',
					SA_GROUPS => 'Admin / Groups',
					SA_PERIODS => 'Admin / Periods',
                    SA_ROUTES => 'Admin / Routes',
					SA_SECURITY => 'Admin / Security',
					SA_USERS => 'Admin / Users',
                    SAC_BILLING => 'Admin / Customers / Billing',
                    SAC_RATES => 'Admin / Customers / Rates',
                    SAC_TYPES => 'Admin / Customers / Types',
                    SACR_ADD => 'Admin / Customers // Rates / Add',
                    SACR_EDIT => 'Admin / Customers / Rates / Edit',
                    SACR_VIEW => 'Admin / Customers / Rates / View',
                    SACT_ADD => 'Admin / Customers / Types / Add',
                    SACT_EDIT => 'Admin / Customers / Types / Edit',
                    SACT_VIEW => 'Admin / Customers / Types / View',
					SAG_ADD => 'Admin / Groups / Add',
					SAG_EDIT => 'Admin / Groups / Edit',
					SAP_ADD => 'Admin / Periods / Add',
					SAP_EDIT => 'Admin / Periods / Edit',
                    SAR_ADD => 'Admin / Routes / Add',
                    SAR_EDIT => 'Admin / Routes / Edit',
					SAU_ADD => 'Admin / Users / Add',
					SAU_EDIT => 'Admin / Users / Edit',
					SC_ADDNEW => 'Customers / New',
					SC_BILLING => 'Customers / Billing',
					SC_EDIT => 'Customers / Edit',
					SC_FLAGSTOPS => 'Customers / Flag Stops',
					SC_SEARCH => 'Customers / Search',
					SC_PAYMENTS => 'Customers / Payments',
					SC_REPORTS => 'Customers / Reports',
					SC_SEQUENCING => 'Customers / Sequencing',
					SC_VIEW => 'Customers / View',
					SC_POPUP => 'Customers / Popup',
                    SC_COMBINED => 'Customers / Combined',
                    SCC_ADD => 'Customers / Combined / Add',
                    SCC_EDIT => 'Customers / Combined / Edit',
					SCB_BILL => 'Customers / Billing / Bill',
					SCB_DOWNLOAD => 'Customers / Billing / Download',
					SCB_LOG => 'Customers / Billing / Log',
					SCB_REPAIR => 'Customers / Billing / Repair',
					SCBB_STEP1 => 'Customers / Billing / Bill / Step 1',
					SCBB_STEP2 => 'Customers / Billing / Bill / Step 2',
					SCBB_STEP3 => 'Customers / Billing / Bill / Step 3',
					SCBB_STEP4 => 'Customers / Billing / Bill / Step 4',
					SCBB_STEP5 => 'Customers / Billing / Bill / Step 5',
					SCBB_STEP6 => 'Customers / Billing / Bill / Step 6',
					SCE_ADDRESSES => 'Customers / Edit / Addresses',
					SCE_ADJUSTMENTS => 'Customers / Edit / Adjustments',
					SCE_BILLS => 'Customers / Edit / Bills',
					SCE_BILLLOG => 'Customers / Edit / Bill Log',
					SCE_COMPLAINTS => 'Customers / Edit / Complaints',
					SCE_MAP => 'Customers / Edit / Map',
					SCE_NOTES => 'Customers / Edit / Notes',
					SCE_PAYMENTS => 'Customers / Edit / Payments',
					SCE_SERVICE => 'Customers / Edit / Service',
					SCE_SERVICETYPES => 'Customers / Edit / ServiceType',
					SCE_SUMMARY => 'Customers / Edit / Summary',
					SCE_BILLING => 'Customers / Edit / Billing',
					SCP_ADD => 'Customers / Payments / Add',
					SCP_LOOKUP => 'Customers / Payments / Search',
					SCR_AHEAD => 'Customers / Reports / Ahead',
					SCR_BEHIND => 'Customers / Reports / Behind',
					SCR_ORDERS => 'Customers / Reports / Orders',
					SCR_STOPPED => 'Customers / Reports / Stopped',
					SCV_ADDRESSES => 'Customers / View / Addresses',
					SCV_ADJUSTMENTS => 'Customers / View / Adjustments',
					SCV_BILLS => 'Customers / View / Bills',
					SCV_BILLLOG => 'Customers / View / Bill Log',
					SCV_COMPLAINTS => 'Customers / View / Complaints',
					SCV_MAP => 'Customers / View / Map',
					SCV_NOTES => 'Customers / View / Notes',
					SCV_PAYMENTS => 'Customers / View / Payments',
					SCV_SERVICE => 'Customers / View / Service',
					SCV_SERVICETYPES => 'Customers / View / ServiceType',
					SCV_SUMMARY => 'Customers / View / Summary',
					SCV_BILLING => 'Customers / View / Billing',
					SCW_ADJUSTMENT => 'Customers / Popup / Adjustment',
					SCW_CHANGETYPE => 'Customers / Popup / ChangeType',
					SCW_COMPLAINT => 'Customers / Popup / Complaint',
					SCW_STOPSTART => 'Customers / Popup / StopStart',
					SCW_STARTSTOP => 'Customers / Popup / StartStop',
					SM_HOME => 'Home',
					SD_ADD => 'Adjustment / Add',
					SD_EDIT => 'Adjustment / Edit',
					SD_VIEW => 'Adjustment / View',
					SL_ADD => 'Bill / Add',
					SL_EDIT => 'Bill / Edit',
					SL_VIEW => 'Bill / View',
					SV_ADD => 'Service / Add',
					SV_EDIT => 'Service / Edit',
					SV_VIEW => 'Service / View',
					SP_ADD => 'Payment / Add',
					SP_EDIT => 'Payment / Edit',
					SP_VIEW => 'Payment / View',
					SW_ADD => 'ServiceType / Add',
					SW_EDIT => 'ServiceType / Edit',
					SW_VIEW => 'ServiceType / View',
					ST_ADD => 'Security / Add',
					ST_EDIT => 'Security / Edit',
					SI_ADD => 'Configuration / Add',
					SI_EDIT => 'Configuration / Edit',
					SO_ADD => 'Period / Add',
					SO_EDIT => 'Period / Edit',
					SU_ADD => 'Route / Add',
					SU_EDIT => 'Route / Edit',
					SJ_ADD => 'Group / Add',
					SJ_EDIT => 'Group / Edit',
					SS_ADD => 'User / Add',
					SS_EDIT => 'User / Edit',
					SK_ADD => 'Profile / Add',
					SK_EDIT => 'Profile / Edit',
					SQ_ADD => 'Complaints / Add',
					SQ_EDIT => 'Complaints / Edit',
					SQ_VIEW => 'Complaints / View',
					SE_REPORTS => 'Routes / Reports',
					SE_SEQUENCING => 'Routes / Sequencing',
					SER_CHANGES => 'Routes / Reports / Changes',
					SER_DRAW => 'Routes / Reports / Draw',
					SER_ROUTE => 'Routes / Reports / Route',
					SER_STATUS => 'Routes / Reports / Status',
					SER_TIPS => 'Routes / Reports / Tips',
					SERC_HISTORY => 'Routes / Reports / Changes / History',
					SERC_NOTES => 'Routes / Reports / Changes / Notes',
					SERC_REPORT => 'Routes / Reports / Changes / Report',
				);
		}
	
	}
?>
