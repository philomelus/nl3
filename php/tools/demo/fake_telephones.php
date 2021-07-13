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

	define('ROOT', '../');

	set_include_path(get_include_path() . PATH_SEPARATOR . ROOT);

	require_once 'inc/security.inc.php';
	require_once 'inc/common.inc.php';

	set_time_limit(0);

	$fields = array
		(
			'dTele1',
			'dTele2',
			'dTele3',
			'bTele1',
			'bTele2',
			'bTele3'
		);

	for ($id = 1; $id < 1414; ++$id)
	{
		$customer = lup_customer($id);
		reset($fields);
		foreach($fields as $field)
		{
			if (!empty($customer->$field))
			{
				$tele = $customer->$field;
				if (!preg_match('/^\([0-9]{3}\) [0-9]{3}-[0-9]{4}( ext [0-9]*)?$/', $tele))
				{
					if (preg_match('^[0-9]{3}-[0-9]{4}$', $tele, $parts))
					{
						$tele = '(503) ' . $tele;
					}
					else if (preg_match('/^([0-9]{3})-([0-9]{3})-([0-9]{4})$/', $tele, $parts))
					{
						$tele = '(' . $parts[1] . ') ' . $parts[2] . '-' . $parts[3];
					}
					else if ($tele == '-')
						$tele = '';
					else
{printf("%04d: %s = %s\n", $customer->id, $field, $tele);
$updatedNext = false;
}
				}

				preg_match('/^(\([0-9]{3}\)) [0-9]{3}-([0-9]{4})( ext [0-9]*)?$/', $tele, $parts);
				$query = "UPDATE `customers` SET `" . $field . "` = '" . $parts[1] . ' 555-' . $parts[2] . $parts[3] . "' WHERE `id` = " . $customer->id;
				if (!db_query($query))
					echo "Query failed: " . $query . " (" . $DB->error . ")\n";
			}
		}
	}

?>
