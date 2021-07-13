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
function update_keys()
{
	var key1 = document.getElementById("key1");
	var key2 = document.getElementById("key2");
	var key3 = document.getElementById("key3");
	if (key1 && key2 && key3)
	{
		if (key1.value == "All")
		{
			key2.value = "All";
			key2.disabled = true;
			key3.value = "All";
			key3.disabled = true;
		}
		else
		{
			key2.disabled = false;
			if (key2.value == "All")
			{
				key3.value = "All";
				key3.disabled = true;
			}
		}
	}
}
