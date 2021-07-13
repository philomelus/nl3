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
var previous = '9999.99';
var payments = '9999.99';
var rate = '9999.99';
var adjustments = '9999.99';
$(document).ready(
	function()
	{
		$("#period").change(
			function()
			{
				var id = $(this).val();
				$(".period_start").text(periods.DSTART[id]);
				$(".period_end").text(periods.DEND[id]);
			});
		$("#period").change();
		$(".propigate").keyup(
			function (event)
			{
				var pre = $("#previous").val();
				var pay = $("#payments").val();
				var rat = $("#rate").val();
				var adj = $("#adjustments").val();
				if (previous != pre || payments != pay
						|| rate != rat || adjustments != adj)
				{
					var total = parseFloat(pre)
							- parseFloat(pay)
							+ parseFloat(rat)
							+ parseFloat(adj);
					if (!isNaN(total))
					{
						previous = pre;
						payments = pay;
						rate = rat;
						adjustments = adj;
						var s = printf('%01.2f', total);
						$("#total").val(s);
						$("#totaldue").text('$' + s);
					}
				}
			});
		$(".propigate").keyup();
		$("#cancel").click(
			function(event)
			{
				window.close();
				return false;
			});
	});
