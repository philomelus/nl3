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
	require_once 'inc/securitydatabase.inc.php';
	
	class SecurityData extends SecurityDataBase
	{
		private static $instance;
		private static $descriptions;
		
		private function __construct() 
		{
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
			if (empty(self::$descriptions))
				SecurityDataBase::initialize(self::$descriptions);
			return self::$descriptions;
		}
		
		public function features()
		{
			static $features;
			
			if (empty($features))
			{
				$features[] = 'page';
			
				sort($features);
			}
			
			return $features;
		}
		
		public function featuresByPage($page)
		{
			$features = array();
			if (empty(self::$descriptions))
				$this->features();
			if (isset(self::$descriptions[$page]))
			{
				foreach(self::$descriptions[$page] as $f => $d)
					$features[] = $f;
			}
			return $features;
		}
		
		public function &pages()
		{
			static $pages;
			
			if (empty($pages))
				SecurityDataBase::_pages($pages);
			
			return $pages;
		}
	}
		
?>