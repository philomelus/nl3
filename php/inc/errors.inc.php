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
//=============================================================================
// Common error codes used through-out

	define('ERR_SUCCESS', 0);				// Successful completion
	define('ERR_UNDEFINED', -1);			// Nothing has been done yet, status unknown
	define('ERR_FAILURE', -2);				// General failure
	define('ERR_NOTFOUND', -3);				// Something wasn't found
	define('ERR_INVALID', -4);				// Something was invalid

//=============================================================================
// Error variables, initialized here to mean nothing has happened yet

	$err = ERR_UNDEFINED;	// Our internal error code
	$errCode = 0;			// Actual error code from official error source
	$errText = '';			// Actual error message text, from official error source
	$errContext = '';		// If set, its an identifiying location like "While locating customer:"
	$errQuery = '';			// If set, its the query that was in use when failure occured

//=============================================================================
// This object simply makes sure push_error and pop_error are called at the
// beginning of ending life of the object.

	// TODO:  Make this a static member of class error_stack
	$errStack = array();

	class error_stack
	{
		function __construct()
		{
			global $errStack;
			global $err, $errCode, $errContext, $errQuery, $errText;

			$errors = array
			(
				'err' => $err,
				'errCode' => $errCode,
				'errText' => $errText,
				'errContext' => $errContext,
				'errQuery' => $errQuery
			);

			array_push($errStack, $errors);

			$err = ERR_UNDEFINED;
			$errCode = 0;
			$errQuery = '';
			$errText = '';
		}
		function __destruct()
		{
			global $errStack;
			global $err, $errCode, $errContext, $errQuery, $errText;

			$errors = array_pop($errStack);
			// If previous was in error, but not undefined since something has been done,
			// overwrite the new error status with the previos one so it propigates
			if (($errors['err'] < ERR_SUCCESS && $errors['err'] != ERR_UNDEFINED)
					|| ($err == ERR_UNDEFINED && $errors['err'] >= ERR_SUCCESS))
			{
				$err = $errors['err'];
				$errCode = $errors['errCode'];
				$errText = $errors['errText'];
				$errContext = $errors['errContext'];
				$errQuery = $errors['errQuery'];
			}
		}
	}

//=============================================================================

	// DEPRECATE!
	function gen_errorHeader()
	{
		return '<h1>gen_errorHeader() deprecated</h1>';
	}

//=============================================================================

	// DEPRECATE
	function gen_errorFooter()
	{
		return '<h1>gen_errorFooter() deprecated</h1>';
	}

//=============================================================================
// Emits a an appropriate error
// based on $errText, $errCode, and $message.  Returns true if failed, so
// callers can simply return from their module.

    function gen_errorNew()
    {
        global $err, $errCode, $errContext, $errQuery, $errText;
        global $smarty;

        // Make sure there is at least some kind of error code
        // TODO:  Fix callers that don't!
        if (!isset($err) || is_null($err) || empty($err))
            $err = ERR_UNDEFINED;

        // Save error to database for later perusal
        log_error();

        // Display full blow error page
        $smarty->assign('err', $err);
        $smarty->assign('errCode', $errCode);
        $smarty->assign('errContext', $errContext);
        $smarty->assign('errQuery', $errQuery);
        $smarty->assign('errText', $errText);
        $smarty->assign('errStack', gen_stack(debug_backtrace()));
        return $smarty->fetch('error.tpl');
    }

	function gen_error($linePre = true, $linePost = true)
    {
        global $smarty;
        $msarty->trigger_error('gen_error has been replaced by gen_errorNew', E_USER_DEPRECATED);
        return gen_errorNew();
	}

//-----------------------------------------------------------------------------

    function gen_error_page($title, $msg, $path = '')
    {

        global $err, $errCode, $errContext, $errQuery, $errText;
        global $smarty, $username;

        // Make sure there is at least some kind of error code
        // TODO:  Fix callers that don't!
        if (!isset($err) || is_null($err) || empty($err))
            $err = ERR_UNDEFINED;

        // Save error to database for later perusal
        log_error();

        // Display full blow error page
        $smarty->assign('path', '(where, below)');
        $smarty->assign('title', $title);
        $smarty->assign('msg', $msg);
        $smarty->assign('ROOT', $path);
        $smarty->assign('username', $username);
        $smarty->assign('err', $err);
        $smarty->assign('errCode', $errCode);
        $smarty->assign('errContext', $errContext);
        $smarty->assign('errQuery', $errQuery);
        $smarty->assign('errText', $errText);
        $smarty->assign('errStack', gen_stack(debug_backtrace()));
        return $smarty->fetch('errorpage.tpl');
    }

//-----------------------------------------------------------------------------

	function gen_stack($stack)
	{
		$html = '<table><tbody>';
		foreach($stack as $index => $dump)
		{
			$html .= '<tr>'
					. '<td>' . $index . '</td>'
					. '<td>' . (isset($dump['function']) ? $dump['function'] : '&nbsp;') . '</td>'
					. '<td>' . (isset($dump['class']) ? $dump['class'] : '&nbsp;') . '</td>'
					. '<td>' . (isset($dump['file']) ? $dump['file'] : '&nbsp;') . '</td>'
					. '<td>' . (isset($dump['line']) ? $dump['line'] : '&nbsp;') . '</td>'
					. '</tr>';
		}
		return $html . '</tbody></table>';
	}

//-----------------------------------------------------------------------------

	function log_error()
	{
		global $err, $errCode, $errContext, $errQuery, $errText;
		db_insert('errors', array
			(
				'when' => 'NOW()',
				'icode' => $err,
				'ecode' => $errCode,
				'context' => '\'' . db_escape($errContext) . '\'',
				'query' => '\'' . db_escape($errQuery) . '\'',
				'what' => '\'' . db_escape($errText) . '\''
			));
		$text = '';
		if (!empty($errText))
			$text .= '$errText = "' . $errText . '". ';
		if (!empty($errContext))
			$text .= '$errContext = "' . $errContext . '". ';
		if (!empty($errQuery))
			$text .= '$errQuery = "' . $errQuery . '". ';
		if (!empty($errCode))
			$text .= '$errCode = ' . $errCode . '. ';
		$text .= '$err = ' . $err . '. ';
		error_log($text);
	}

?>
