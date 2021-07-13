<?php
/*
  Copyright 2005, 2006, 2007, 2008, 2009, 2010 Russell E. Gibson

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

require_once 'inc/security.inc.php';	
require_once 'inc/common.inc.php';

$period_id = get_config('billing-period');
if (isset($_GET['sort']))
    $sort = intval($_GET['sort']);
else
    $sort = 0;
$in = '';

// Generate list for id's if provided
if (isset($_GET['ids']))
{
    $list = explode(',', $_GET['ids']);
    $ids = array();
    for ($i = 0; $i < count($list); ++$i)
    {
        $right = 0;
        sscanf($list[$i], '%d-%d', $left, $right);
        if ($right)
        {
            for ($n = $left; $n <= $right; ++$n)
                $ids[] = $n;
        }
        else
            $ids[] = intval($left);
    }
    $in = ' AND `cid` IN (';
    $c = '';
    foreach($ids as $id)
    {
        $in .= $c . $id;
        $c = ',';
    }
    $in .= ')';
}

// Generate correct query based on they way it's sorted
switch ($sort)
{
default:
case 0:
    $query = 'SELECT * FROM `customers_bills` WHERE `iid` = '
	. $period_id . ' AND `export` = \'Y\'' . $in;
    break;

case 1:
    $query = 'SELECT * FROM `customers_bills` '
	. 'INNER JOIN `routes_sequence` ON `customers_bills`.`cid` = `routes_sequence`.`tag_id` '
	. 'WHERE `iid` = '
	. $period_id . ' AND `export` = \'Y\'' . $in
	. ' ORDER BY `routes_sequence`.`order`';
    break;

case 2:
    # NOTE: I tried hard to get this into join syntax...  I couldn't figure it out
    $query = 'SELECT `customers_bills`.*, '
	. '(IF ((SELECT `zip` FROM `customers_addresses` WHERE `sequence` = 101 AND `customers_bills`.`cid` = `customers_addresses`.`customer_id`), '
	. '(SELECT `zip` FROM `customers_addresses` WHERE `sequence` = 101 AND `customers_bills`.`cid` = `customers_addresses`.`customer_id`), '
	. '(SELECT `zip` FROM `customers_addresses` WHERE `sequence` = 1 AND `customers_bills`.`cid` = `customers_addresses`.`customer_id`))) '
	. 'AS `zip` '
	. 'FROM `customers_bills`, `customers_addresses` '
	. 'WHERE `customers_bills`.`cid` = `customers_addresses`.`customer_id` AND `export` = \'Y\' AND `iid` = ' . $period_id
	. ' ORDER BY `zip`';
    break;
}

// Get the records
$records = db_query($query);
if ($records)
{
    $out = '';

    // Put names of columns in first row
    $fields = $records->fetch_fields();
    $field_count = $records->field_count;
    if ($fields[$field_count - 1]->name == 'zip')
        $field_count--;
    for ($i = 0; $i < $field_count; ++$i)
        $out .= '"' . $fields[$i]->name . '",';
    $out .= "\r\n";

    // Add all values in the table to $out.
    while ($l = $records->fetch_array())
    {
        for ($i = 0; $i < $field_count; ++$i)
            $out .= '"' . $l[$i] . '",';
        $out .= "\r\n";
    }

    $fn = 'Bills - ' . $Period[P_TITLE] . '.csv';
    header('Content-type: text/x-csv');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Content-Disposition: attachment; filename="' . $fn . '"');
    header('Content-Length: ' . strlen($out));
    echo $out;
}

?>
