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
 * Viale dei Mughetti 13/A											<br>
 * 10151 Torino (Italy)												<br>
 * Tel.:   (+39) 011 735645											<br>
 * Fax:    (+39) 011 735645											<br>
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
	 * As in every big IDE suc as Sun ONE or Microsoft Visual Studio
	 * you have the possibiliti to add the top menu for simple
	 * organization of masks.
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @package p4a
	 */
	class P4A_MENU extends P4A_WIDGET
	{
		/**
		 * Menu rendering interface type (drop_down|tabbed)
		 * @var string
		 * @access private
		 */
		var $type = 'drop_down';
		
		/**
		 * Menu elements
		 * @var array
		 * @access private
		 */
		//todo
		var $items = NULL;
		
		/**
		 * Subelements positions map.
		 * @var array
		 * @access private
		 */
		var $map_items = array();
		
		/**
		 * The element/subelement currently active.
		 * @var menu_item
		 * @access public
		 */
		var $item_active = NULL;
		
		/**
		 * Class constructor.
		 * @param string		Mnemonic identifier for the object.
		 * @access private
		 */
		function &p4a_menu($name = '')
		{
			parent::p4a_widget($name); 
			$this->build("P4A_Collection", "items");
		}
		
		/**
		 * Adds an element to the menu.
		 * @param string		Mnemonic identifier for the element.
		 * @param string		Item's label.
		 * @access public
		 */
		function addItem($name, $label = NULL)
		{
			$item =& $this->items->build("P4A_Menu_Item", $name);
			$item->setParent($this->getId());
			if( $label !== NULL ) {
				$item->setLabel($label);
			}
			
			$this->setItemPosition($item->name, $this->nextFreePosition());
 		}
		
		/**
		 * Removes an element from the menu.
		 * @param string		Mnemonic identifier for the element.
		 * @access public
		 */
		function dropItem($name)
		{
			if (isset($name, $this->items->$name)){
				$this->items->$name->destroy();
				unset($this->items->$name);
			}else{
				error("ITEM NOT FOUND");
			}
		}
		
		/**
		 * Returns true if the menu has items.
		 * @return boolean
		 * @access public
		 */
		function hasItems()
		{
			if ($this->items->getNumItems()){
				return TRUE;
			}else{
				return FALSE;
			}	
		}
		
		/**
		 * Sets the position for a menu element.
		 * @param string		Mnemonic identifier for the element.
		 * @param integer		The position.
		 * @access private
		 */
		function setItemPosition($item_name, $position)
		{
			if (isset($this->items->$item_name)){
				$this->map_items[$position] = $item_name;	
			}else{
				error("ITEM NOT FOUND: $item_name");
			}
		}
		
		/**
		 * Returns the first free position index.
		 * @return integer
		 * @access public
		 */ 
		function nextFreePosition()
		{
			if (count($this->map_items))
			{
				return max(array_keys($this->map_items)) + 1;					
			}else{
				return 1;
			}
		}
		
		/**
		 * Returns the first element name.
		 * @access private
		 */
		function getFirstItem()
		{
			if ($this->hasItems()){
				$min_pos = min(array_keys($this->map_items));
				return $this->map_items[$min_pos];
			}else{
				error("NOT SUB ITEM");
			}		
		}
		 	
		/**
		 * Sets the desidered element as active.
		 * @access private
		 */
		function setItemActive($name)
		{
			$this->item_active = $name;
		}		

		/**
		 * Returns the HTML rendered widget.
		 * @return string
		 * @access private
		 */
		function getAsString()
		{
			if (!$this->isVisible()) {
				return;
			}
			
			if ($this->type === 'drop_down')
			{
				$this->useTemplate('menu_drop_down');
				$array_items = array();
			
				// First level menu
				while($item =& $this->items->nextItem())
				{
					$array_item = array();
					$array_item['label'] = $item->label;
					if (($item->items->getNumItems())){
						$array_item['actions'] = $item->composeStringActions();
					}
					$array_item['properties'] = $item->composeStringProperties();
					$array_item['id'] = $item->getId();

					// Second level menu
					while($sub_item =& $item->items->nextItem())
					{
						if ($sub_item->isVisible()) {
							$array_sub_item = array();
							$array_sub_item['label'] = $sub_item->label;
							$array_sub_item['actions'] = $sub_item->composeStringActions();
							$array_sub_item['properties'] = $sub_item->composeStringProperties();
							$array_sub_item['id'] = $sub_item->getId();
					
							$array_item['sub_items'][] = $array_sub_item;
						}
						unset($sub_item);   
					}	
				
					$array_items[] = $array_item;
					unset($item);
				}						
			
				$this->display('properties', $this->composeStringProperties());
				$this->display('items', $array_items);
			}
			elseif($this->type === 'tabbed' or $this->type === 'tabbed_rounded')
			{
				$this->useTemplate('menu_' . $this->type);
				$aItems1 = array();
				$aItems2 = array();
			
				// First level menu
				foreach($this->items as $key=>$item)
				{
				
					if( $this->item_active === NULL ) {
						$this->setItemActive( $key ) ; 
					}
				
					$aItem = array();
					$aItem['label'] = $item->label;
					$aItem['actions'] =	$item->composeStringActions();
					if ($item->name == $this->item_active){
						$aItem['active'] = TRUE;
					}else{
						$aItem['active'] = FALSE;
					}
					$aItems1[] = $aItem;
				}
		
				// Second level menu			
				while($item =& $this->items->{$this->item_active}->items->nextItem())
				{
					$aItem = array();
					$aItem['label'] = $item->label;
					$aItem['actions'] =	$item->composeStringActions();
				
					if( $this->items->{$this->item_active}->item_active === NULL ) {
						$this->items->{$this->item_active}->setItemActive( $item->getName() ) ;
					}
				
					if ($item->name == $this->items->{$this->item_active}->item_active){
						$aItem['active'] = TRUE;
					}else{
						$aItem['active'] = FALSE;
					}
					$aItems2[] = $aItem; 	
					unset($item);
				}
						
				$this->display('items1', $aItems1);
				$this->display('items2', $aItems2);
				
			}
						
			return $this->fetchTemplate();

		}
		
		/**
		 * Changes the menu type.
		 * @param string		The type identifier
		 * @access public
		 * @see $type
		 */
		function setType($type)
		{
			$this->type = $type;
		}
	
	}
	
	/**
	 * Rapresents every menu item.
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @package p4a
	 */
	class P4A_MENU_ITEM extends P4A_WIDGET
	{
		/**
		 * Tells if the element is currenty active.
		 * @var boolean
		 * @access private
		 */
		var $active = FALSE;
		
		/**
		 * Subelements array.
		 * @var array
		 * @access private
		 */
		 //todo
		var $items = NULL;
		
		/**
		 * Name of the currently active subelement.
		 * @var array
		 * @access public
		 */
		var $item_active = NULL;
		
		/**
		 * Stores the shortkey associated with the element.
		 * @var string
		 * @access private
		 */
		var $key = NULL;
		
		/**
		 * Subelements positions map.
		 * @var array
		 * @access private
		 */
		var $map_items = array();
		
		/**
		 * Parent element of the item.
		 * @var object
		 * @access private
		 */
		var $parent = NULL;
		
		/**
		 * Class constructor.
		 * @param string		Mnemonic identifier for the object.
		 * @access private
		 */
		function &p4a_menu_item($name)
		{
			parent::p4a_widget($name);
			
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
		 //todo
		function addItem($name, $label = NULL)
		{
			$item =& $this->items->build("P4A_Menu_Item", $name);
			
			$this->setItemPosition($item->name, $this->nextFreePosition());
			
			if( $label !== NULL ) {
				$item->setLabel($label);
			}
		}
		
		/**
		 * Adds a separator to the element.
		 * @param string		Mnemonic identifier for the separator.
		 * @access public
		 */		
		function addSeparator($name)
		{
			$item =& $this->items->build("P4A_Menu_Item", $name);
			//todo
			$item->setParent($this->getId());
			$item->dropAction('onClick');
			$item->setLabel('');
				
		
			$item->setProperty('class', 'menuSeparator');
			$item->setStyleProperty('margin-left', '10px');
			$item->setStyleProperty('margin-right', '10px');

			$this->setItemPosition($item->name, $this->nextFreePosition());
		}
		
		/**
s		 * Removes an element from the element.
		 * @param string		Mnemonic identifier for the element.
		 * @access public
		 */
		function dropItem($name)
		{
			if (isset($this->items->$name)){
				$this->items->$name->destroy();
				unset($this->items->$name);
			}else{
				error("ITEM NOT FOUND");
			}
		}
		
		/**
		 * Returns true if the element has subelements.
		 * @return boolean
		 * @access public
		 */
		function hasItems()
		{
			if ($this->items->getNumItems()){
				return TRUE;
			}else{
				return FALSE;
			}	
		}
		
		/**
		 * Sets the position of a subelement.
		 * @param string		Mnemonic identifier for the element.
		 * @param integer		The position.
		 * @access private
		 */
		function setItemPosition($item_name, $position)
		{
			if (($this->items->$item_name)){
				$this->map_items[$position] = $item_name;
				$this->items->$item_name->setPosition($position);	
			}else{
				error("ITEM NOT FOUND: $item_name");
			}
		}
		
		/**
		 * Returns the first free position index.
		 * @return integer
		 * @access public
		 */
		function nextFreePosition()
		{
			if (count($this->map_items))
			{				
				return max(array_keys($this->map_items)) + 1;					
			}else{
				return 1;
			}
		}
		
		/**
		 * Returns the first subelement name.
		 * @return string
		 * @access private
		 */
		function getFirstItem()
		{
			if ($this->hasItems())
			{
				$min_pos = min(array_keys($this->map_items));
				return $this->map_items[$min_pos];
			}else{
				error("NOT SUB ITEM");
			}		
		}
		
		/**
		 * Sets as active the current element.
		 * @access private
		 */
		function setActive()
		{
			$p4a =& P4A::singleton();
			$parent =& $p4a->getObject($this->parent);
			$parent->setItemActive($this->name);
		}
		
		/**
		 * Sets as NON active the current element.
		 * @access private
		 */	
		function setNoActive()
		{
			$this->active = FALSE;
		}
		
		/**
		 * Sets an object as parent.
		 * @access private
		 */
		function setParent($object_id)
		{
			$this->parent = $object_id;
		}
		
		/**
		 * Sets the desidered subelement as active.
		 * @param string		Mnemonic identifier for the element.
		 * @access private
		 */	
		function setItemActive($name)
		{
			$this->item_active = $name;
			$this->setActive();
		}
		
		/**
		 * Sets the position of the current element.
		 * @param integer		The position.
		 * @access private
		 */
		function setPosition($position)
		{
			$this->position = $position;	
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
		 * @access private
		 */
		function onClick()
		{		
			// If the current element has subitems, than we pass the action to the subitem
			if ($this->hasItems()){
				return $this->items->{$this->getFirstItem()}->onClick();
			}else{
				$this->setActive();
				return $this->actionHandler('onClick');
			}
		}
		
	}
?>
