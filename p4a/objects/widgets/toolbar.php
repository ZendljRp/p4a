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
 * A toolbar is a buttons/images set.
 * Every button/image can have an action handler.
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @package p4a
 */
class P4A_Toolbar extends P4A_Widget
{
	/**
	 * Counts the number of separators/spacers in the toolbar
	 * @var integer
	 */
	protected $separators_counter = 0;
	
	/**
	 * @var integer
	 */
	protected $_size = 32;

	/**
	 * Buttons collection
	 * @var array
	 */
	public $buttons = null;

	/**
	 * @param string $name Mnemonic identifier for the object
	 */
	public function __construct($name)
	{
		parent::__construct($name);
		$this->build("p4a_collection", "buttons");
	}

	/**
	 * Istances a new p4a_button object and than adds it to the toolbar
	 * @param string $button_name Mnemonic identifier for the object
	 * @param string $icon The icon taken from icon set (file name without extension)
	 * @param string $position (left|right)
	 * @see P4A_Button
	 */
	public function &addButton($button_name, $icon = null, $position = "left")
	{
		$this->buttons->build("p4a_button", $button_name);
		$this->buttons->$button_name->setIcon($icon);
		$this->buttons->$button_name->setStyleProperty("float", $position);
		if ($this->_size) {
			$this->buttons->$button_name->setSize($this->_size);
		}
		return $this->buttons->$button_name;
	}

	/**
	 * Adds a separator image
	 * @param string $position (left|right)
	 * @return P4A_Image
	 */
	public function &addSeparator($position = "left")
	{
		$name = 's' . $this->separators_counter++;
		$this->buttons->build("p4a_image", $name);
		$this->buttons->$name->setIcon("separator");
		$this->buttons->$name->setStyleProperty("float", $position);
		return $this->buttons->$name;
	}

	/**
	 * Adds a P4A_Box object
	 * @see P4A_Box
	 * @param string $name
	 * @param string $text
	 * @param string $position (left|right)
	 * @return P4A_Box
	 */
	public function &addBox($name, $text, $position = "left")
	{
		$this->buttons->build("p4a_box", $name);
		$this->buttons->$name->setValue($text);
		$this->buttons->$name->setStyleProperty("float", $position);
		return $this->buttons->$name;
	}

	/**
	 * Adds a spacer image of the desidered width
	 * @param integer $width
	 * @param string $position (left|right)
	 * @return P4A_Image
	 */
	public function &addSpacer($width = 10, $position = "left")
	{
		$name = 's' . $this->separators_counter++;
		$this->buttons->build("p4a_image", $name);
		$this->buttons->$name->setStyleProperty("float", $position);
		$this->buttons->$name->setIcon(P4A_ICONS_PATH . "/spacer." . P4A_ICONS_EXTENSION);
		$this->buttons->$name->setWidth($width);
		$this->buttons->$name->setHeight(1);
		return $this->buttons->$name;
	}

	/**
	 * Disables all buttons
	 */
	public function disable()
	{
		while ($button =& $this->buttons->nextItem()) {
			$button->disable();
		}
	}

	/**
	 * Enables all buttons
	 */
	public function enable()
	{
		while ($button =& $this->buttons->nextItem()) {
			$button->enable();
		}
	}

	/**
	 * @param integer $size
	 */
	public function setSize($size)
	{
		$this->_size = $size;

		while ($button =& $this->buttons->nextItem()) {
			$button->setSize($size);
		}
	}

	/**
	 * @return integer
	 */
	public function getSize()
	{
		return $this->_size;
	}

	/**
	 * Returns the HTML rendered widget
	 * @return string
	 */
	public function getAsString()
	{
		$id = $this->getId();
		if (!$this->isVisible()) {
			return "<div id='$id' class='hidden'></div>";
		}

		$size = $this->getSize();
		$properties = $this->composeStringProperties();
		$class = $this->composeStringClass(array("p4a_toolbar", "p4a_toolbar_$size"));
		$return = "<div id='$id' $class $properties>";
		while($button =& $this->buttons->nextItem()) {
			$return .= $button->getAsString();
		}
		$return .= "<div class='br'></div>\n";
		$return .= "</div>\n\n";
		return $return;
	}
}