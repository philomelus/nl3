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

// Create object, passing array of menus as:
//
// array
// (
//	item id =>
//		array
// 		(
// 			MI_CODE => path to code,
//			MI_NAME => textual human readable name for display,
//			MI_PAGE => security page identifier,
//			MI_TIP => tip to display on hover,
//			MI_URL => url to access menu,
//    [opt] MI_SCRIPTS => Arry of paths of scripts to include,
//    [opt] MI_STYLES => Array of paths of css files to include
// 		)
// )
//
// then call events().  When events() will return when, uh, somethin...

define('MI_CODE',		'code');		// Path to code to include
define('MI_NAME',		'name');		// Name to display
define('MI_PAGE',		'page');		// Security page
define('MI_TIP',		'tip');			// Popup tip describing item
define('MI_URL',		'url');			// Url to access the item
define('MI_SCRIPTS',	'scripts');		// Text of scripts to include
define('MI_STYLES',		'styles');		// Text of styles to include

class _MenuBase
{
	protected $_menus;
	protected $_level;
	protected $_prefix;
	
	protected $_parent;
	protected $_parentItem;
	
	public function __construct(&$menus, $level = 1, $prefix = '')
	{
		$this->_menus = $menus;
		$this->_level = $level;
		$this->_prefix = $prefix;
		
		// Conjure up a prefix if not provided
		if ($this->_level > 2 && empty($this->_prefix))
			$this->_prefix = str_repeat('sub', $this->_level - 2);
	}
	
	public function begin()
	{
		reset($this->_menus);
		
		// Build array of allowed menu items
		$menus = array();
		foreach($this->_menus as $item => $menu)
		{
			if (isset($menu[MI_PAGE]))
			{
				if (allowed('page', $menu[MI_PAGE]))
					$menus[$item] = $menu;
			}
			else
				$menus[$item] = $menu;
		}
		
		reset($menus);
		return array(key($menus) => $this->_menus[key($menus)]);
	}
	
	public function display($active)
	{
		// Display parent menu if needed
		if (!empty($this->_parent))
			$this->_parent->display($this->_parentItem);
		
		// Display the menu
		gen_menu_secure($this->_menus, $active, $this->_level);
	}

	protected function display_begin()
	{
	}
	
	protected function display_end()
	{
	}
	
	public function display_page($active, $styles='', $script='')
	{
		global $err;
		global $resultHtml;
		global $action;
		
		// Include code if provided
		self::load($active);

		// Allow page to handle submission
		$resultHtml = $this->submit($action);
		
		// Get page specific styles if provided
		$this->get_styles($styles, $this->_menus[$active]);
		$name = $this->_prefix . 'styles';
		if (function_exists($name))
			$styles .= $name();
		
		// Get page specific scripts if provided
		$this->get_scripts($script, $this->_menus[$active]);
		$name = $this->_prefix . 'script';
		if (function_exists($name))
			$script .= $name();

		// Standard headers
		echo gen_htmlHeader($this->title($active), $styles, $script);
		echo '<body class="w3-container">';
		$this->header_begin();
		echo gen_header($this->title_linked($active));
		$this->header_end();
		
		// Display error if needed
		if ($this->error_begin())
		{
			if ($err < ERR_SUCCESS)
				echo gen_error(true, true);
			$this->error_end();
		}
		
		// Update display
		$this->display_begin();
		$name = $this->_prefix . 'display';
		if (function_exists($name))
			$name();
		$this->display_end();

		// Standard footer
		$this->footer_begin();
		echo gen_htmlFooter();
		$this->footer_end();
	}
	
	protected function error_begin()
	{
		return true;
	}
	
	protected function error_end()
	{
	}
	
	protected function footer_begin()
	{
	}
	
	protected function footer_end()
	{
	}
	
	private function get_scripts(&$scripts, $menu)
	{
		if (isset($this->_parent))
			$this->_parent->get_scripts($scripts, $this->_parent->_menus[$this->_parentItem]);
		if (isset($menu[MI_SCRIPTS]))
		{
			foreach($menu[MI_SCRIPTS] as $file)
				$scripts .= '<script type="text/javascript" src="' . $file . '"></script>';
		}
	}
	
	private function get_styles(&$styles, $menu)
	{
		if (isset($this->_parent))
			$this->_parent->get_styles($styles, $this->_parent->_menus[$this->_parentItem]);
		if (isset($menu[MI_STYLES]))
		{
			foreach($menu[MI_STYLES] as $file)
				$styles .= '<link rel="stylesheet" type="text/css" href="' . $file . '" />';
		}
	}
	
	protected function header_begin()
	{
	}
	
	protected function header_end()
	{
	}
	
	public function load($active)
	{
		if (isset($this->_menus[$active][MI_CODE]))
		{
			if (file_exists($this->_menus[$active][MI_CODE]))
				include $this->_menus[$active][MI_CODE];
		}
	}
	
	protected function menu_begin()
	{
	}
	
	protected function menu_end()
	{
	}
	
	public function prefix()
	{
		return $this->_prefix;
	}
	
	public function submit($active)
	{
		global $action;
		
		if (!isset($_REQUEST['action']))
			return '';

		if (empty($_REQUEST['action']))
			return '';
		
		$name = $this->_prefix . 'submit';
		if (function_exists($name))
		{
			$action = $_REQUEST['action'];
			return $name();
		}
		
		return '';
	}
	
	public function super(&$object, $menu)
	{
		$this->_parent = &$object;
		$this->_parentItem = $menu;
	}
	
	public function title($active)
	{
		return $this->_menus[$active][MI_NAME];
	}
	
	public function title_linked($active)
	{
		return $this->_menus[$active][MI_NAME];
	}
}

?>
