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

	set_include_path('..' . PATH_SEPARATOR . get_include_path());

	require_once 'newsledgerdb.inc.php';
	require_once 'inc/constants.inc.php';

	$DB = mysqli_connect(DB_SERVER, DB_USER, DB_PASSWORD, DB_DATABASE);
	if (!$DB)
		die(mysqli_connect_error() . ' (' . mysqli_connect_errno() . ')');

	// Get all the pages
	$records = $DB->query('SELECT * FROM `pages` ORDER BY `parent`, `id`');
	if (!$records)
		die($DB->error . ' (' . $DB->errno . ")\n");
	$PAGES = array();
	while ($record = $records->fetch_object())
	{
		if (empty($record->parent))
		{
			$PAGES[$record->id] = array
				(
					'constant' => 'S_' . $record->constant,
					'id' => $record->id,
					'title' => $record->title,
					'parent' => '',
					'code' => $record->code
				);
		}
		else
		{
			$parents = array();
			for ($i = 0; $i < strlen($record->parent); $i += 2)
				$parents[] = substr($record->parent, $i, 2);

			$constant = 'S';
			$parent = '';
			foreach($parents as $p)
			{
				$parent .= $p;
				if (empty($PAGES[$parent]))
				{
					echo '$recrod='; var_export($record); echo "\n";
					die('Parent ' . $parent . ' of ' . $record->id . " doesn't exist!\n");
				}
				if (empty($PAGES[$parent]['code']))
				{
					echo '$recrod='; var_export($record); echo "\n";
					die('Parent ' . $PAGES[$parent]['constant'] . " doesn't have a code!\n");
				}
				$constant .= $PAGES[$parent]['code'];
			}

			$PAGES[$parent . $record->id] = array
				(
					'constant' => $constant . '_' . $record->constant,
					'id' => $record->id,
					'title' => $record->title,
					'parent' => $record->parent,
					'code' => $record->code
				);
		}
	}

	echo "<?php\n"
			. "/*\n"
			. "\tCopyright 2005, 2006, 2007, 2008 Russell E. Gibson\n\n"
			. "\tThis file is part of NewsLedger.\n\n"
			. "\tNewsLedger is free software: you can redistribute it and/or modify\n"
			. "\tit under the terms of the GNU General Public License as published by\n"
			. "\tthe Free Software Foundation, either version 3 of the License, or\n"
			. "\t(at your option) any later version.\n\n"
			. "\tNewsLedger is distributed in the hope that it will be useful,\n"
			. "\tbut WITHOUT ANY WARRANTY; without even the implied warranty of\n"
			. "\tMERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the\n"
			. "\tGNU General Public License for more details.\n\n"
			. "\tYou should have received a copy of the GNU General Public License\n"
			. "\talong with NewsLedger; see the file LICENSE.  If not, see\n"
			. "\t<http://www.gnu.org/licenses/>.\n"
			. "*/\n\n"
			. "\trequire_once 'smarty/smarty.inc.php';\n"
			. "\trequire_once 'inc/db.inc.php';\n\n";
	reset($PAGES);
	foreach($PAGES as $page => $data)
	{
		echo "\tdefine('" . $data['constant'] . '\', \'' . $page . "');\n";
	}
	echo "\n?>\n";
?>
