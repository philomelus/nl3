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

	define('P_BILL', 'bill');					// Bill date of period
	define('P_DAYS', 'days');					// Number of days in period
	define('P_DEND', 'dend');					// Display End
	define('P_DSTART', 'dstart');				// Display Start
	define('P_DUE', 'due');						// Due date for period
	define('P_END', 'end');						// Date of end of period + 23:59:59
	define('P_PERIOD', 'period');				// Period ID
	define('P_START', 'start');					// Date of start of period @ 0:00:00
	define('P_TITLE', 'title');					// Title for period
	
	define('PN_BILL', 'nextBill');				// Bill date of next period
	define('PN_DAYS', 'nextDays');				// Number of days in next period
	define('PN_DEND', 'nextDend');				// Display End for next period
	define('PN_DSTART', 'nextDstart');			// Display Start for next period
	define('PN_DUE', 'nextDue');				// Due date for next period
	define('PN_END', 'nextEnd');				// Date of end of next period + 23:59:59
	define('PN_PERIOD', 'nextPeriod');			// Period ID of next period
	define('PN_START', 'nextStart');			// Date of start of next period @ 0:00:00
	define('PN_TITLE', 'nextTitle');			// Title for next period
	
	define('PP_BILL', 'prevBill');				// Bill date in previous period
	define('PP_DAYS', 'prevDays');				// Number of days in previous period
	define('PP_DEND', 'prevDend');				// Display End for previous period
	define('PP_DSTART', 'prevDstart');			// Display Start for previous period
	define('PP_DUE', 'prevDue');				// Due date for previous period
	define('PP_END', 'prevEnd');				// Date of end of previous period + 23:59:59
	define('PP_PERIOD', 'prevPeriod');			// Period ID of previous period
	define('PP_START', 'prevStart');			// Date of start of previous period @ 0:00:00
	define('PP_TITLE', 'prevTitle');			// Title for previous period
	
	// Global set to current period
	$Period = gen_periodArray();

	//-------------------------------------------------------------------------
	// Returns an array of the above values for the specified period.  Always
	// consults the database for the information.

	// BUGBUG:  This is called more than once on startup...  Shouldn't need too...
	
	function gen_periodArray($period = 0)
	{
		global $err, $errCode, $errContext, $errQuery, $errText;
		
		$errObj = new error_stack;

		$result = array();

		// Determine correct billing period
		if ($period == 0)
		{
			$result[P_PERIOD] = get_globalConfig('billing-period', 0);
			if ($err < ERR_SUCCESS)
				return array();
		}
		else
			$result[P_PERIOD] = $period;

		// If we still don't have a period, give up
		if ($result[P_PERIOD] == 0)
		{
			$err = ERR_NOTFOUND;
			$errCode = ERR_NOTFOUND;
			$errContext = 'Generating period array';
			$errQuery = '';
			$errText = 'No active billing period defined';
			return array();
		}
		
		// Get the last period id
		$lastPeriod = db_query_result('SELECT MAX(`id`) FROM `periods`');
		if (!$lastPeriod)
			$lastPeriod = -1;
		
		// Query database
		$query = "SELECT * FROM `periods` WHERE `id` BETWEEN ";
		if ($result[P_PERIOD] == 1)
			$query .= '1';
		else
			$query .= ($result[P_PERIOD] - 1);
		$query .= ' AND ' . ($result[P_PERIOD] + 1) . " ORDER BY `changes_start` ASC";
		$records = db_query($query);
		if (!$records)
			return array();
		$count = $records->num_rows;
		if ($count != 3
				&& !($count == 2 && ($result[P_PERIOD] == 1 || $result[P_PERIOD] == $lastPeriod)))
		{
			$err = ERR_NOTFOUND;
			$errCode = ERR_NOTFOUND;
			$errContext = 'Locating periods';
			$errQuery = $query;
			$errText = 'Expected 3 rows, got ' . $records->num_rows;
			return array();
		}
		
		// First record is previous period
		if ($count == 2 && $result[P_PERIOD] == 1)
		{
			$result[PP_PERIOD] = 0;
			$result[PP_TITLE] = '';
			$result[PP_START] = 0;
			$result[PP_END] = 0;
			$result[PP_DSTART] = 0;
			$result[PP_DEND] = 0;
			$result[PP_DUE] = 0;
			$result[PP_BILL] = 0;
			$result[PP_DAYS] = 0;
		}
		else
		{
			$record = $records->fetch_object();
			$result[PP_PERIOD] = $result[P_PERIOD] - 1;
			$result[PP_TITLE] = $record->title;
			$result[PP_START] = strtotime($record->changes_start);
			$result[PP_END] = strtotime($record->changes_end . ' 23:59:59');
			$result[PP_DSTART] = strtotime($record->display_start);
			$result[PP_DEND] = strtotime($record->display_end . ' 23:59:59');
			$result[PP_DUE] = strtotime($record->due);
			$result[PP_BILL] = strtotime($record->bill);
			$result[PP_DAYS] = days_between_dates($result[PP_START], $result[PP_END]);
		}
		
		// Second record is current period
		$record = $records->fetch_object();
		$result[P_TITLE] = $record->title;
		$result[P_START] = strtotime($record->changes_start);
		$result[P_END] = strtotime($record->changes_end . ' 23:59:59');
		$result[P_DSTART] = strtotime($record->display_start);
		$result[P_DEND] = strtotime($record->display_end . ' 23:59:59');
		$result[P_DUE] = strtotime($record->due);
		$result[P_BILL] = strtotime($record->bill);
		$result[P_DAYS] = days_between_dates($result[P_START], $result[P_END]);
		
		// Last record is next period
		if ($count == 2 && $result[P_PERIOD] == $lastPeriod)
		{
			$result[PN_PERIOD] = 0;
			$result[PN_TITLE] = '';
			$result[PN_START] = 0;
			$result[PN_END] = 0;
			$result[PN_DSTART] = 0;
			$result[PN_DEND] = 0;
			$result[PN_DUE] = 0;
			$result[PN_BILL] = 0;
			$result[PN_DAYS] = 0;
		}
		else
		{
			$record = $records->fetch_object();
			$result[PN_PERIOD] = $result[P_PERIOD] + 1;
			$result[PN_TITLE] = $record->title;
			$result[PN_START] = strtotime($record->changes_start);
			$result[PN_END] = strtotime($record->changes_end . ' 23:59:59');
			$result[PN_DSTART] = strtotime($record->display_start);
			$result[PN_DEND] = strtotime($record->display_end . ' 23:59:59');
			$result[PN_DUE] = strtotime($record->due);
			$result[PN_BILL] = strtotime($record->bill);
			$result[PN_DAYS] = days_between_dates($result[PN_START], $result[PN_END]);
		}

		return $result;
	}

?>
