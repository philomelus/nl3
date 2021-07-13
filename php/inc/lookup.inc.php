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

//-----------------------------------------------------------------------------
/**
 *	Returns a customers address in the form of an object
 *	@param integer $id Customer ID
 *	@param integer $sequence Address type
 *	@return boolean|object Object with fields as members or false on failure
 */

	function lup_c_address($id, $sequence = ADDR_C_DELIVERY)
	{
		global $err, $errText;
		// TODO:  Use prepared statement and singleton here
		$result = db_query_object('SELECT * FROM `customers_addresses` WHERE `customer_id` = '
				. $id . ' AND `sequence` = ' . $sequence . ' LIMIT 1');
		if (!$result && $err >= ERR_SUCCESS)
		{
			$errText = 'Customer ' . gen_customerid($id) . ', sequence ' . $sequence
					. ', address record doesn\'t exist!';
		}
		return $result;
	}

//-----------------------------------------------------------------------------
/**
 *	Returns a customers address in the form of an object, or if an error occurrs
 *	generates an error page and aborts execution.
 *	@param string $page Page title for error
 *	@param integer $id Customer ID
 *	@param integer $sequence Address type
 *	@return boolean|object Object with customer address fields as members
 */

	function lup_c_address_valid($page, $id, $sequence = ADDR_C_DELIVERY)
	{
		global $errText;
		$address = lup_c_address($id, $sequence);
		if (!$address)
		{
			echo fatal_error($page, $errText);
			exit();
		}
		return $address;
	}

//-----------------------------------------------------------------------------

/**
 *	Returns a customers adjustment in the form of an object
 *	@param integer $id Adjustment ID
 *	@return boolean|object Object with fields as members or false on failure
 */

	function lup_c_adjustment($id)
	{
		global $err, $errText;
		// TODO:  Use prepared statement and singleton here
		$result = db_query_object('SELECT * FROM `customers_adjustments` WHERE `id` = '
				. $id . ' LIMIT 1');
		if (!$result && $err >= ERR_SUCCESS)
			$errText = 'Adjustment ' . gen_c_adjustmentid($id) . ' doesn\'t exist';
		return $result;
	}

//-----------------------------------------------------------------------------

/**
 *	Returns a customers bill in the form of an object
 *	@param integer $customer_id Customer ID
 *	@param integer $period_id Period ID
 *	@return boolean|object Object with fields as members or false on failure
 */

	function lup_c_bill($customer_id, $period_id)
	{
		global $err, $errText;

		// TODO:  Use prepared statement and singleton here
		$result = db_query_object('SELECT * FROM `customers_bills` WHERE `cid` = \''
				. sprintf('%06d', $customer_id) . '\' AND `iid` = ' . $period_id . ' LIMIT 1');
		if (!$result && $err >= ERR_SUCCESS)
		{
			$errText = 'Bill ' . gen_c_billid($customer_id, $period_id)
					. ' doesn\'t exist';
		}
		return $result;
	}

//-----------------------------------------------------------------------------

/**
 *	Returns a combined customers record in the form of an object
 *	@param integer $id Customer ID of combined customer
 *	@param integer $id2 [optional] Customer ID of combinee customer
 *	@return boolean|object Object with fields as members or false on failure
 */

	function lup_c_combined($id, $id2 = 0)
	{
		global $err, $errText;
		// TODO:  Use prepared statement and singleton here
		$query = 'SELECT * FROM `customers_combined_bills` WHERE `customer_id_main` = ' . intval($id);
		if (intval($id2) != 0)
			$query .= ' AND `customer_id_secondary` = ' . intval($id2);
		$result = db_query_object($query . ' LIMIT 1');
		if (!$result && $err >= ERR_SUCCESS)
		{
			if (intval($id2) != 0)
				$errText = 'Customers ' . gen_customerid($id) . ' and ' . gen_customerid($id2) . ' are not combined!';
			else
				$errText = 'Customer ' . gen_customerid($id) . ' is not combined!';
		}
		return $result;
	}

//-----------------------------------------------------------------------------

