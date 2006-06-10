<?php

/**
 * P4A - PHP For Applications.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * To contact the authors write to:									<br>
 * CreaLabs															<br>
 * Via Medail, 32													<br>
 * 10144 Torino (Italy)												<br>
 * Web:    {@link http://www.crealabs.it}							<br>
 * E-mail: {@link mailto:info@crealabs.it info@crealabs.it}
 *
 * The latest version of p4a can be obtained from:
 * {@link http://p4a.sourceforge.net}
 *
 * @link http://p4a.sourceforge.net
 * @link http://www.crealabs.it
 * @link mailto:info@crealabs.it info@crealabs.it
 * @copyright CreaLabs
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @package p4a
 */

	/**
	 * p4a menu system.
	 * As in every big IDE such as Sun ONE or Microsoft Visual Studio
	 * you have the possibility to add the top menu for simple
	 * organization of masks.
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @package p4a
	 */
	class P4A_Menu extends P4A_Widget
	{
		/**
		 * Menu elements
		 * @var P4A_Collection
		 * @access public
		 */
		var $items = NULL;

		/**
		 * The element/subelement currently active.
		 * @var menu_item
		 * @access private
		 */
		var $_active_item = NULL;

		/**
		 * Class constructor.
		 * @param string		Mnemonic identifier for the object.
		 * @access private
		 */
		function P4A_Menu($name = '')
		{
			parent::P4A_Widget($name);
			$this->build("P4A_Collection", "items");
		}

		/**
		 * Adds an element to the menu.
		 * @param string		Mnemonic identifier for the element.
		 * @param string		Item's label.
		 * @access public
		 */
		function &addItem($name, $label = NULL)
		{
			$item =& $this->items->build("P4A_Menu_Item", $name);
			if ($label !== NULL) {
				$item->setLabel($label);
			}

			return $item;
 		}

		/**
		 * Removes an element from the menu.
		 * @param string		Mnemonic identifier for the element.
		 * @access public
		 */
		function dropItem($name)
		{
			if (isset($name, $this->items->$name)) {
				$this->items->$name->destroy();
				unset($this->items->$name);
			} else {
				P4A_Error("ITEM NOT FOUND");
			}
		}

		/**
		 * Returns true if the menu has items.
		 * @return boolean
		 * @access public
		 */
		function hasItems()
		{
			if ($this->items->getNumItems()) {
				return TRUE;
			} else {
				return FALSE;
			}
		}

		/**
		 * Sets the desidered element as active.
		 * @access private
		 */
		function setActiveItem($name)
		{
			$this->_active_item = $name;
		}
		
		/**
		 * Returns the active item name
		 * @access public
		 */
		function getActiveItem()
		{
			return $this->_active_item;
		}

		/**
		 * Returns the HTML rendered widget.
		 * @return string
		 * @access public
		 */
		function getAsString()
		{
			$id = $this->getId();
			if (!$this->isVisible()) {
				return "<div id='$id' class='hidden'></div>";
			}
			
			$sReturn = "";
			if ($this->hasItems()) {
				$sReturn .= "<ul class='p4a_menu'>";
				while ($item =& $this->items->nextItem()) {
					$sReturn .= $item->getAsString();
				}
				$sReturn .= "</ul>";
			}
			return $sReturn;
		}
	}

	/**
	 * Rapresents every menu item.
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @package p4a
	 */
	class P4A_Menu_Item extends P4A_Widget
	{
		/**
		 * Subelements array.
		 * @var P4A_Collection
		 * @access private
		 */
		var $items = NULL;
		
		/**
		 * The element/subelement currently active.
		 * @var menu_item
		 * @access private
		 */
		var $_active_item = NULL;

		/**
		 * Stores the shortkey associated with the element.
		 * @var string
		 * @access private
		 */
		var $key = NULL;

		/**
		 * Class constructor.
		 * @param string		Mnemonic identifier for the object.
		 * @access private
		 */
		function P4A_Menu_Item($name)
		{
			parent::P4A_Widget($name);

			$this->setDefaultLabel();
			$this->addAction('onClick');
			$this->build("P4A_Collection", "items");
		}

		/**
		 * Adds an element to the element.
		 * @param string		Mnemonic identifier for the element.
		 * @param string		Item's label.
		 * @access public
		 */
		function &addItem($name, $label = null)
		{
			$item =& $this->items->build("P4A_Menu_Item", $name);

			if ($label !== null) {
				$item->setLabel($label);
			}

			return $item;
		}

		/**
		 * Removes an element from the element.
		 * @param string		Mnemonic identifier for the element.
		 * @access public
		 */
		function dropItem($name)
		{
			if (isset($this->items->$name)) {
				$this->items->$name->destroy();
				unset($this->items->$name);
			} else {
				P4A_Error("ITEM NOT FOUND");
			}
		}

		/**
		 * Returns true if the element has subelements.
		 * @return boolean
		 * @access public
		 */
		function hasItems()
		{
			if ($this->items->getNumItems()) {
				return TRUE;
			} else {
				return FALSE;
			}
		}

		/**
		 * Sets the desidered subelement as active.
		 * @param string		Mnemonic identifier for the element.
		 * @access private
		 */
		function setActiveItem($name)
		{
			$this->_active_item = $name;
			$this->setActive();
		}
		
		/**
		 * Returns the active item name
		 * @access public
		 */
		function getActiveItem()
		{
			return $this->_active_item;
		}

		/**
		 * Sets the access key for the element.
		 * @param string		The access key.
		 * @access public
		 * @see $key
		 */
		function setAccessKey($key)
		{
			$this->setProperty('accesskey', $key);
		}

		/**
		 * Removes the access key for the element.
		 * @access public
		 * @see $key
		 */
		function unsetAccessKey()
		{
			$this->unsetProperty('accesskey');
		}

		/**
		 * What is executed on a click on the element.
		 * If the current element has subitems,
		 * than we pass the action to the subitem.
		 * @access public
		 */
		function onClick()
		{
			return $this->actionHandler('onClick');
		}
		
		/**
		 * Renders HTML
		 * @access public
		 * @return string
		 */
		function getAsString()
		{
			if (!$this->isVisible()) {
				return "";
			}
			
			$properties = $this->composeStringProperties();
			
			if (empty($this->_map_actions["onClick"]["method"])) {
				$sReturn = "<li><div $properties>" . $this->getLabel() . "</div>";
			} else {
				$actions = $this->composeStringActions();
				$sReturn = "<li><a href='#' $actions $properties>" . $this->getLabel() . "</a>";
			}
			
			if ($this->hasItems()) {
				$sReturn .= "<ul>";
				while ($item =& $this->items->nextItem()) {
					$sReturn .= $item->getAsString();
				}
				$sReturn .= "</ul>";
			}
			$sReturn .= "</li>";
			return $sReturn;
		}
	}
?>