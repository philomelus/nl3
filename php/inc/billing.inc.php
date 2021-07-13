<?php
/*
	Copyright 2009 Russell E. Gibson

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

//-----------------------------------------------------------------------------
/**
 *	Returns an array of 5 containing a customers billing name and address
 *	@param integer $id Customer ID
 *	@return array|boolean Array of 5 strings, or false on failure.
 */

	function bill_address($id)
	{
		global $err;

		// Billing name
		$temp = lup_c_name($id, NAME_C_BILLING1);
		if ($err < ERR_SUCCESS)
		{
			if ($err != ERR_NOTFOUND)
				return false;
			$err = ERR_SUCCESS;
		}
		if (!$temp || empty($temp->first))
		{
			$temp = lup_c_name($id, NAME_C_DELIVERY1);
			if (!$temp)
				return false;
		}
		$name = strtoupper(valid_name($temp->first, $temp->last));

		// Billing address
		// Determine which address to use for billing
		$length = strlen($name);
		$temp = lup_c_address($id, ADDR_C_BILLING);
		if ($err < ERR_SUCCESS)
		{
			if ($err != ERR_NOTFOUND)
				return false;
			$err = ERR_SUCCESS;
		}
		if (!$temp || empty($temp->address1))
		{
			$temp = lup_c_address($id, ADDR_C_DELIVERY);
			if (!$temp)
				return false;
		}
		$address1 = $temp->address1;
		$city = $temp->city;
		$state = $temp->state;
		$zip = $temp->zip;

		// Validate first and possibly second lines
		$required = false;
		if (strlen($address1) > 22)
		{
			$required = true;

			// Locate whitespace before 23'rd char, and use from there to end as new line
			$i = 21;
			while ($address1{$i} != ' ' && $i > 0)
				--$i;

			// Make sure the first line at least has something
			if ($i <= 1)
				$i = 21;

			// Split the line into 2
			$address2 = substr($address1, $i + 1);
			$address1 = substr($address1, 0, $i + ($address1{$i} == ' ' ? 0 : 1));

			// Update zip padding length if needed
			if (strlen($address2) > $length)
				$length = strlen($address2);
		}
		if (strlen($address1) > $length)
			$length = strlen($address1);

		// Generate the final 2 lines
		if ($required)
		{
			$address3 = substr($city, 0, 19) . ' ' . $state;
			if (strlen($address3) > $length)
				$length = strlen($address3);
			if ($length > 22)		// BUGBUG: HARD CODED field width
				$length = 22;
			$address4 = str_repeat(' ', $length - 5) . substr($zip, 0, 5);
		}
		else
		{
			$address2 = substr($city, 0, 19) . ' ' . $state;
			if (strlen($address2) > $length)
				$length = strlen($address2);
			if ($length > 22)		// BUGBUG: HARD CODED field width
				$length = 22;
			$address3 = str_repeat(' ', $length - 5) . substr($zip, 0, 5);
			$address4 = '';
		}

		return array($name, strtoupper($address1), strtoupper($address2),
				strtoupper($address3), strtoupper($address4));
	}

//-----------------------------------------------------------------------------
/**
 *	Returns an array of 4 containing the current note for a customers bill
 *	@param integer $id Customer ID
 *	@param string Optional customers note if already known
 *	@return array|boolean Array of 4 strings, or false on failure.
 */

	function bill_note($id, $cache = NULL)
	{
		global $err;
		$note = array(0 => '', 1 => '', 2 => '', 3 => '');
		if (is_null($cache))
		{
			$temp = db_query_result('SELECT `billNote` FROM `customers` WHERE `id` = ' . $id);
			if (!$temp && $err < ERR_SUCCESS)
				return false;
		}
		else
			$temp = $cache;
		if (empty($temp))
			$temp = get_config('billing-note', '');
		if (!empty($temp))
		{
			$temp = wordwrap($temp, 36, "\r\n", true);
			$note = explode("\r\n", $temp);
			while (count($note) < 4)		// Make sure $note has 4 lines
				$note[] = '';
		}
		return $note;
	}

//-----------------------------------------------------------------------------
/**
 *	Returns a string description of a customer bill rate
 *	@param integer $id Customer ID
 *	@return string|boolean Description of rate of false on error
 */

	function bill_rate_title($id)
	{
		global $DeliveryTypes, $err;
		$fs = get_config('flag-stop-type');
		if ($fs != CFG_NONE)
		{
			if ($id == $fs)
				return 'Flag Stop Rate';
		}
		populate_types();
		if ($err < ERR_SUCCESS)
			return false;
		return sprintf("%s Rate", $DeliveryTypes[$id]['name']);
	}
?>
