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
	 * Every DATA_SOURCE field is a DATA_FIELD.
	 * It's used to emulate some database behaviours
	 * such as default values.<br>
	 * It can be considered the same as a database table's field.
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @package p4a
	 */
	class P4A_DATA_FIELD extends P4A_OBJECT
	{
		/**
		 * The value of field.
		 * @var string
		 * @access private
		 */
		var $value = NULL;
				
		/**
		 * The new value of field
		 * @var string
		 * @access private
		 */
		var $new_value = NULL;

		/**
		 * The default value for the field in new rows.
		 * @var string
		 * @access private
		 */
		var $default_value = NULL;

		/**
		 * The default value for the field in new rows.
		 * @var string
		 * @access private
		 */
		var $type = 'text';
		
		
		var $is_updatable = TRUE;
						
		/**
		 * Class constructor.
		 * Sets ID and name for the object.
		 * @param string		Mnemonic identifier for the object.
		 * @access private
		 */
		function &p4a_data_field($name)
		{
			parent::p4aObject((string)$name);
		}
		
		/**
		 * Sets the value of the data field.
		 * @access public
		 */
		function setValue($value)
		{
			$this->value = $value;
			$this->setNewValue($value);
		}
		
		/**
		 * Returns the value of the data field.
		 * @access public
		 * @return mixed
		 */
		function getValue()
		{
			return $this->value;
		}
		
		/**
		 * Sets the new value of the data field.
		 * @access public
		 */
		function setNewValue($value)
		{
			$this->new_value = $value;
		}
			
		/**
		 * Returns the new value of the data field.
		 * @access public
		 * @return mixed
		 */
		function getNewValue()
		{
			return $this->new_value;
		}
		
		/**
		 * Sets the type of the data_field.
		 * @access public
		 * @param string		The type
		 */
		function setType($type)
		{
			$this->type = $type;
		}
		
		/**
		 * Returns the type of the data_field.
		 * @access public
		 * @return string
		 */
		function getType()
		{
			return $this->type;
		}
		
		//todo
		function setUpdatable($value = TRUE)
		{
			$this->is_updatable = $value;
		}
		
		//todo
		function isUpdatable()
		{
			return $this->is_updatable;
		}
		
		//todo
		function setDefaultValue()
		{
			$this->setValue($this->default_value);
		}
	}
?>