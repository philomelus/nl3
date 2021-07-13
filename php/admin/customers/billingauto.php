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
	$message = '';
	$errors = array();
	
	//-------------------------------------------------------------------------
	
	function subdisplay()
	{
		global $err, $errCode, $errContext, $errQuery, $errText;
		global $message;
		global $errors;
		
		if (!isset($_POST['fs-daily-org']))
			$_POST['fs-daily-org'] = sprintf('%01.2f', floatval(get_config('flag-stop-daily-rate', 0)));
		if (!isset($_POST['fs-sunday-org']))
			$_POST['fs-sunday-org'] = sprintf('%01.2f', floatval(get_config('flag-stop-sunday-rate', 0)));
		if (!isset($_POST['billing-minimum-org']))
			$_POST['billing-minimum-org'] = get_config('billing-minimum', 0.01);
		if (!isset($_POST['flag-stop-billing-minimum-org']))
			$_POST['flag-stop-billing-minimum-org'] = get_config('flag-stop-billing-minimum', 0.01);
		if (!isset($_POST['daily-org']))
			$_POST['daily-org'] = sprintf('%01.2f', floatval(get_config('customers-daily-only-cost', 0)));
		if (!isset($_POST['sunday-org']))
			$_POST['sunday-org'] = sprintf('%01.2f', floatval(get_config('customers-sunday-only-cost', 0)));
		if (!isset($_POST['daily-single-org']))
			$_POST['daily-single-org'] = sprintf('%01.4f', floatval(get_config('customers-daily-single-cost', 0)));
		if (!isset($_POST['sunday-single-org']))
			$_POST['sunday-single-org'] = sprintf('%01.4f', floatval(get_config('customers-sunday-single-cost', 0)));
			
		if (isset($_POST['daily']))
			$daily = sprintf('%01.2f', floatval($_POST['daily']));
		else
			$daily = $_POST['daily-org'];
		
		if (isset($_POST['sunday']))
			$sunday = sprintf('%01.2f', floatval($_POST['sunday']));
		else
			$sunday = $_POST['sunday-org'];
		
		if (isset($_POST['daily-single']))
			$dailySingle = sprintf('%01.4f', floatval($_POST['daily-single']));
		else
			$dailySingle = $_POST['daily-single-org'];
		
		if (isset($_POST['sunday-single']))
			$sundaySingle = sprintf('%01.4f', floatval($_POST['sunday-single']));
		else
			$sundaySingle = $_POST['sunday-single-org'];
		
		if (isset($_POST['fs-daily']))
			$fsDaily = sprintf('%01.2f', floatval($_POST['fs-daily']));
		else
			$fsDaily = $_POST['fs-daily-org'];
		
		if (isset($_POST['fs-sunday']))
			$fsSunday = sprintf('%01.2f', floatval($_POST['fs-sunday']));
		else
			$fsSunday = $_POST['fs-sunday-org'];
		
		if (isset($_POST['billing-minimum']))
			$billingMinimum = sprintf('%01.2f', floatval($_POST['billing-minimum']));
		else
			$billingMinimum = sprintf('%01.2f', $_POST['billing-minimum-org']);
		
		if (isset($_POST['flag-stop-billing-minimum']))
			$flagStopBillingMinimum = sprintf('%01.2f', floatval($_POST['flag-stop-billing-minimum']));
		else
			$flagStopBillingMinimum = sprintf('%01.2f', $_POST['flag-stop-billing-minimum-org']);
		
		if (count($errors) > 0)
		{
			foreach($errors as $e)
				echo $e;
		}
		
		if (!empty($message))
			echo '<div>' . $message . '</div>';
		
?>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
		<table>
			<tbody>
				<tr>
					<td>Flag Stop - Daily</td>
					<td>
						$<input type="text" name="fs-daily" size="6" value="<?php echo $fsDaily ?>" />&nbsp;each
					</td>
				</tr>
				<tr>
					<td>Flag Stop - Sunday</td>
					<td>
						$<input type="text" name="fs-sunday" size="6" value="<?php echo $fsSunday ?>" />&nbsp;each
					</td>
				</tr>
				<tr>
					<td>Print Bills Owing At Least</td>
					<td>
						$<input type="text" name="billing-minimum" size="6" value="<?php echo $billingMinimum ?>" />
					</td>
				</tr>
				<tr>
					<td>Print Flag Stop Bills Owing At Least</td>
					<td>
						$<input type="text" name="flag-stop-billing-minimum" size="6" value="<?php echo $flagStopBillingMinimum ?>" />
					</td>
				</tr>
				<tr>
					<td>Daily Cost - Period</td>
					<td>
						$<input type="text" name="daily" size="6" value="<?php echo $daily ?>" />&nbsp;per&nbsp;Period
					</td>
				</tr>
				<tr>
					<td>Sunday Cost - Period</td>
					<td>
						$<input type="text" name="sunday" size="6" value="<?php echo $sunday ?>" />&nbsp;per&nbsp;Period
					</td>
				</tr>
				<tr>
					<td>Daily Cost - Single</td>
					<td>
						$<input type="text" name="daily-single" size="6" value="<?php echo $dailySingle ?>" />&nbsp;each
					</td>
				</tr>
				<tr>
					<td>Sunday Cost - Single</td>
					<td>
						$<input type="text" name="sunday-single" size="6" value="<?php echo $sundaySingle ?>" />&nbsp;each
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="submit" name="action" value="Save Changes" />
					</td>
				</tr>
			</tbody>
		</table>
		<input type="hidden" name="daily-org" value="<?php echo $_POST['daily-org'] ?>" />
		<input type="hidden" name="sunday-org" value="<?php echo $_POST['sunday-org'] ?>" />
		<input type="hidden" name="daily-single-org" value="<?php echo $_POST['daily-single-org'] ?>" />
		<input type="hidden" name="sunday-single-org" value="<?php echo $_POST['sunday-single-org'] ?>" />
		<input type="hidden" name="fs-daily-org" value="<?php echo $_POST['fs-daily-org'] ?>" />
		<input type="hidden" name="fs-sunday-org" value="<?php echo $_POST['fs-sunday-org'] ?>" />
		<input type="hidden" name="billing-minimum-org" value="<?php echo $_POST['billing-minimum-org'] ?>" />
		<input type="hidden" name="flag-stop-billing-minimum-org" value="<?php echo $_POST['flag-stop-billing-minimum-org'] ?>" />
		<input type="hidden" name="menu" value="<?php echo $_REQUEST['menu'] ?>" />
	</form>
<?php
	}
	
	//-------------------------------------------------------------------------
	
	function subsubmit()
	{
		global $err, $errCode, $errContext, $errQuery, $errText;
		global $message;
		global $errors;
		
		if (!preg_match('/^[0-9]+(,[0-9]{3})*(\.[0-9]{0,2})?$/', $_POST['daily']))
			$errors[] = 'Daily Cost - Period is invalid';
		if (!preg_match('/^[0-9]+(,[0-9]{3})*(\.[0-9]{0,2})?$/', $_POST['sunday']))
			$errors[] = 'Sunday Cost - Period is invalid';
		if (!preg_match('^[0-9]+(,[0-9]{3})*(\.[0-9]{0,4})?$/', $_POST['daily-single']))
			$errors[] = 'Daily Cost - Single is invalid';
		if (!preg_match('/^[0-9]+(,[0-9]{3})*(\.[0-9]{0,4})?$/', $_POST['sunday-single']))
			$errors[] = 'Sunday Cost - Single is invalid';
		if (!preg_match('/^[0-9]+(,[0-9]{3})*(\.[0-9]{0,2})?$/', $_POST['fs-daily']))
			$errors[] = 'Flag Stop - Daily is invalid';
		if (!preg_match('/^[0-9]+(,[0-9]{3})*(\.[0-9]{0,2})?$/', $_POST['fs-sunday']))
			$errors[] = 'Flag Stop - Sunday is invalid';
		if (!preg_match('/^[0-9]+(,[0-9]{3})*(\.[0-9]{0,2})?$/', $_POST['billing-minimum']))
			$errors[] = 'Print Bills Owing At Least is invalid - ' . $_POST['billing-minimum'];
		if (!preg_match('/^[0-9]+(,[0-9]{3})*(\.[0-9]{0,2})?$/', $_POST['flag-stop-billing-minimum']))
			$errors[] = 'Print Flag Stop Bills Owing At Least is invalid - ' . $_POST['flag-stop-billing-minimum'];
		
		if (count($errors) == 0)
		{
			$updated = array();
			do
			{
				// Update the rest as needed
				foreach(array
					(
						'daily' => array
							(
								'Daily Cost - Period',
								'customers-daily-only-cost',
								'%01.2f'
							),
						'sunday' => array
							(
								'Sunday Cost - Period',
								'customers-sunday-only-cost',
								'%01.2f'
							),
						'daily-single' => array
							(
								'Daily Cost - Single',
								'customers-daily-single-cost',
								'%01.4f'
							),
						'sunday-single' => array
							(
								'Sunday Cost - Single',
								'customers-sunday-single-cost',
								'%01.4f'
							),
						'fs-daily' => array
							(
								'Flag Stop - Daily',
								'flag-stop-daily-rate',
								'%01.2f'
							),
						'fs-sunday' => array
							(
								'Flag Stop - Sunday',
								'flag-stop-sunday-rate',
								'%01.2f'
							),
						'billing-minimum' => array
							(
								'Billing Minimum',
								'billing-minimum',
								'%01.2f'
							),
						'flag-stop-billing-minimum' => array
							(
								'Flag Stop Billing Minimum',
								'flag-stop-billing-minimum',
								'%01.2f'
							)
					) as $i => $f)
				{
					$temp = sprintf($f[2], floatval($_POST[$i]));
					if ($temp != $_POST[$i . '-org'])
					{
						set_globalConfig($f[1], $temp);
						if ($err < ERR_SUCCESS)
							break;
						$updated[$f[0]] = array($_POST[$i . '-org'], $temp);
					}
				}
			} while (false);
			if ($err >= ERR_SUCCESS)
			{
				if (count($updated) > 0)
				{
					$message = '<span>Changes saved successfully.</span>';
					$temp = '';
					foreach($updated as $field => $values)
						$temp .= ' ' . $field . ' was ' . $values[0] . ' and now is ' . $values[1] . '.';
					audit('Updated billing settings.' . $temp);
				}
				else
					$message = '<span>No changes needed saving.</span>';
			}
			else
				$message = '<span>Not all changes saved due to error!</span>';
		}
	}

?>