/**
 *	Returns a customers complaint record in the form of an object
 *	@param integer $id Customers complaint ID
 *	@return boolean|object Object with fields as members or false on failure
 */

	function lup_c_complaint($id)
	{
		global $err, $errText;
		// TODO:  Use prepared statement and singleton here
		$result = db_query_object('SELECT * FROM `customers_complaints` WHERE `id` = '
				. $id . ' LIMIT 1');
		if (!$result && $err >= ERR_SUCCESS)
			$errText = 'Service change ' . gen_c_complaintid($id) . ' doesn\'t exist';
		return $result;
	}

//-----------------------------------------------------------------------------
/**
 *	Returns a customers name in the form of an object
 *	@param integer $id Customer ID
 *	@param integer $sequence Address type
 *	@return boolean|object Object with fields as members or false on failure
 */

	function lup_c_name($id, $sequence = NAME_C_DELIVERY1)
	{
		global $err, $errText;
		// TODO:  Use prepared statement and singleton here
		$result = db_query_object('SELECT * FROM `customers_names` WHERE `customer_id` = ' . $id
				. ' AND `sequence` = ' . $sequence . ' LIMIT 1');
		if (!$result && $err >= ERR_SUCCESS)
		{
			$errText = 'Customer ' . gen_customerid($id) . ', sequence ' . $sequence
					. ', name doesn\'t exist!';
		}
		return $result;
	}

//-----------------------------------------------------------------------------
/**
 *	Returns a customers name in the form of an object, or if an error occurrs
 *	generates an error page and aborts execution.
 *	@param string $page Page title for error
 *	@param integer $id Customer ID
 *	@param integer $sequence Name type
 *	@return boolean|object Object with customer address fields as members
 */

	function lup_c_name_valid($page, $id, $sequence = ADDR_C_DELIVERY)
	{
		global $errText;
		$name = lup_c_name($id, $sequence);
		if (!$name)
		{
			echo fatal_error($page, $errText);
			exit();
		}
		return $name;
	}

//-----------------------------------------------------------------------------
/**
 *	Returns a customer payment record in the form of an object
 *	@param integer $id Customer payment ID
 *	@return boolean|object Object with fields as members or false on failure
 */

	function lup_c_payment($id)
	{
		global $err, $errText;
		// TODO:  Use prepared statement and singleton here
		$result = db_query_object('SELECT * FROM `customers_payments` WHERE `id` = '
				. $id . ' LIMIT 1');
		if (!$result && $err >= ERR_SUCCESS)
			$errText = 'Payment ' . gen_c_paymentid($id) . ' doesn\'t exist';
		return $result;
	}

//-----------------------------------------------------------------------------
/**
 *	Returns a customer rate record in the form of an object
 *	@param integer $type_id Customer delivery type ID
 *	@param integer $period_id_begin Period ID when rate takes effect
 *	@param integer $period_id_end Period ID when rate expires
 *	@return boolean|object Object with fields as members or false on failure
 */

	function lup_c_rate($type_id, $period_id_begin, $period_id_end)
	{
		global $err, $errText;
        $query = 'SELECT * FROM `customers_rates` WHERE `type_id` = '
				. $type_id . ' AND `period_id_begin` = ' . $period_id_begin
				. ' AND `period_id_end` ';
        if (is_null($period_id_end) || $period_id_end == 0)
            $query .= '<=> NULL';
        else
            $query .= '= ' . $period_id_end;
		$result = db_query_object($query);
		if (!$result && $err >= ERR_SUCCESS)
		{
			$errText = 'Rate ' . gen_c_typeid($type_id) . '-'
					. gen_peiodid($perid_id_begin) . '-'
					. gen_periodid($period_id_end) . ' doesn\'t exist';
		}
		return $result;
	}

//-----------------------------------------------------------------------------
/**
 *	Returns a customer service record in the form of an object
 *	@param integer $id Customer service id
 *	@return boolean|object Object with fields as members or false on failure
 */

	function lup_c_service($id)
	{
		global $err, $errText;
		// TODO:  Use prepared statement and singleton here
		$result = db_query_object('SELECT * FROM `customers_service` WHERE `id` = '
				. $id . ' LIMIT 1');
		if (!$result && $err >= ERR_SUCCESS)
			$errText = 'Customer service ' . gen_c_serviceid($id) . ' doesn\'t exist';
		return $result;
	}

