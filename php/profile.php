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

	require_once 'inc/security.inc.php';

	define('PAGE', S_PROFILE);

	require_once 'inc/login.inc.php';
	require_once 'inc/common.inc.php';
	require_once 'inc/menu.inc.php';
	require_once 'inc/form.inc.php';
	require_once 'inc/profile.inc.php';
	require_once 'inc/profiledata.inc.php';
	require_once 'inc/securitydata.inc.php';
	require_once 'inc/popups/profile.inc.php';

	//-------------------------------------------------------------------------

	function display()
	{
		global $smarty;
		global $err, $errCode, $errContext, $errQuery, $errText;
		global $Routes;
		global $DeliveryTypes;
		global $message;

		populate_routes();
		populate_types();

		$data = ProfileData::create();

        // Make sure keys are set first time through
		if (!isset($_REQUEST['key1']))
			$_REQUEST['key1'] = 'All';

		if (!isset($_REQUEST['key2']))
			$_REQUEST['key2'] = 'All';

		if (!isset($_REQUEST['key3']))
			$_REQUEST['key3'] = 'All';

        // Make sure record offset and limit are set first time through
        if (!isset($_REQUEST['limit']))
            $_REQUEST['limit'] = 15;
        if (!isset($_REQUEST['offset']))
            $_REQUEST['offset'] = 0;

        $smarty->assign('action', $_SERVER['PHP_SELF']);

		// Build query for config items if needed
		$query = 'SELECT * FROM `users_configuration` WHERE `user_id` = ' . $_SESSION['uid'];
		$temp = '';
		if (isset($_REQUEST['key1']) && $_REQUEST['key1'] != 'All')
		{
			$temp .= $_REQUEST['key1'];
			if (isset($_REQUEST['key2']) && $_REQUEST['key2'] != 'All')
			{
				$temp .= '-' . $_REQUEST['key2'];
				if (isset($_REQUEST['key3']) && $_REQUEST['key3'] != 'All')
					$temp .= '-' . $_REQUEST['key3'];
			}
		}
		if (!empty($temp))
			$query .= ' AND `key` LIKE \'' . db_escape($temp) . '%\'';
		$query .= ' ORDER BY `key` LIMIT '
            . intval($_REQUEST['offset'])
            . ','
            . intval($_REQUEST['limit']);
		$set = db_query($query);
		if ($err < ERR_SUCCESS)
            $message = $errText;

        // Pass on the count of result rows
        $count = $set->num_rows;
        $smarty->assign('count', $count);

        // Pass on the active keys
		if (isset($_REQUEST['key1']))
		{
			$keys = array
				(
					2 => '',
					3 => ''
				);
			if ($_REQUEST['key1'] == "All")
			{
				$_REQUEST['key2'] = 'All';
				$keys[2] = ' disabled="disabled"';
				$_REQUEST['key3'] = 'All';
				$keys[3] = ' disabled="disabled"';
			}
			else
			{
				if ($_REQUEST['key2'] == "All")
				{
					$_REQUEST['key3'] = 'All';
					$keys[3] = ' disabled="disabled"';
				}
			}
		}
		else
		{
			$keys = array
				(
					2 => ' disabled="disabled"',
					3 => ' disabled="disabled"'
				);
		}
        $smarty->assign('keys', $keys);

        // Pass on any messages
        if (!isset($message))
            $message = Null;
        $smarty->assign('message', $message);

        // Pass on keys
        $smarty->assign('key1', $_REQUEST['key1']);
        $smarty->assign('key2', $_REQUEST['key2']);
        $smarty->assign('key3', $_REQUEST['key3']);

        // Pass on key1 options
        $temp = array();
        $temp[] = 'All';
        foreach($data->keys1(false) as $val)
            $temp[] = $val;
        $smarty->assign('keys1', $temp);

        // Pass on key2 options
        $temp = array();
        $temp[] = 'All';
        if ($_REQUEST['key1'] == 'All')
        {
            foreach($data->keys2(false) as $val)
                $temp[] = $val;
        }
        else
        {
            foreach($data->keys2FromKeys1(false, $_REQUEST['key1']) as $val)
                $temp[] = $val;
        }
        $smarty->assign('keys2', $temp);

        // Pass on key3 options
        $temp = array();
        $temp[] = 'All';
		if ($_REQUEST['key2'] == 'All')
        {
            foreach($data->keys3(false) as $val)
                $temp[] = $val;
        }
        else
		{
			foreach($data->keys3FromKeys2(false, $_REQUEST['key2']) as $val)
                $temp[] = $val;
		}
        $smarty->assign('keys3', $temp);

		$smarty->assign('profileAddURL', ProfileAddUrl(''));

        // Pass on list of profile items
        $temp = array();
		if ($count > 0)
		{
			while ($row = $set->fetch_object())
			{
                $record = array();

				$profile = $data->lookup($row->key);

                // Editable
				if (isset($profile[ProfileData::IS_READONLY]) && $profile[ProfileData::IS_READONLY])
				{
                    $record['edit'] = false;
                    $record['editLink'] = '';
				}
				else
				{
                    $record['edit'] = true;
                    $record['editLink'] = ProfileEditUrl($row->key, '');
				}

                // Deletable
				if ((isset($profile[ProfileData::IS_REQUIRED]) && $profile[ProfileData::IS_REQUIRED])
						|| (isset($profile[ProfileData::IS_READONLY]) && $profile[ProfileData::IS_READONLY]))
                {
                    $record['delete'] = false;
                    $record['deleteLink'] = '';
				}
				else
				{
                    $record['delete'] = true;
                    $record['deleteLink'] = $_SERVER['PHP_SELF'] . '?menu=' . IDM_PROFILE . '&amp;action=Delete&amp;key='
                                          . htmlspecialchars($row->key);
				}

                // Key
                $record['key'] = $row->key;

                // Description
				if (isset($profile[ProfileData::DESC]))
					$record['desc'] = valid_text($profile[ProfileData::DESC]);
				else
					$record['data'] = '&nbsp;';

                // Data value
				if (isset($profile[ProfileData::TYPE]))
				{
					switch ($profile[ProfileData::TYPE])
					{
					case CFG_BOOLEAN:
						switch ($row->value)
						{
                        case 'true':
                            $record['value'] = 'True';
							break;

						case 'false':
                            $record['value'] = 'False';
							break;

						default:
							$record['value'] = 'ERROR (BOOLEAN)';
							break;
						}
						break;

					case CFG_STRING:
						if (empty($row->value))
						{
							if (isset($profile[ProfileData::IS_REQUIRED]) && $profile[ProfileData::IS_REQUIRED])
								$record['value'] = 'ERROR (STRING)';
							else
								$record['value'] = 'blank';
						}
						else
							$record['value'] = wordwrap(valid_text($row->value), 27, '<br />', true);
						break;

					case CFG_PERIOD:
						$record['value'] = valid_text(iid2title(intval($row->value)));
						break;

					case CFG_ROUTE:
						$record['value'] = valid_text($Routes[intval($row->value)]);
						break;

					case CFG_TYPE:
						$record['value'] = valid_text($DeliveryTypes[intval($row->value)]['abbr']);
						break;

					case CFG_COLOR:
						$record['value'] = intval($row->val) . ' (#' . sprintf('%06X', intval($row->value)) . ')';
						break;

					case CFG_INTEGER:
						$record['value'] = intval($row->value);
						break;

					case CFG_FLOAT:
						$record['value'] = floatval($row->value);
						break;

					case CFG_MONEY:
						$record['value'] = sprintf('$%01.4f', $row->value);
						break;

					case CFG_ENUM:
						$record['value'] = $profile[ProfileData::ENUM][$row->value];
						break;

					case CFG_TELEPHONE:
						$record['value'] = $row->value;
						break;

					default:
						$record['value'] = 'ERROR (TYPE)';
						break;
					}
				}
				else
                    $record['value'] = 'ERROR (UNKNOWN)';

                // Add result to set
                $temp[] = $record;
            }
        }
        $smarty->assign('data', $temp);

        $smarty->assign('limit', $_REQUEST['limit']);
        $smarty->assign('offset', $_REQUEST['offset']);

        $smarty->display('profile.tpl');
	}

	//-------------------------------------------------------------------------

	function submit()
	{
		global $err, $errCode, $errContext, $errQuery, $errText;
		global $message;

		$action = $_REQUEST['action'];
		if ($action == '<')
		{
			$count = intval($_REQUEST['limit']);
			$offset = intval($_REQUEST['offset']);
			$offset -= $count;
			if ($offset < 0)
				$offset = 0;
			$_REQUEST['offset'] = $offset;
		}
		else if ($action == '>')
			$_REQUEST['offset'] += $_REQUEST['limit'];
		else if ($action == 'Reset')
		{
			$_REQUEST['key1'] = 'All';
			$_REQUEST['key2'] = 'All';
			$_REQUEST['key3'] = 'All';
			$_REQUEST['limit'] = 15;
			$_REQUEST['offset'] = 0;
		}
		else if ($action == 'Delete')
		{
			if (!isset($_GET['key']))
				return '';
			$key = $_GET['key'];

			// Delete the specified entry
			$query = 'DELETE FROM `users_configuration` WHERE `key` = \'' . $key
					. '\' AND `user_id` = ' . $_SESSION['uid'] . ' LIMIT 1';
			$result = db_query($query);
			if (!$result)
			{
				$message = '<div>Failed to delete ' . $title . '</div>';
			}
			else
			{
				$message = '<div>' . $key
						. ' deleted successfully</div>';
			}
		}

		return '';
	}

	$MENUS->display_page(IDM_PROFILE);
?>
