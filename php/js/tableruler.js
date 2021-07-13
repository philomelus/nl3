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
window.onload = function()
	{
		tableruler();
	}
function tableruler()
{
	if (document.getElementById && document.createTextNode)
	{
		var tables = document.getElementsByTagName('table');
		for (var i = 0; i < tables.length; ++i)
		{
			if (tables[i].className == 'ruler')
			{
				var trs = tables[i].getElementsByTagName('tr');
				for (var j = 0; j < trs.length; ++j)
				{
					if (trs[j].parentNode.nodeName == 'TBODY')
					{
						trs[j].onmouseover = function()
						{
							this.className='ruled';
							return false;
						}
						trs[j].onmouseout = function()
						{
							this.className='';
							return false;
						}
					}
				}
			}
		}
	}
}
