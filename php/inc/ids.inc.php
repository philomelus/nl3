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

	define('IDS_EDIT', 0x01);
	define('IDS_VIEW', 0x02);
	define('IDS_BOTH', IDS_EDIT | IDS_VIEW);
	
//-----------------------------------------------------------------------------

	function gen_c_adjustmentid($id)
	{
		return sprintf('%08d', $id);
	}

//-----------------------------------------------------------------------------

	function gen_c_billid($customer_id, $period_id)
	{
		return sprintf('%d%04d', $customer_id, $period_id);
	}
	
//-----------------------------------------------------------------------------

	function gen_c_complaintid($id)
	{
		return sprintf('%08d', $id);
	}

//-----------------------------------------------------------------------------

	function gen_c_paymentid($id, $page = '', $show = IDS_BOTH, $path = '')
	{
		$html = sprintf('%08d', $id);
		if (!empty($page))
		{
			if (($show & IDS_VIEW) === IDS_VIEW)
			{
				if (allowed('payment-view', $page))
				{
					$html .= '<button type="submit" onclick="' . CustomerPaymentViewUrl($id, $path)
							. '; return false;"><img src="' .  $path . 'img/view_s.png" alt="V" /></button>';
				}
			}
			if (($show & IDS_EDIT) === IDS_EDIT)
			{
				if (allowed('payment-edit', $page))
				{
					$html .= '<button type="submit" onclick="' . CustomerPaymentEditUrl($id, $path)
							. '; return false;"><img src="' . $path . 'img/edit_s.png" alt="E" /></button>';	
				}
			}
		}
		return $html;
	}

//-----------------------------------------------------------------------------

	function gen_c_serviceid($id)
	{
		return sprintf('%08d', $id);
	}
	
//-----------------------------------------------------------------------------

	function gen_c_servicetypeid($id)
	{
		return sprintf('%08d', $id);
	}
	
//-----------------------------------------------------------------------------

	function gen_c_typeid($id)
	{
		return sprintf('%04d', $id);
	}
	
//-----------------------------------------------------------------------------

	function gen_customerid($id, $page = '', $path = '', $btn = true)
	{
		$html = sprintf('%08d', $id);
		if (!empty($page))
		{
			if (allowed('customer-view', $page))
			{
				if ($btn)
				{
					$html .= '<button type="submit" onclick="' . CustomerViewUrl($id, $path)
							. '; return false;"><img src="' .  $path . 'img/view.png" alt="V" /></button>';
				}
				else
				{
					$html .= '<span>'
							. CustomerViewLink($id)
							. '<img src="' . $path . 'img/view.png" alt="V" title="View Customer' . sprintf('%08d', $id) . '" />'
							. '</a>'
							. '</span>';
				}
			}
			if (allowed('customer-edit', $page))
			{
				if ($btn)
				{
					$html .= '<button type="submit" onclick="' . CustomerEditUrl($id, $path)
							. '; return false;"><img src="' . $path . 'img/edit.png" alt="E" /></button>';
				}
				else
				{
					$html .= '<span>'
							. CustomerEditLink($id)
							. '<img src="' . $path . 'img/edit.png" alt="E" title="Edit Customer ' . sprintf('%08d', $id) . '" />'
							. '</a>'
							. '</span>';
				}
			}
		}
		return $html;
	}

//-----------------------------------------------------------------------------

	function gen_groupid($id)
	{
		return sprintf('%08d', $id);
	}
	
//-----------------------------------------------------------------------------

	function gen_periodid($id, $page = '', $path = '')
	{
		$html = iid2title($id);
		if (!empty($page))
		{
			if (allowed('period-view', $page))
			{
				$html .= '<button type="submit" onclick="' . PeriodViewUrl($id, $path)
						. '; return false;"><img src="' .  $path . 'img/view.png" alt="V" /></button>';
			}
			if (allowed('period-edit', $page))
			{
				$html .= '<button type="submit" onclick="' . PeriodEditUrl($id, $path)
						. '; return false;"><img src="' . $path . 'img/edit.png" alt="E" /></button>';	
			}
		}
		return $html;
	}

//-----------------------------------------------------------------------------

	function gen_s_type($type)
	{
		return $type;
	}
	
//-----------------------------------------------------------------------------

	function gen_userid($id)
	{
		return sprintf('%04d', $id);
	}
	
?>
