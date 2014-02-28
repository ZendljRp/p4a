<?php
/**
 * This file is part of P4A - PHP For Applications.
 *
 * P4A is free software: you can redistribute it and/or modify it
 * under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of
 * the License, or (at your option) any later version.
 *
 * P4A is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with P4A.  If not, see <http://www.gnu.org/licenses/lgpl.html>.
 *
 * To contact the authors write to:                                     <br />
 * Fabrizio Balliano <fabrizio@fabrizioballiano.it>                     <br />
 * Andrea Giardina <andrea.giardina@crealabs.it>
 *
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @copyright Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
 * @link http://p4a.sourceforge.net
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @package p4a
 */

namespace P4A\Widget;

/**
 * HTML "button".
 * It's useful to trigger actions in easy way (with/without graphics).
 * @author Fabrizio Balliano <fabrizio@fabrizioballiano.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @copyright Copyright (c) 2003-2010 Fabrizio Balliano, Andrea Giardina
 * @package p4a
 */
class Button extends Widget
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
     * @return P4A_Button
     */
    public function setIcon($icon)
    {
        $this->_icon = $icon;
        return $this;
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
     * @return P4A_Button
     */
    public function setSize($size)
    {
        $this->_size = strtolower($size);
        return $this;
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
     * @return P4A_Button
     */
    public function setLabel($label, $visible = false)
    {
        $this->_label_visible = $visible;
        return parent::setLabel($label);
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
        $properties = $this->composeStringProperties();
        $actions = $this->composeStringActions();
        $accesskey = $this->getAccessKey();
        if (strlen($accesskey) > 0) {
            $title = "[$accesskey] $title";
        }
        if ($this->_label_visible or !$this->_icon) {
            $label = P4A_Highlight_AccessKey($label, $accesskey);
        } else {
            $label = null;
        }

        $icon = "";
        if ($this->_icon != null) {
            $iconset = P4A_ICONSET;
            $icon = "<span class='$iconset $iconset-{$this->_icon}'></span>";
        }

        $class = array('btn', 'btn-default');
        $class = $this->composeStringClass($class);

        $tooltip_text = __($this->getTooltip());
        if ($tooltip_text) {
            $tooltip_text = "<div id='{$id}tooltip' class='p4a_tooltip'><div class='p4a_tooltip_inner'>$tooltip_text</div></div>";
            $actions .= " onmouseover='p4a_tooltip_show(this)' ";
        }

        return "<button id='$id' type='button' title='$title' $class $properties $actions>{$tooltip_text}{$icon}{$label}</button>";
    }
}