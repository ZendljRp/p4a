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
	 * Base class for objects that permit user interation with the application. 
	 * Every P4A objects thats can be rendered should use WIDGET as base class.
	 * This class have all the basic methods to build complex widgets that must
	 * be P4A compatible.
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @package p4a
	 */
	class P4A_WIDGET extends P4A_OBJECT
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
		 * Class constructor.
		 * Sets default properties and store the object in the application object stack.
		 * @param string	Widget identifier, when you add an object to another object (such as $p4a) you can access to it by $p4a->object_name.
		 * @param string	Prefix string for ID generation.
		 * @param string	Object ID identifies an object in the $p4a's object collection. You can set a static ID if you want that all clients uses the same ID (tipically for web sites).
		 * @access private 
		 */
		function &p4a_widget($name = NULL, $prefix = 'wdg', $id = NULL)
		{
			parent::p4aObject($name, $prefix, $id);
			$this->setProperty('name', $this->getId());
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
		 * @access public
		 * @see $enable
		 */
		function enable()
		{
			$this->enabled = TRUE;
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
		 * @access public
		 */
		function setVisible()
		{
			$this->visible = TRUE;
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
			$this->setLabel( ucwords(str_replace('_', ' ', $this->name)) );
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
			$this->properties[$property] = $value;						
		}

		/**
		 * Unsets an HTML property for the widget.
		 * @param string	The property's name.
		 * @access public
		 */		
		function unsetProperty($property)
		{
			unset($this->properties[$property]);
		}

		/**
		 * Returns the value of a property.
		 * @param string	The property's name.
		 * @return string
		 * @access public
		 */		
		function getProperty($property)
		{
			if ( array_key_exists( $property, $this->properties ) ) {
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
			unset( $this->style[$property] );
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
				return NULL;
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
		function setWidth( $value = NULL, $unit = 'px' )
		{
			if( $value === NULL ) {
				$this->unsetStyleProperty( 'width' ) ;
			} else {
				$this->setStyleProperty( 'width', $value . $unit ) ;
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
			return $this->getStyleProperty( 'width' ) ;
		}

		/**
		 * Sets the height for the widget.
		 * It's a wrapper for set_style_property().
		 * @param integer	The value to be used as height.
		 * @param string	The measure unit (px|pt|%) etc...
		 * @access public
		 * @see set_style_property()
		 */		
		function setHeight( $value = NULL, $unit = 'px' )
		{
			if( $value === NULL ) {
				$this->unsetStyleProperty( 'height' ) ;
			} else {
				$this->setStyleProperty( 'height', $value . $unit ) ;
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
			return $this->getStyleProperty( 'height' ) ;
		}
		
		/**
		 * Sets the background color for the widget.
		 * It's a wrapper for set_style_property().
		 * @param string	The value to be used as color.
		 * @access public
		 * @see set_style_property()
		 */		
		function setBgcolor( $value )
		{
			$this->setStyleProperty( 'background-color', $value ) ;
		}
		
		/**
		 * Sets the background image for the widget.
		 * It's a wrapper for set_style_property().
		 * @param string	The url of the image.
		 * @access public
		 * @see set_style_property()
		 */	
		function setBgimage( $value )
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
		function setFontWeight( $value )
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
		function setFontColor( $value )
		{
			$this->setStyleProperty('color', $value);
		}		
		
		/**
		 * Adds an action to the implemented actions stack for the widget.
		 * @access public 
		 * @param string	The action's name.
		 * @param string	The JavaScript event that triggers.
		 * @param boolean	Action Requires confirmation?.
		 * @param string	Text for confirmation.
		 * @param string	i18n message id for confirmation.
		 */
		function addAction($action, $event = NULL, $require_confirmation = false, $confirmation_text = NULL, $confirmation_text_handler = 'confirm_general')
		{
			// If not specified, the event has the same name of the action 
			if (! $event)
			{
				$event = $action;
			}
			
			$tmp_action = array();
			$tmp_action['event'] = $event;
			$this->actions[$action] = $tmp_action;
			$this->actions[$action]['require_confirmation'] = $require_confirmation;
			$this->actions[$action]['confirmation_text'] = $confirmation_text;
			$this->actions[$action]['confirmation_text_handler'] = $confirmation_text_handler;
		}
		
		/**
		 * Requires confirmation for an action.
		 * @access public
		 * @param string	The action.
		 * @param string	Text for confirmation.
		 * @param string	i18n message id for confirmation.
		 */
		function requireConfirmation( $action, $confirmation_text = NULL, $confirmation_text_handler = 'confirm_general' )
		{
			$this->actions[$action]['require_confirmation'] = true;
			$this->actions[$action]['confirmation_text'] = $confirmation_text;
			$this->actions[$action]['confirmation_text_handler'] = $confirmation_text_handler;
		}
		
		/**
		 * Removes confirmation for an action.
		 * @access public
		 * @param string	The action.
		 */
		function unrequireConfirmation( $action )
		{
			$this->actions[$action]['require_confirmation'] = false;
		}
		
		/**
		 * Changes the event associated to an action.
		 * If no event is given, here we set event=action.
		 * 
		 * @param string	The action's name.
		 * @param string	The JavaScript event that triggers.
		 * @access public
		 */
		function changeEvent($action, $event = NULL)
		{
			// If not specified, the event has the same name of the action 
			if ($event === NULL)
			{
				$event = $action;
			}
			
			if (array_key_exists($action, $this->actions))
			{
				$this->actions[$action]['event'] = $event;
			}
		}
		
		/**
		 * Removes an action from the implemented actions stack for the widget.
		 * 
		 * @param string	The action's name.
		 * @access public
		 */
		function dropAction($action)
		{
			unset($this->actions[$action]);
		}
				
		/**
		 * Composes a string containing all the HTML properties of the widget.
		 * Note: it will also contain the name and the value.
		 * @return string
		 * @access public
		 */
		function composeStringProperties()
		{
			$sReturn = "";
			foreach($this->properties as $property_name=>$property_value)
			{
				$sReturn .= $property_name . '="' . htmlspecialchars($property_value) . '" ' ;	
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
		function composeStringActions($params = NULL)
		{
  			$p4a =& P4A::singleton();
			
			$sParams = '';
  			$sActions = '';
  			
  			if (is_string($params) or is_numeric($params)){
  				$sParams =  ", '" . $params . "'"; 			
  			}elseif(is_array($params) and count($params)){
  				$sParams = ", ";
  				foreach($params as $param){
  					$sParams .= "'" . $param . "', "; 
  				}
  				$sParams = substr($sParams, 0, -2);
  			}
  			
  			foreach ($this->actions as $action=>$action_data)
			{
				if( !$this->isEnabled() ) {
					return NULL;
				}
				
				$browser_action = $action;
				$return = 'false';
				$prefix = '';
				$suffix = '';
				
				if ($action == 'onReturnPress')
				{
					$browser_action = 'onKeyPress';
					$return = 'true';
					$prefix .= 'if(isReturnPressed(event)){';
					$suffix .= '}';
				}
				
				if ($action_data['require_confirmation'])
				{
					$suffix .= '}';
					
					if ($action_data['confirmation_text'] === NULL)
					{
						$prefix .= 'if(confirm(\''. str_replace( '\'', '\\\'', $p4a->i18n->messages->get($action_data['confirmation_text_handler'])) .'\')){';
					}
					else
					{
						$prefix .= 'if(confirm(\''. str_replace( '\'', '\\\'', $action_data['confirmation_text'] ) .'\')){';
					}
				}
				
				$sActions .= $browser_action . '="' . $prefix . 'executeEvent(\'' . $this->getId() . '\', \'' . $action_data['event'] . '\''. $sParams .');' . $suffix . ' return ' . $return . ';" '; 
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
			foreach($this->style as $property=>$property_value)
			{
				$sStyle .= "$property:$property_value;";
			}
			
			if ($sStyle)
			{
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
			if ($this->class_style)
			{
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
			$this->use_template = TRUE;
			$this->template_name = $template_name;

			$this->smarty = new Smarty();

			if (file_exists(P4A_SMARTY_WIDGET_TEMPLATES_DIR . '/' . $template_name)){
				$this->smarty->template_dir = P4A_SMARTY_WIDGET_TEMPLATES_DIR;
				$this->display('tpl_path', P4A_SMARTY_WIDGET_TEMPLATES_PATH . '/' . $template_name);
			}else{
				$this->smarty->template_dir = P4A_SMARTY_DEFAULT_WIDGET_TEMPLATES_DIR;
				$this->display('tpl_path', P4A_SMARTY_DEFAULT_WIDGET_TEMPLATES_PATH . '/' . $template_name);
			}
			$this->smarty->compile_dir = P4A_SMARTY_WIDGET_COMPILE_DIR;
			$this->smarty->left_delimiter = P4A_SMARTY_LEFT_DELIMITER;
			$this->smarty->right_delimiter = P4A_SMARTY_RIGHT_DELIMITER;
		}
		
		/**
		 * Unsets template informations for the widget.
		 * @access public
		 */
		function noUseTemplate()
		{
			$this->use_template = FALSE;
			$this->template_name = NULL;
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
			}else{
				$sDisplay = $var_value;
			}
			
			if ($this->use_template){
				$this->smarty->assign($var_name, $sDisplay);
			}else{
				error("FETCH TEMPLATE IMPOSSIBLE. Call first use_template.");				
			}
		}
		
		/**
		 * Empties the template engine variables' stack.
		 * @access public
		 */
		function clearDisplay()
		{
			$this->smarty_var = array();
			$this->smarty->clear_all_assign();
			unset($this->smarty);
			$this->useTemplate($this->template_name);
		}
        
		/**
		 * Returns the HTML rendered template.
		 * @return string
		 * @access public
		 */
		function fetchTemplate()
		{
			if ($this->use_template){
				$path_template = $this->template_name . '/' . $this->template_name . '.' . P4A_SMARTY_TEMPLATE_EXSTENSION;
				return $this->smarty->fetch($path_template);
			}else{
				error("FETCH TEMPLATE IMPOSSIBLE. First Call use_template.");
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
		 * Wrapper used to add the handling of OnKeyPress action.
		 * @see action_handler()
		 */
		function onKeyPress($params = NULL)
		{
			return $this->actionHandler('onKeyPress', $params);	
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
	}
?>