//-----------------------------------------------------------------------------
/**
 *	Returns a customer service type record in the form of an object
 *	@param integer $id Customer service id
 *	@return boolean|object Object with fields as members or false on failure
 */

	function lup_c_service_type($id)
	{
		global $err, $errText;
		// TODO:  Use prepared statement and singleton here
		$result = db_query_object('SELECT * FROM `customers_service_types` WHERE `id` = '
				. $id . ' LIMIT 1');
		if (!$result && $err >= ERR_SUCCESS)
			$errText = 'Customer service type ' . gen_c_servicetypeid($id) . ' doesn\'t exist';
		return $result;
	}

//-----------------------------------------------------------------------------
/**
 *	Returns a customer telephone record in the form of an object
 *	@param integer $id Customer id
 *	@param integer $sequence Customer telephone type
 *	@return boolean|object Object with fields as members or false on failure
 */

	function lup_c_telephone($customer_id, $sequence = TEL_C_DELIVERY1)
	{
		global $err, $errText;
		// TODO:  Use prepared statement and singleton here
		$result = db_query_object('SELECT * FROM `customers_telephones` WHERE `customer_id` = '
				. $customer_id . ' AND `sequence` = ' . $sequence . ' LIMIT 1');
		if (!$result && $err >= ERR_SUCCESS)
		{
			$errText = 'Customer ' . gen_customerid($customer_id) . ', sequence '
					. $sequence . ' telephone doesn\'t exist';
		}
		return $result;
	}

//-----------------------------------------------------------------------------
/**
 *	Returns a customer service type record in the form of an object
 *	@param integer $id Customer service id
 *	@return boolean|object Object with fields as members or false on failure
 */

	function lup_c_type($id)
	{
		global $err, $errText;
		// TODO:  Use prepared statement and singleton here
		$result = db_query_object('SELECT * FROM `customers_types` WHERE `id` = '
				. $id . ' LIMIT 1');
		if (!$result && $err >= ERR_SUCCESS)
			$errText = 'Delivery type ' . gen_c_typeid($id) . ' doesn\'t exist!';
		return $result;
	}

