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

	require_once 'inc/database.inc.php';
	require_once 'inc/audit.inc.php';
    require_once 'inc/config.inc.php';
    require_once 'inc/common.inc.php';

//-------------------------------------------------------------------------
/*	Determine whether current user is allowed access
 *	@param string $what Name of feature to check access for
 *	@param string $where Security id of page to check; Defaults to PAGE constant
 */
	
	function allowed($what, $where = NULL)
	{
		global $err;
		
		// Make sure required session variables are available
		if (!isset($_SESSION['uid']) || empty($_SESSION['uid'])
				|| !isset($_SESSION['gid']) || empty($_SESSION['gid'])
				|| !defined('PAGE'))
		{
			return false;
		}

		// Make sure we have a page
		if (empty($where))
			$where = PAGE;
		$result = 	db_query('
SELECT
(
  SELECT `allowed` FROM `security`
  WHERE
	`user_id` = ' . $_SESSION['uid'] . '
	AND (ISNULL(`group_id`) OR `group_id` = ' . $_SESSION['gid'] . ')
	AND `page` = ' . $where . '
	AND `feature` = \'' . $what . '\'
  LIMIT 1
) AS `user`,
(
  SELECT `allowed` FROM `security`
  WHERE
	`group_id` = ' . $_SESSION['gid'] . '
	AND ISNULL(`user_id`)
	AND `page` = ' . $where . '
	AND `feature` = \'' . $what . '\'
  LIMIT 1
) AS `group`,
(
  SELECT `allowed` FROM `security`
  WHERE
	ISNULL(`group_id`)
	AND ISNULL(`user_id`)
	AND `page` = ' . $where . '
	AND `feature` = \'' . $what . '\'
  LIMIT 1
) AS `all`');
		if (!$result && $err < ERR_SUCCESS)
			return false;
		$data = $result->fetch_object();
		$result->close();
		if (is_null($data->user))
		{
			if (is_null($data->group))
			{
				if (is_null($data->all))
					return true;
				else
					return ($data->all == 'Y');
			}
			else
				return ($data->group == 'Y');
		}
		else
			return ($data->user == 'Y');
	}
	
	$title = get_globalConfig('default-title', 'NewsLedger');
	
    // 1 year (365.25 * 24 * 60 * 60)
	session_set_cookie_params(31536000, '/', '.rnstech.com');
	session_name('newsledger' . $title);
	session_start();
	
	// Perform new login if required
	if (isset($_POST['action']) && $_POST['action'] == 'Login')
	{
		// Validate username and password
		$result = db_query('SELECT `password`, `id`, `group_id`, `name` FROM `users`'
				. ' WHERE `login` = \'' . db_escape($_POST['username']) . '\'');
		if ($result)
		{
			$info = $result->fetch_object();
			$result->close();
			if (!empty($info->password))
			{
				if ($info->password == md5($_POST['password']))
				{
					$_SESSION['user'] = stripslashes($_POST['username']);
					$_SESSION['password'] = $info->password;
					$_SESSION['name'] = $info->name;
					$_SESSION['uid'] = $info->id;
					$_SESSION['gid'] = $info->group_id;
					audit(db_escape($_SESSION['name']) . ' logged in.');
				}
				else
				{
					$msg = 'Unknown or invalid credentials';
					audit($_POST['username'] . ' failed login, incorrect password.');
				}
			}
			else
			{
				$msg = 'Unknown or invalid credentials';
				audit('Login failed, unkown username: \'' . $_POST['username'] . '\'.');
			}
		}
	}
	
	// Validate page if user is logged in
	if (isset($_SESSION['user']) && !empty($_SESSION['user'])
			&& isset($_SESSION['password']) && !empty($_SESSION['password'])
			&& defined('PAGE'))
	{
        // User is logged in, and the page is set.  Redirect if
        // maintenance is being performed.
        $maint = get_globalConfig('maintenance-active', 'false');
        if ($maint == "true")
        {
            // TODO:  This is hard coded b/c this software is now specific
            //        to my father.
            if ($_SESSION['user'] != 'russg')
            {
                // Force them to maintenance message page
                header('Location: index.maint.php');
                exit();
            } 
        }

        // User is logged in, and the page is set, and maintenance isn't being
        // performed.  It's up to the page to redirect if user isn't allowed.
        global $username;
        $username = $_SESSION['name'];
		return;
	}

	// Display login page
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">
    <head>
        <title><?php echo $title; ?></title>
    </head>
	<body>
		<div><?php echo $title; ?></div>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<?php
	// Add in message if needed
	if (!empty($msg))
	{
?>
			<p><?php echo $msg ?></p>
<?php
	}
	
	// Add in error if needed
	if ($err < ERR_SUCCESS)
		echo gen_error();

?>
			<br />
			<table>
				<tr> 
					<td>Username</td>
					<td>
<?php
	if (!isset($_REQUEST['username']))
		$_REQUEST['username'] = '';
?>
						<input type="text" name="username" size="20" value="<?php echo $_REQUEST['username']; ?>" maxlength="50" />
					</td>
				</tr>
				<tr>
					<td>Password</td>
					<td>
<?php
	if (!isset($_REQUEST['password']))
		$_REQUEST['password'] = '';
?>
						<input type="password" name="password" size="20" value="<?php echo $_REQUEST['password']; ?>" maxlength="20" />
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" name="action" value="Login" />
					</td>
				</tr>
			</table>
		</form>
<?php
    echo gen_htmlFooter();

	// Prevent parent from executing
	exit();
?>
