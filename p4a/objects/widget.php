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
	 * Base class for objects that permit user interation with the application.
	 * Every P4A objects thats can be rendered should use WIDGET as base class.
	 * This class have all the basic methods to build complex widgets that must
	 * be P4A compatible.
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @package p4a
	 */
	class P4A_Widget extends P4A_Object
	{
		/**
		 * Object's value. Used for widget with data binding.
		 * @access private
		 * @var string
		 */
		var $value = NULL;

		/**
		 * Object's enabled status. If the widget is visible but not enable it won't be clickable.
		 * @access private
		 * @var boolean
		 */
		var $enabled = TRUE;

		/**
		 * Defines object visibility.
		 * @access private
		 * @var boolean
		 */
		var $visible = TRUE;

		/**
		 * Keeps the association between an action and its listener.
		 * @access private
		 * @var array
		 */
		var $map_actions = array();

		/**
		 * Keeps all the actions implemented by the widget.
		 * @access private
		 * @var array
		 */
		var $actions = array();

		/**
		 * Keeps the label associated with the widget.
		 * The label will be displayed on the left of the widget.
		 * @access public
		 * @var mixed
		 */
		var $label = NULL;

		/**
		 * Keeps all the HTML properties for the widget.
		 * @access private
		 * @var array
		 */
		var $properties = array();

		/**
		 * Keeps all the CSS properties for the widget.
		 * @access private
		 * @var array
		 */
		var $style = array();

		/**
		 * Defines if we are going to use a template for the widget.
		 * @access public
		 * @var boolean
		 */
		var $use_template = false;

		/**
		 * Defines the name of the widget.
		 * if you set it to 'menu' P4A will search for "menu/menu.tpl"
		 * in the "themes/CURRENT_THEME/widgets/" directory.
		 * @access public
		 * @var string
		 */
		var $template_name = NULL;

		/**
		 * variables used for templates
		 * @var array
		 * @access private
		 */
		var $_tpl_vars = array();

		/**
		 * Temporary variables (destroyed after rendering)
		 * @access private
		 * @var array
		 */
		var $_temp_vars = array();
		
		var $tooltip = "";

		/**
		 * Class constructor.
		 * Sets default properties and store the object in the application object stack.
		 * @param string	Widget identifier, when you add an object to another object (such as $p4a) you can access to it by $p4a->object_name.
		 * @param string	Prefix string for ID generation.
		 * @param string	Object ID identifies an object in the $p4a's object collection. You can set a static ID if you want that all clients uses the same ID (tipically for web sites).
		 * @access private
		 */
		function p4a_widget($name = NULL, $prefix = 'wdg', $id = NULL)
		{
			parent::p4a_object($name, $prefix, $id);
		}

		/**
		 * Sets the value of the widget.
		 * @param string	The value to be setted.
		 * @access public
		 * @see $value
		 */
		function setValue($value)
		{
			$this->value = $value;
		}

		/**
		 * Retuns the value of the widget.
		 * @return string
		 * @access public
		 * @see $value
		 */
		function getValue()
		{
			return $this->value;
		}

		/**
		 * Sets the widget enabled.
		 * @param boolean		Visibility flag
		 * @access public
		 * @see $enable
		 */
		function enable($enabled=TRUE)
		{
			$this->enabled = $enabled;
		}

		/**
		 * Sets the widget disabled.
		 * @access public
		 * @see $enabled
		 */
		function disable()
		{
			$this->enabled = FALSE;
		}

		/**
		 * Returns true if the widget is enabled.
		 * @access public
		 * @see $enable
		 */
		function isEnabled()
		{
			return $this->enabled;
		}

		/**
		 * Sets the widget visible.
		 * @param boolean		Visibility flag
		 * @access public
		 */
		function setVisible($visible=TRUE)
		{
			$this->visible = $visible;
		}

		/**
		 * Sets the widget invisible.
		 * @access public
		 */
		function setInvisible()
		{
			$this->visible = FALSE;
		}

		/**
		 * Returns true if the widget is visible.
		 * @return boolean
		 * @access public
		 */
		function isVisible()
		{
			return $this->visible;
		}

		/**
		 * Sets the label for the widget.
		 * In rendering phase it will be added with ':  '.
		 * @param string	The string to set as label.
		 * @access public
		 * @see $label
		 */
		function setLabel($label)
		{
			// Used for sheets group->sheets labels
			$this->actionHandler('set_label', $label);
			$result = preg_match("/(&(\w))/", $label, $a);
			if ($result) {
				$amp_key = $a[0];
				$key = $a[2];
				$label = str_replace($amp_key,"<span class=\"accesskey\">$key</span>", $label);
				$this->setProperty("accesskey",$key);
			}

			$this->label = $label;
		}

		/**
		 * Create from name a default label for the widget
		 * In rendering phase it will be added with ':  '.
		 * @param string	The string to set as label.
		 * @access public
		 * @see $label
		 */
		function setDefaultLabel()
		{
			$name = $this->getName();
			$label = __($name);

			if ($label == $name) {
				$this->setLabel(ucwords(str_replace('_', ' ', $this->getName())));
			} else {
				$this->setLabel($label);
			}
		}

		/**
		 * Returns the label for the widget.
		 * @return string
		 * @access public
		 */
		function getLabel()
		{
			return $this->label;
		}

		/**
		 * Sets an HTML property for the widget.
		 * @param string	The property's name.
		 * @param string	The property's value.
		 * @access public
		 */
		function setProperty($property, $value)
		{
			$this->properties[strtolower($property)] = $value;
		}

		/**
		 * Unsets an HTML property for the widget.
		 * @param string	The property's name.
		 * @access public
		 */
		function unsetProperty($property)
		{
			unset($this->properties[strtolower($property)]);
		}

		/**
		 * Returns the value of a property.
		 * @param string	The property's name.
		 * @return string
		 * @access public
		 */
		function getProperty($property)
		{
			$property = strtolower($property);
			if (array_key_exists($property, $this->properties)) {
				return $this->properties[$property];
			} else {
				return null;
			}
		}

		/**
		 * Sets a CSS property for the widget.
		 * @param string	The property's name.
		 * @param string	The property's value.
		 * @access public
		 */
		function setStyleProperty($property, $value)
		{
			$this->style[$property] = $value;
		}

		/**
		 * Unset a CSS property for the widget.
		 * @param string	The property's name.
		 * @access public
		 */
		function unsetStyleProperty($property)
		{
			unset($this->style[$property]);
		}

		function setAccessKey($key) {
			$this->setProperty("accesskey",$key);
		}

		function getAccessKey() {
			return $this->getProperty("accesskey");
		}

		/**
		 * Returns the value of a CSS property.
		 * @param string	The property's name.
		 * @return string
		 * @access public
		 */
		function getStyleProperty($property)
		{
			if( array_key_exists( $property, $this->style ) ) {
				return $this->style[$property];
			} else {
				return null;
			}
		}

		/**
		 * Sets the width for the widget.
		 * It's a wrapper for set_style_property().
		 * @param integer	The value to be used as width.
		 * @param string	The measure unit (px|pt|%) etc...
		 * @access public
		 * @see set_style_property()
		 */
		function setWidth($value = null)
		{
			if (is_numeric($value)) {
				$value = $value;
			}
			if ($value === null) {
				$this->unsetStyleProperty('width');
			} else {
				$this->setStyleProperty('width', $value);
			}
		}

		/**
		 * Returns the width for the widget.
		 * It's a wrapper for get_style_property().
		 * @access public
		 * @return string
		 * @see get_style_property()
		 */
		function getWidth()
		{
			return $this->getStyleProperty('width');
		}

		/**
		 * Sets the height for the widget.
		 * It's a wrapper for set_style_property().
		 * @param integer	The value to be used as height.
		 * @param string	The measure unit (px|pt|%) etc...
		 * @access public
		 * @see set_style_property()
		 */
		function setHeight($value = null, $unit = 'px')
		{
			if (is_numeric($value)) {
				$value = $value . $unit;
			}
			if ($value === null) {
				$this->unsetStyleProperty('height');
			} else {
				$this->setStyleProperty('height', $value);
			}
		}

		/**
		 * Returns the height for the widget.
		 * It's a wrapper for get_style_property().
		 * @access public
		 * @see get_style_property()
		 */
		function getHeight()
		{
			return $this->getStyleProperty('height');
		}

		/**
		 * Sets the background color for the widget.
		 * It's a wrapper for set_style_property().
		 * @param string	The value to be used as color.
		 * @access public
		 * @see set_style_property()
		 */
		function setBgcolor($value)
		{
			$this->setStyleProperty('background-color', $value) ;
		}

		/**
		 * Sets the background image for the widget.
		 * It's a wrapper for set_style_property().
		 * @param string	The url of the image.
		 * @access public
		 * @see set_style_property()
		 */
		function setBgimage($value)
		{
			$this->setStyleProperty('background-image', "url('" . $value . "')");
		}

		/**
		 * Sets the font weight for the widget
		 * It's a wrapper for set_style_property().
		 * @param string	The url of the image.
		 * @access public
		 * @see set_style_property()
		 */
		function setFontWeight($value)
		{
			$this->setStyleProperty('font-weight', $value);
		}

		/**
		 * Sets the font color for the widget
		 * It's a wrapper for set_style_property().
		 * @param string	The url of the image.
		 * @access public
		 * @see set_style_property()
		 */
		function setFontColor($value)
		{
			$this->setStyleProperty('color', $value);
		}

		/**
		 * Adds an action to the implemented actions stack for the widget.
		 * @access protected
		 * @param string	The action's name.
		 * @param string	Text for javascript confirmation.
		 * @param boolean	is an ajax action?
		 */
		function addAction($action, $confirmation_text = null, $ajax = false)
		{
			$action = strtolower($action);

			$this->actions[$action] = array();
			$this->actions[$action]['confirmation_text'] = $confirmation_text;
			$this->actions[$action]['ajax'] = $ajax;
		}

		/**
		 * Switches an action to ajax (is AJAX is enabled).
		 * If the action does not exists it will be created
		 * @access public
		 * @param string	The action's name.
		 */
		function useAjaxAction($action)
		{
			$p4a =& p4a::singleton();
			$action = strtolower($action);
			
			if (isset($this->actions[$action])) {
				$this->actions[$action]['ajax'] = $p4a->isAjaxEnabled();
			} else {
				$this->addAction($action, null, $p4a->isAjaxEnabled());
			}
		}

		/**
		 * Requires confirmation for an action.
		 * @access public
		 * @param string	The action.
		 * @param string	Text for confirmation.
		 */
		function requireConfirmation($action, $confirmation_text)
		{
			$action = strtolower($action);
			
			if (isset($this->actions[$action])) {
				$this->actions[$action]['confirmation_text'] = $confirmation_text;
			} else {
				$this->addAction($action, $confirmation_text);
			}
		}

		/**
		 * Removes confirmation for an action.
		 * @access public
		 * @param string	The action.
		 */
		function unrequireConfirmation($action)
		{
			$this->requireConfirmation($action, null);
		}

		/**
		 * Removes an action from the implemented actions stack for the widget.
		 *
		 * @param string	The action's name.
		 * @access public
		 */
		function dropAction($action)
		{
			$action = strtolower($action);
			
			if (isset($this->actions[$action])) {
				unset($this->actions[$action]);
			}
		}

		/**
		 * Composes a string containing all the HTML properties of the widget.
		 * Note: it will also contain the name and the value.
		 * @return string
		 * @access public
		 */
		function composeStringProperties()
		{
			$sReturn = '';
			foreach ($this->properties as $property_name=>$property_value) {
				$sReturn .= $property_name . '="' . $property_value . '" ' ;
			}

			$sReturn .= $this->composeStringStyle();
			return $sReturn;
		}

		/**
		 * Composes a string containing all the actions implemented by the widget.
		 * Note: it will also contain the name and the value.
		 * @param array		Parameters passed to the action handler.
		 * @return string
		 * @access public
		 */
		function composeStringActions($params = null, $check_enabled_state = true)
		{
  			$p4a =& P4A::singleton();

			$sParams = '';
  			$sActions = '';

  			if (is_string($params) or is_numeric($params)) {
  				$sParams .=  ", '{$params}'";
  			} elseif (is_array($params) and count($params)) {
  				$sParams = ', ';
  				foreach ($params as $param) {
  					$sParams .= "'{$param}', ";
  				}
  				$sParams = substr($sParams, 0, -2);
  			}

  			foreach ($this->actions as $action=>$action_data) {
				if ($check_enabled_state and !$this->isEnabled()) {
					return '';
				}

				$browser_action = $action;
				$return = 'false';
				$prefix = '';
				$suffix = '';

				if ($action == 'onreturnpress') {
					$browser_action = 'onkeypress';
					$return = 'true';
					$prefix .= 'if(isReturnPressed(event)){';
					$suffix .= '}';
				} elseif ($action == 'onkeypress'
					   or $action == 'onkeydown'
					   or $action == 'onkeyup') {
					$sParams .= ", getKeyPressed(event)";
				}

				if ($action_data['require_confirmation']) {
					$suffix .= '}';

					if ($action_data['confirmation_text'] === NULL) {
						$prefix .= 'if(confirm(\''. str_replace( '\'', '\\\'', $p4a->i18n->messages->get($action_data['confirmation_text_handler'])) .'\')){';
					} else {
						$prefix .= 'if(confirm(\''. str_replace( '\'', '\\\'', $action_data['confirmation_text'] ) .'\')){';
					}
				}

				if (isset($action_data['ajax']) and $action_data['ajax'] == 1) {
					$execute = 'executeAjaxEvent';
				} else {
					$execute = 'executeEvent';
				}

				if (isset($action_data['event'])) {
					$action_data_event = $action_data['event'];
				} else {
					$action_data_event = '';
				}

				$sActions .= $browser_action . '="' . $prefix . "{$execute}('" . $this->getId() . '\', \'' . $action_data_event . '\''. $sParams .');' . $suffix . ' return ' . $return . ';" ';
			}
			return $sActions;
		}

		/**
		 * Composes a string containing the CSS properties for the widget.
		 * @return string
		 * @access public
		 */
		function composeStringStyle()
		{
			$sStyle = '';
			foreach($this->style as $property=>$property_value) {
				$sStyle .= "$property:$property_value;";
			}

			if ($sStyle) {
				$sStyle = 'style="' . substr($sStyle, 0, -1) . '" ';
			}
			return $sStyle;
		}

		/**
		 * Composes a string contaning the CSS class property for the widget.
		 * @return string
		 * @access public
		 */
		function composeStringClassStyle()
		{
			$sClassStyle = '';
			if ($this->class_style) {
				$sClassStyle = 'class="' . $this->class_style . '" ';
			}
			return $sClassStyle;
		}

		/**
		 * Defines the template used by the widget.
		 * @param string	Template name
		 * @access public
		 * @see $template_name
		 */
		function useTemplate($template_name)
		{
			if ($template_name === false) {
				$this->use_template = false;
				$this->template_name = null;
			} else {
				$this->use_template = true;
				$this->template_name = $template_name;
			}
		}

		/**
		 * Adds this variable (name and value) to the template engine variables' stack.
		 * @param string	The variable name.
		 * @param string	The variable value.
		 */
		function display($var_name, $var_value)
		{
			$sDisplay = '';
			if (is_object($var_value)) {
				$sDisplay = $var_value->getAsString();
			} else {
				$sDisplay = $var_value;
			}

			if ($this->use_template) {
				$this->_tpl_vars[$var_name] = $sDisplay;
			} else {
				p4a_error("FETCH TEMPLATE IMPOSSIBLE. Call first use_template.");
			}
		}

		/**
		 * Empties the template engine variables' stack.
		 * @access public
		 */
		function clearTemplateVars()
		{
			$this->_tpl_vars = array();
		}

		/**
		 * Returns the HTML rendered template.
		 * @return string
		 * @access public
		 */
		function fetchTemplate()
		{
			if ($this->use_template) {
				$p4a =& p4a::singleton();

				if (strpos($this->template_name, '/') !== false) {
					list($_template_dir, $_template_file) = explode('/', $this->template_name);
				} else {
					$_template_dir = $this->template_name;
					$_template_file = $this->template_name;
				}

				foreach ($this->_tpl_vars as $k=>$v) {
					if (is_object($v)) {
						$$k = $v->getAsString();
					} else {
						$$k = $v;
					}
				}

				foreach ($this->_temp_vars as $k=>$v) {
					$$k = $v;
				}

				ob_start();
				require P4A_THEME_DIR . "/widgets/{$_template_dir}/{$_template_file}.tpl";
				$output = ob_get_contents();
				ob_end_clean();

				$this->clearTempVars();

				return $output;
			} else {
				p4a_error("ERROR: Unable to fetch template, first call \"use_template\".");
			}
		}

		/**
		 * Returns the HTML rendered widget.
		 * This method MUST be overridden by every
		 * widget that extends P4A_this class.
		 * @access public
		 */
		function getAsString()
		{
		}

		/**
		 * Prints the value returned by get_as_string().
		 * It Should never be used by "normal" p4a users.
		 * @access public
		 * @see get_as_string()
		 */
		function raise()
		{
			print $this->getAsString();
		}

		/**
		 * Wrapper used to add the handling of OnBlur action.
		 * @see action_handler()
		 */
		function onBlur($params = NULL)
		{
			return $this->actionHandler('onBlur', $params);
		}

		/**
		 * Wrapper used to add the handling of OnClick action.
		 * @see action_handler()
		 */
		function onClick($params = NULL)
		{
			return $this->actionHandler('onClick', $params);
		}

		/**
		 * Wrapper used to add the handling of OnChange action.
		 * @see action_handler()
		 */
		function onChange($params = NULL)
		{
			return $this->actionHandler('onChange', $params);
		}

		/**
		 * Wrapper used to add the handling of onDblClick action.
		 * @see action_handler()
		 */
		function onDblClick($params = NULL)
		{
			return $this->actionHandler('onDblClick', $params);
		}

		/**
		 * Wrapper used to add the handling of onFocus action.
		 * @see action_handler()
		 */
		function onFocus($params = NULL)
		{
			return $this->actionHandler('onFocus', $params);
		}

		/**
		 * Wrapper used to add the handling of onMouseDown action.
		 * @see action_handler()
		 */
		function onMouseDown($params = NULL)
		{
			return $this->actionHandler('onMouseDown', $params);
		}

		/**
		 * Wrapper used to add the handling of onMouseMove action.
		 * @see action_handler()
		 */
		function onMouseMove($params = NULL)
		{
			return $this->actionHandler('onMouseMove', $params);
		}

		/**
		 * Wrapper used to add the handling of onMouseOver action.
		 * @see action_handler()
		 */
		function onMouseOver($params = NULL)
		{
			return $this->actionHandler('onMouseOver', $params);
		}

		/**
		 * Wrapper used to add the handling of onMouseUp action.
		 * @see action_handler()
		 */
		function onMouseUp($params = NULL)
		{
			return $this->actionHandler('onMouseUp', $params);
		}

		/**
		 * Wrapper used to add the handling of OnKeyPress action.
		 * @see action_handler()
		 */
		function onKeyPress($params = NULL)
		{
			return $this->actionHandler('onKeyPress', $params);
		}

		/**
		 * Wrapper used to add the handling of OnKeyUp action.
		 * @see action_handler()
		 */
		function onKeyUp($params = NULL)
		{
			return $this->actionHandler('onKeyUp', $params);
		}

		/**
		 * Wrapper used to add the handling of OnKeyDown action.
		 * @see action_handler()
		 */
		function onKeyDown($params = NULL)
		{
			return $this->actionHandler('onKeyDown', $params);
		}

		/**
		 * Wrapper used to add the handling of onReturnPress action.
		 * The onReturnPress action is an onKeyPress with checking if
		 * the pressed key is return.
		 * @see action_handler()
		 */
		function onReturnPress($params = NULL)
		{
			return $this->actionHandler('onReturnPress', $params);
		}

		/**
		 * Wrapper used to add the handling of onSelect action.
		 * @see action_handler()
		 */
		function onSelect($params = NULL)
		{
			return $this->actionHandler('onSelect', $params);
		}

		/**
		 * Add a temporary variable
		 * @param string		The URI of file.
		 * @access public
		 */
		function addTempVar($name, $value)
		{
			$this->_temp_vars[$name] = $value;
		}

		/**
		 * Drop a temporary variable
		 * @param string		The URI of CSS.
		 * @access public
		 */
		function dropTempVar($name)
		{
			if(isset($this->_temp_vars[$name])){
				unset($this->_temp_vars[$name]);
			}
		}

		/**
		 * Clear temporary vars list
		 * @access public
		 */
		function clearTempVars()
		{
			$this->_temp_vars = array();
		}

		function redesign()
		{
			$p4a =& p4a::singleton();
			$p4a->redesign($this->getId());
		}
		
		function setTooltip($text)
		{
			$this->tooltip = $text;
		}
		
		function getTooltip()
		{
			return $this->tooltip;
		}
	}