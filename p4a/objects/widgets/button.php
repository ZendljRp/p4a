<?php
/**
 * This file is part of P4A - PHP For Applications.
 *
 * P4A is free software: you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * P4A is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with Foobar.  If not, see <http://www.gnu.org/licenses/agpl.html>.
 * 
 * To contact the authors write to:									<br />
 * CreaLabs SNC														<br />
 * Via Medail, 32													<br />
 * 10144 Torino (Italy)												<br />
 * Website: {@link http://www.crealabs.it}							<br />
 * E-mail: {@link mailto:info@crealabs.it info@crealabs.it}
 *
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @copyright CreaLabs SNC
 * @link http://www.crealabs.it
 * @link http://p4a.sourceforge.net
 * @license http://www.gnu.org/licenses/agpl.html GNU Affero General Public License
 * @package p4a
 */

/**
 * HTML "button".
 * It's useful to trigger actions in easy way (with/without graphics).
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @package p4a
 */
class P4A_Button extends P4A_Widget
{
	/**
	* The icon used by button, if null standard html button is used.
	* @var string
	*/
	protected $_icon = null;
	
	/**
	 * Height of the button
	 * @var integer
	 */
	protected $_size = 32;
	
	/**
	 * @var boolean
	 */
	protected $_label_visible = false;

	/**
	 * @param string $name Mnemonic identifier for the object
	 * @param string $icon The icon taken from icon set (file name without extension)
	 */
	public function __construct($name, $icon = null)
	{
		parent::__construct($name);
		$this->setIcon($icon);
		$this->setLabel(P4A_Generate_Default_Label($name));
	}

	/**
	 * @param string $icon The icon taken from icon set (file name without extension) or path to an external image
	 */
	public function setIcon($icon)
	{
		$this->_icon = $icon;
	}

	/**
	 * @return string
	 */
	public function getIcon()
	{
		return $this->_icon;
	}

	/**
	 * @param integer $size
	 */
	public function setSize($size)
	{
		$this->_size = strtolower($size);
	}

	/**
	 * @return integer
	 */
	public function getSize()
	{
		return $this->_size;
	}
	
	/**
	 * Sets the label and its visibility
	 * When a label is visible it will be rendered next to the icon (if there's an icon),
	 * otherwise you'll see the lable as a tooltip.
	 *
	 * @param string $label
	 * @param boolean $visible
	 */
	public function setLabel($label, $visible = false)
	{
		parent::setLabel($label);
		$this->_label_visible = $visible;
	}

	/**
	 * Retuns the HTML rendered button
	 * @return string
	 */
	public function getAsString()
	{
		$id = $this->getId();
		if (!$this->isVisible()) {
			return "<span id='$id' class='hidden'></span>";
		}

		$label = htmlspecialchars(__($this->getLabel()), ENT_QUOTES);
		$title = $label;
		$class = $this->composeStringClass();
		$properties = $this->composeStringProperties();
		$actions = $this->composeStringActions();
		$accesskey = $this->getAccessKey();
		if (strlen($accesskey) > 0) $title = "[$accesskey] $title";
		if ($this->_label_visible or !$this->_icon) {
			$label = P4A_Highlight_AccessKey($label, $accesskey);
		} else {
			$label = null;
		}
		
		$icon = "";
		if ($this->_icon != null) {
			$size = $this->getSize();
			if (strpos($size, 'x') !== false) {
				list($width, $size) = explode('x', $size);
			}
			
			if (strpos($this->_icon, '.') !== false) {
				$icon = $this->_icon;
			} else {
				$icon = P4A_ICONS_PATH . "/{$size}/{$this->_icon}";
				if (!$this->isEnabled()) $icon .= "_disabled";
				$icon .= '.' . P4A_ICONS_EXTENSION;
			}
			$icon = "<img src='$icon' alt=''>";
		}
		
		return "<button id='$id' title='$title' $class $properties $actions>" . 
				P4A_Generate_Widget_Layout_Table($icon, $label) . '</button>';
	}
}