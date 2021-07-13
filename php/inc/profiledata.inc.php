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
	require_once 'inc/profiledatabase.inc.php';
	
	class ProfileData extends ProfileDataBase
	{
		// If the contents of these constants are changed, gen_ProfileData must be updated
		const DESC = '@desc';					// Textual desription of setting
		const ENUM = '@enum';					// Array of integer => string for valid enum values
		const TYPE = '@type';					// Type of the setting, from the CFG_ constants
		const IS_GLOBAL = '@global';			// T/F whether the setting is a global only setting (default F)
		const IS_READONLY = '@readOnly';		// True if value isn't editable
		const IS_REQUIRED = '@required';		// True if the setting is required for NewsLedger to function
		
		private static $instance;

		private static $descriptions;
		
		private static $keys;
		private static $keys1;
		private static $keys2;
		private static $keys3;
		private static $keys4;
		
		private static $gkeys;
		private static $gkeys1;
		private static $gkeys2;
		private static $gkeys3;
		private static $gkeys4;
		
		private static $keys2fromkeys1;
		
		private static $gkeys2fromkeys1;
		
		private function __construct() 
		{
			ProfileDataBase::initialize(self::$descriptions);
			
			ProfileDataBase::_keys(self::$keys, false);
			ProfileDataBase::_keys1(self::$keys1, false);
			ProfileDataBase::_keys2(self::$keys2, false);
			ProfileDataBase::_keys3(self::$keys3, false);
			ProfileDataBase::_keys2FromKeys1(self::$keys2fromkeys1, false);
			
			ProfileDataBase::_keys(self::$gkeys, true);
			ProfileDataBase::_keys1(self::$gkeys1, true);
			ProfileDataBase::_keys2(self::$gkeys2, true);
			ProfileDataBase::_keys3(self::$gkeys3, true);
			ProfileDataBase::_keys2FromKeys1(self::$gkeys2fromkeys1, true);
		}
		
		public static function create()
		{
			if (!isset(self::$instance))
			{
				$c = __CLASS__;
				self::$instance = new $c;
			}
			return self::$instance;
		}

		public function descriptions()
		{
			return $this->descriptions;
		}

		public function &keys($global = false)
		{
			if ($global)
				return self::$gkeys;
			return self::$keys;
		}
		
		public function &keys1($global = false)
		{
			if ($global)
				return self::$gkeys1;
			return self::$keys1;
		}
		
		public function &keys2($global = false)
		{
			if ($global)
				return self::$gkeys2;
			return self::$keys2;
		}
	
		public function keys2FromKeys1($global, $key1)
		{
			if ($global)
				return self::$gkeys2fromkeys1[$key1];
			return self::$keys2fromkeys1[$key1];
		}
		
		public function &keys3($global = false)
		{
			if ($global)
				return self::$gkeys3;
			return self::$keys3;
		}
	
		public function keys3FromKeys2($global, $key2)
		{
			$keys = array();
			$keys1 = $this->keys1();
			foreach($keys1 as $key1)
			{
				foreach(self::$descriptions[$key1] as $key2key => $val2)
				{
					if ($key2key == $key2)
					{
						foreach($val2 as $key3 => $val3)
						{
							if (!in_array($key3, $keys))
							{
								if (isset($val3[self::IS_GLOBAL]))
								{
									if ($global == $val3[self::IS_GLOBAL])
										$keys[] = $key3;
								}
								else if (!$global)
									$keys[] = $key3;
							}
						}
					}
				}
			}
			natsort($keys);
			return $keys;
		}
		
		public function lookup($key)
		{
			$cmd = 'return self::$descriptions';
			foreach(explode('-', $key) as $key)
				$cmd .= '[\'' . $key . '\']';
			return eval($cmd . ';');
		}
	}
		
?>