//-----------------------------------------------------------------------------
/**
 *	Returns a customers record in the form of an object
 *	@param integer $id Customers ID
 *	@return boolean|object Object with fields as members or false on failure
 */

	function lup_customer($id)
	{
		global $err, $errText;
		// TODO:  Use prepared statement and singleton here
		$result = db_query_object('
SELECT
	`c`.*,
	`a`.address1 AS `address`,
	`a`.`city` AS `city`,
	`a`.`state` AS `state`,
	`a`.`zip` AS `zip`,
	`t`.`sequence` AS `telephone_sequence`,
	`t`.`number` AS `telephone`,
	`n`.`first` AS `firstName`,
	`n`.`last` AS `lastName`
FROM
	`customers` AS `c`,
	`customers_addresses` AS `a`,
	`customers_telephones` AS `t`,
	`customers_names` AS `n`
WHERE
	`c`.`id` = `a`.`customer_id`
	AND `c`.`id` = `t`.`customer_id`
	AND `c`.`id` = `n`.`customer_id`
	AND `a`.`sequence` = ' . ADDR_C_DELIVERY . '
	AND `t`.`sequence` = ' . TEL_C_DELIVERY1 . '
	AND `n`.`sequence` = ' . NAME_C_DELIVERY1 . '
	AND `c`.`id` = ' . $id . '
LIMIT
	1');
		if (!$result && $err >= ERR_SUCCESS)
			$errText = 'Customer ' . gen_customerid($id) . ' doesn\'t exist!';
		return $result;
	}

//-----------------------------------------------------------------------------
/**
 *	Returns a customers record in the form of an object, or if an error occurrs
 *	generates an error page and aborts execution.
 *	@param string $page Page title for error
 *	@param integer $id Customer ID
 *	@return object Object with customer fields as members
 */

	function lup_customer_valid($page, $id)
	{
		global $errText;
		$customer = lup_customer($id);
		if (!$customer)
		{
			echo fatal_error($page, $errText);
			exit();
		}
		return $customer;
	}

//-----------------------------------------------------------------------------
/**
 *	Returns a group record in the form of an object
 *	@param integer $id Group ID
 *	@return boolean|object Object with fields as members or false on failure
 */

	function lup_group($id)
	{
		global $err, $errText;
		// TODO:  Use prepared statement and singleton here
		$result = db_query_object('SELECT * FROM `groups` WHERE `id` = '
				. $id . ' LIMIT 1');
		if (!$result && $err >= ERR_SUCCESS)
			$errText = 'Group ' . gen_groupid($id) . ' doesn\'t exist!';
		return $result;
	}

//-----------------------------------------------------------------------------
/**
 *	Returns a period record in the form of an object
 *	@param integer $id Period ID
 *	@return boolean|object Object with fields as members or false on failure
 */

	function lup_period($id)
	{
		global $err, $errText;
		// TODO:  Use prepared statement and singleton here
		$result = db_query_object('SELECT * FROM `periods` WHERE `id` = '
				. $id . ' LIMIT 1');
		if (!$result && $err >= ERR_SUCCESS)
			$errText = 'Period ' . gen_periodid($id) . ' doesn\'t exist!';
		return $result;
	}

//-----------------------------------------------------------------------------
/**
 *	Returns a route record in the form of an object
 *	@param integer $id Route ID
 *	@return boolean|object Object with fields as members or false on failure
 */

	function lup_route($id)
	{
		global $err, $errText;
		// TODO:  Use prepared statement and singleton here
		$result = db_query_object('SELECT * FROM `routes` WHERE `id` = '
				. $id . ' LIMIT 1');
		if (!$result && $err >= ERR_SUCCESS)
			$errText = 'Route ' . gen_routeid($id) . ' doesn\'t exist!';
		return $result;
	}

//-----------------------------------------------------------------------------
/**
 *	Returns a route record in the form of an object
 *	@param integer $page Page to get securoty for
 *	@param integer $feature Feature to get security for
 *	@param integer $user_id [optional] User id to get access for; Defaults to current user
 *	@param integer $group_id [optional] Group id to get access for; Defaults to current group
 *	@return boolean|object Object with fields as members or false on failure
 */

	function lup_security($page, $feature, $user_id = -1, $group_id = -1)
	{
		global $err, $errText;
		// TODO:  Use prepared statement and singleton here
		if ($group_id == -1)
			$group_id = $_SESSION['gid'];
		if ($user_id == -1)
			$user_id = $_SESSION['uid'];
		$query = 'SELECT * FROM `security` WHERE `page` = ' . $page
				. ' AND `feature` = \'' . $feature . '\'';
		if (strlen($group_id) == 0 || is_null($group_id))
			$query .= ' AND ISNULL(`group_id`)';
		else
			$query .= ' AND `group_id` = ' . $group_id;
		if (strlen($user_id) == 0 || is_null($user_id))
			$query .= ' AND ISNULL(`user_id`)';
		else
			$query .= ' AND `user_id` = ' . $user_id;
		$result = db_query_object($query);
		if (!$result && $err >= ERR_SUCCESS)
		{
			$errText = 'Security record ' . $gid . ', ' . $uid . ', ' . $page . ', '
					. $feature . ' doesn\'t exist';
		}
		return $result;
	}

//-----------------------------------------------------------------------------
/**
 *	Returns a user record in the form of an object
 *	@param integer $id User id
 *	@return boolean|object Object with fields as members or false on failure
 */

	function lup_user($id)
	{
		global $err, $errText;
		// TODO:  Use prepared statement and singleton here
		$result = db_query_object('SELECT * FROM `users` WHERE `id` = '
				. $id . ' LIMIT 1');
		if (!$result && $err >= ERR_SUCCESS)
			$errText = 'User ' . gen_userid($id) . ' doesn\'t exist!';
		return $result;
	}

?>
