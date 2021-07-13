<?
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

	set_include_path('..' . PATH_SEPARATOR . get_include_path());

	Header("content-type: text/plain");
	require_once 'inc/common.inc.php';
	require_once 'tools/bill_test_cases.php';

//	echo count($services) . "\n";
//	echo count($types) . "\n";

//print_r($services); exit();


	for ($outer = 61; $outer < 528; $outer += 36)
	{
		for ($n = 0; $n < 36; ++$n)
		{
			$id = $outer + $n;

			if ($n > 18)
				echo "UPDATE `customers` SET `billStopped` = 'Y' WHERE `id` = " . $id . " LIMIT 1;\n";
			else
				echo "UPDATE `customers` SET `billStopped` = 'N' WHERE `id` = " . $id . " LIMIT 1;\n";

			if (count($services[$n]) == 0)
			{
//				echo "/* SERVICES EMPTY */\n";
			}
			else
			{
				foreach($services[$n] as $data)
				{
					echo 'INSERT INTO `customers_service` SET `customer_id` = ' . $id
							. ', `period_id` = 0, `created` = NOW(), `type` = \''
							. $data[0] . '\', `when` = \'' . $data[1]
							. '\', `ignoreOnBill` = \'N\';' . "\n";
				}
			}

			$index = $n % 12;
			if (count($types[$index]) == 0)
			{
//				echo "/* TYPES EMPTY */\n";
			}
			else
			{
				foreach($types[$index] as $type)
				{
					echo 'INSERT INTO `customers_service_types` SET `customer_id` = ' . $id
							. ', `period_id` = 0, `created` = NOW(), `when` = \'' . $type[0]
							. '\', `ignoreOnBill` = \'N\', `type_id_from` = ' . $type[1]
							. ', `type_id_to` = ' . $type[2] . ";\n";
				}
			}
		}
	}

?>
