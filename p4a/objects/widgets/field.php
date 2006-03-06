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
	 * A fields is a GUI element that shows its value, and this value can be changed.
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @package p4a
	 */
	class P4A_Field extends P4A_Widget
	{
		/**
		 * Buttons collection.
		 * @var array
		 * @access private
		 */
		var $buttons = NULL;

		/**
		 * Data source for the field.
		 * @var data_source
		 * @access private
		 */
		var $data = NULL;

		/**
		 * Data source for the field.
		 * @var data_field
		 * @access private
		 */
		var $data_field = NULL;

		/**
		 * Will be used for future implementations.
		 * @var data_field
		 * @access private
		 */
		var $data_member = NULL;

		/**
		 * The data source member that contains the values for this field.
		 * @var string
		 * @access private
		 */
		var $data_value_field = NULL ;

		/**
		 * The data source member that contains the descriptions for this field.
		 * @var string
		 * @access private
		 */
		var $data_description_field	= NULL ;

		/**
		 * Field type.
		 * @var string
		 * @access private
		 * @see set_type()
		 */
		var $type = 'text' ;

		/**
		 * Max size in pixels for image thumbnail.
		 * @var integer
		 * @access private
		 */
		var $max_thumbnail_size = 100 ;

		/**
		 * Defines if a SELECT has "none selected" record.
		 * @var integer
		 * @access private
		 */
		var $allow_null = false ;

		/**
		 * Defines the message for "none selected" record for select.
		 * @var integer
		 * @access private
		 */
		var $null_message = NULL ;

		/**
		 * Field align.
		 * @var string
		 * @access private
		 */
		var $align = 'left';

		/**
		 * Tells if the fields content is formatted or not.
		 * @var string
		 * @access private
		 */
		var $formatted = true;

		/**
		 * The formatter class name for the data field.
		 * @var string
		 * @access private
		 */
		var $formatter_name = NULL;

		/**
		 * The format name for the data field.
		 * @var string
		 * @access private
		 */
		var $format_name = NULL;

		/**
		 * Path under P4A_UPLOADS_PATH where uploads happens.
		 * @var string
		 * @access private
		 */
		var $upload_subpath = NULL;

		/**
		 * Type of encryption to use for password fields
		 * @var		string
		 * @access	private
		 */
		var $encryption_type = 'md5';

		/**
		 * Is upload enabled on rich text area?
		 * This is disabled by default for security reasons, enable it only after a well done permission check.
		 * @var		boolean
		 * @access	private
		 */
		var $upload = false;

		/**
		 * Class constructor.
		 * Istances the widget, sets name and initializes its value.
		 * @param string				Mnemonic identifier for the object.
		 * @param string				If it's false the widget doesn't instance a default data_field. You must to set a data_field for the widget before call get_value, get_new_value or getAsstring.
		 * @access private
		 */
		function P4A_Field($name, $add_default_data_field = TRUE)
		{
			parent::P4A_Widget($name, 'fld');
			$this->setProperty('name', $this->getId());

			$this->build("p4a_collection", "buttons");
			$this->setType('text');

			//Data field
			if ($add_default_data_field){
				$this->build("P4A_Data_Field", "data_field");
			}

			//Label
 			$this->build("P4A_Label", "label");
			$this->label->setProperty("for",$this->getId());
			$this->setDefaultLabel();
		}

		/**
		 * Sets a data field as current data_field.
		 * This changes default text alignment for
		 * integer, decimal, float, date, time to right.
		 * @access public
		 * @param DATA_FIELD		The new data field
		 */
		function setDataField(&$data_field)
		{
			unset($this->data_field);

			if (! $data_field->isReadOnly())
			{
				switch($data_field->getType()) {
					case 'date':
						$this->setType('date');
						break;
					case 'boolean':
						$this->setType('checkbox');
						break;
				}
			}else{
				$this->setType('label');
			}
			$this->data_field =& $data_field;
			$this->setDefaultVisualizationProperties();
		}

		/**
		 * Sets the default visualization property for the field.
		 * @access private
		 */
		function setDefaultVisualizationProperties()
		{
			$visualization_data_type = NULL;
			$source_description_field = $this->getSourceDescriptionField();

			if (!is_null( $source_description_field ) and is_object($this->data)) {
				$visualization_data_type = $this->data->fields->$source_description_field->getType();
			} elseif (!is_null($this->data_field)) {
				$visualization_data_type = $this->data_field->getType();
			}

			switch( $visualization_data_type ) {
				case 'integer':
				case 'decimal':
				case 'float':
				case 'date':
				case 'time':
					$this->setStyleProperty('text-align', 'right');
					break;
				default:
					$this->setStyleProperty('text-align', 'left');
					break;
			}
		}

		/**
		 * Sets the value for the field.
		 * @param mixed				Value.
		 * @access public
		 */
		function setValue($value)
		{
			$this->data_field->setValue($value);
		}

		/**
		 * Returns the value for the field.
		 * @return mixed
		 * @access public
		 */
		function getValue()
		{
			return $this->data_field->getValue();
		}

		/**
		 * Return the field's value always as string.
		 * If the value is an array, it will be encoded in {value1, value2}
		 * @return string
		 * @access public
		 */
        function getStringValue()
        {
            $value = $this->data_field->getValue();
            if (is_array($value)){
                $sReturn = implode(', ', $value);
                $sReturn = '{' . $sReturn . '}';
                return $sReturn;
            }else {
                return $value;
            }
        }

		/**
		 * Examines the value passed by the web form and set the new value.
		 * @param mixed		The new value for the field.
		 * @access public
		 */
		function setNewValue($new_value)
		{
			$set = true ;

			if ($new_value === NULL) {
				$new_value = NULL;
			} elseif ($this->isFormattable() and $this->isFormatted()) {
				$new_value = $this->unformat($new_value);
			} elseif (($this->type == 'password')
			and ($new_value != P4A_PASSWORD_OBFUSCATOR)) {
				switch ($this->getEncryptionType()) {
					case 'md5':
						if (!empty($new_value)) {
							$new_value = md5($new_value);
						}
						break;
					case 'none':
						break;
					default:
						P4A_Error('unknown encryption type:' . $this->getEncryptionType());
				}
			} elseif (($this->type == 'password')
			and ($new_value == P4A_PASSWORD_OBFUSCATOR)) {
				$set = false;
			}

			if ($set) {
				$this->data_field->setNewValue($new_value);
			}
		}

		/**
		 * Returns the "new_value" for the field (with locale formatting).
		 * @param integer		If the value is an array that we can return only one element.
		 * @return string
		 * @access public
		 */
		function getNewValue($index = NULL)
		{
			$new_value = $this->data_field->getNewValue();

			if ($new_value === NULL) {
				// $new_value = NULL;
			} elseif ($index === NULL) {
				if ($this->isFormattable() and $this->isFormatted()) {
					$new_value = $this->format($new_value);
				}
            } elseif (is_array($new_value)) {
                $new_value = $new_value[$index];
            } elseif (substr($new_value, 0, 1) == '{' and substr($new_value, -1) == '}') {
                $tmp_value = substr($new_value, 1, -1);
                $tmp_value = explode("," , $tmp_value);
                $new_value = $tmp_value[$index];
            }

            return $new_value;
		}
		
		/**
		 * Returns the "new_value" for the field (without locale formatting).
		 * @param integer		If the value is an array that we can return only one element.
		 * @return string
		 * @access public
		 */
		function getUnformattedNewValue($index = NULL)
		{
			$new_value = $this->data_field->getNewValue();

			if ($new_value === NULL) {
				// $new_value = NULL;
			} elseif ($index === NULL) {
				// $new_value = $new_value;
            } elseif (is_array($new_value)) {
                $new_value = $new_value[$index];
            } elseif (substr($new_value, 0, 1) == '{' and substr($new_value, -1) == '}') {
                $tmp_value = substr($new_value, 1, -1);
                $tmp_value = explode("," , $tmp_value);
                $new_value = $tmp_value[$index];
            }

            return $new_value;
		}

		/**
		 * Sets the field's type.
		 * @param strings		The type (text|password|textarea|rich_textarea|hidden|label|select|radio|checkbox|multiselect|multicheckbox).
		 * @access public
		 */
		function setType($type)
		{
			$this->type = $type;

			switch($type) {
			case 'label':
				$label_width = $this->label->getWidth();
				if (!empty($label_width)) {
					$this->setStyleProperty('margin-left', $label_width+20 . "px") ;
				}
				break;
			case 'textarea':
				$this->setWidth(500);
				$this->setHeight(200);
				break;
			case 'rich_textarea':
				$this->setWidth(500);
				$this->setHeight(300);
				break;
			}
		}

		/**
		 * Set type of encryption to use for password fields
		 * (md5|none)
		 *
		 * @access	public
		 */
		function setEncryptionType($type) {
			switch ($type) {
				case 'md5':
				case 'none':
					$this->encryption_type = $type;
					break;
				default:
					P4A_Error('unknown encryption type:' . $type);
			}
		}

		/**
		 * If we use fields like combo box we have to set a data source.
		 * By default we'll take the data source primary key as value field
		 * and the first fiels (not pk) as description.
		 * @param data_source		The data source.
		 * @access public
		 */
		function setSource(&$data_source)
		{
			unset( $this->data ) ;
			$this->data =& $data_source;

			$pk = $this->data->getPk();

			if (is_string($pk)) {
				if ($this->getSourceValueField() === NULL) {
					$this->setSourceValueField($pk);
				}

				if ($this->getSourceDescriptionField() === NULL)
				{
					$num_fields = $this->data->fields->getNumItems();
					$fields = $this->data->fields->getNames();
					$pk = $this->getSourceValueField();

					if ($num_fields == 1) {
						$description_field = $pk;
					} else {
						foreach ($fields as $field) {
							if ($field != $pk) {
								$description_field = $field;
								break;
							}
						}
					}

					$this->setSourceDescriptionField($description_field);
				}
			} else {
				P4A_Error("ONLY ONE PK IN THIS CASE");
			}

			$this->setDefaultVisualizationProperties();
		}

		/**
		 * Sets what data source member is the keeper of the field's value.
		 * @param string		The name of the data source member.
		 * @access public
		 */
		function setSourceValueField( $name )
		{
			// No controls if $name exists...
			// too many controls may be too performance expensive.
			$this->data_value_field = $name ;
		}

		/**
		 * Sets what data source member is the keeper of the field's description.
		 * @param string		The name of the data source member.
		 * @access public
		 */
		function setSourceDescriptionField( $name )
		{
			// No controls if $name exists...
			// too many controls may be too performance expensive
			$this->data_description_field = $name ;
			$this->setDefaultVisualizationProperties();
		}

		/**
		 * Returns the name of the data source member that keeps the field's value.
		 * @return string
		 * @access public
		 */
		function getSourceValueField()
		{
			return $this->data_value_field ;
		}

		/**
		 * Returns the name of the data source member that keeps the field's description.
		 * @return string
		 * @access public
		 */
		function getSourceDescriptionField()
		{
			return $this->data_description_field ;
		}

		/**
		 * Returns the field's type.
		 * @return string
		 * @access public
		 */
		function getType()
		{
			return $this->type;
		}

		/**
		 * Returns the encryption type (for password fields)
		 *
		 * @return	string
		 * @access	public
		 */
		function getEncryptionType() {
			return $this->encryption_type;
		}

		/**
		 * Returns true if the field is text or textarea.
		 * @access public
		 * @return boolean
		 */
		function isFormattable()
		{
			switch ($this->type) {
				case 'label':
				case 'text':
				case 'textarea':
				case 'date':
					return true;
			}

			return false;
		}

		/**
		 * Returns true if a formatting format for the field has been set.
		 * @access public
		 * @return boolean
		 */
		function isFormatted()
		{
			return $this->formatted;
		}

		/**
		 * Sets the field as formatted.
		 * @access public
		 */
		function setFormatted( $value = true )
		{
			$this->formatted = $value;
		}

		/**
		 * Sets the field as not formatted.
		 * @access public
		 */
		function unsetFormatted()
		{
			$this->formatted = false;
		}

		/**
		 * Sets the field formatter and format.
		 * This also turns formatting on.<br>
		 * Eg: set_format('numbers', 'decimal')
		 * @access public
		 * @param string	The formatter name.
		 * @param string	The format name.
		 */
		function setFormat( $formatter_name, $format_name )
		{
			$this->formatter_name = $formatter_name;
			$this->format_name = $format_name;
			$this->setFormatted();
		}

		/**
		 * Removes formatting options and turns formatting off.
		 * @access public
		 */
		function unsetFormat()
		{
			$this->formatter_name = NULL;
			$this->format_name = NULL;
			$this->unsetFormatted();
		}

		/**
		 * Format the given value using the current formatting options.
		 * Empty values are not formatted.<br>
		 * If formatting is turned of it does nothing.
		 * @access private
		 * @param string	The value to be formatted.
		 * @return string
		 */
		function format( $value )
		{
			$p4a =& P4A::singleton();
			if (strlen($value) > 0) {
				if (($this->formatter_name !== NULL) and ($this->format_name !== NULL)) {
					$value = $p4a->i18n->{$this->formatter_name}->format($value, $p4a->i18n->{$this->formatter_name}->getFormat($this->format_name));
				} else {
					$value = $p4a->i18n->autoFormat($value, $this->data_field->getType());
				}
			}

			return $value;
		}

		/**
		 * Takes the formatted passed value and takes it back to its unformatted form.
		 * @access private
		 * @param string	The formatted value.
		 * @return string
		 * @see P4A_Number::unformat()
		 * @see P4A_Date::unformat()
		 */
		function unformat( $value )
		{
			$p4a =& P4A::singleton();
			if( strlen( $value ) > 0 )
			{
				if( ( $this->formatter_name !== NULL ) and ( $this->format_name !== NULL ) )
				{
					$value = $p4a->i18n->{$this->formatter_name}->unformat( $value, $p4a->i18n->{$this->formatter_name}->getFormat( $this->format_name ) );
				}
				else
				{
					$value = $p4a->i18n->autoUnformat( $value, $this->data_field->getType() );
				}
			}

			return $value;
		}

		/**
		 * Resets the "new_value".
		 * @access public
		 */
        function cleanNewValue()
        {
			$this->setNewValue(NULL);
        }

		/**
		 * Returns the HTML rendered field.
		 * @return string
		 * @access public
		 */
		function getAsString()
		{
			if (! $this->isVisible()) {
				return NULL;
			}

			$type   = $this->type;
			$suffix = '';

			if ($type == 'rich_textarea') {
				$type = 'textarea';
				$suffix = $this->getAsRichTextarea();
			}

			$new_method = 'getAs' . $type;
			$string = $this->$new_method();
			return $string . $suffix ;
		}

		/**
		 * Returns the HTML rendered field as '<input type="text"'.
		 * @return string
		 * @access public
		 */
		function getAsText()
		{
			$header 		= '<input type="text" class="border_color1 font_normal" ';
			$close_header 	= '/>';

			if( !$this->isEnabled() ) {
				$header .= 'disabled="disabled" ';
			}

			$sReturn = $this->composeLabel() . $header . $this->composeStringProperties() . $this->composeStringValue() . $this->composeStringActions() . $close_header;
			return $sReturn;
		}

		function getAsDate()
		{
			$p4a =& P4A::singleton();

			if ($this->isEnabled()) {
				$enabled = "";
			} else {
				$enabled = " disabled='disabled' ";
			}
			
			$id = $this->getID();
			$languale = $p4a->i18n->getLanguage();
			$date_format = $p4a->i18n->datetime->getFormat('date_default');
			$template_path = P4A_THEME_PATH . "/widgets/date_calendar";

			$this->setProperty('id', $id);

			$header 	   = "<input type='text' id='" . $this->getID() . "' class='border_color1 font_normal' $enabled";
			$close_header  = "/>";

			if (!$p4a->isHandheld()) {
				$close_header .= "<input type='button' value='...' id='" . $this->getID() . "button' class='border_box font4 no_print' $enabled onclick=\"return showCalendar('$id', '$date_format', false, true);\"/>";
			}

			$sReturn = $this->composeLabel() . $header . $this->composeStringProperties() . $this->composeStringValue() . $this->composeStringActions() . $close_header;
			
			if (!$p4a->isHandheld()) {
				$p4a->active_mask->addTempCSS("$template_path/calendar.css");
				$p4a->active_mask->addTempJavascript("$template_path/calendar_stripped.js");
				$p4a->active_mask->addTempJavascript("$template_path/lang/calendar-en.js");
				$p4a->active_mask->addTempJavascript("$template_path/p4a.js");
			}

			return $sReturn;
		}

		/**
		 * Returns the HTML rendered field as '<input type="password"'.
		 * We use P4A_PASSWORD_OBFUSCATOR for password value so the old password isn't sent over the net.
		 * @return string
		 * @access public
		 */
		function getAsPassword()
		{
			$header 		= '<input type="password" class="border_color1 font_normal" ';
			$close_header 	= '/>';

			if( !$this->isEnabled() ) {
				$header .= 'disabled="disabled" ';
			}

			if (strlen($this->getNewValue()) > 0) {
				$header .= ' value="' . P4A_PASSWORD_OBFUSCATOR . '" ';
			}

			$sReturn = $this->composeLabel() . $header . $this->composeStringProperties() . $this->composeStringValue() . $this->composeStringActions() . $close_header;
			return $sReturn;
		}

		/**
		 * Returns the HTML rendered field as '<input type="textarea"'.
		 * @return string
		 * @access public
		 */
		function getAsTextarea()
		{
			$cols = floor($this->getWidth() / 6) - 4;
			$rows = floor($this->getHeight() / 13);
			$header = "<textarea class='border_color1 font_normal' cols='$cols' rows='$rows' ";
			$close_header = '>';
			$footer	= '</textarea>';

			if( !$this->isEnabled() ) {
				$header .= 'disabled="disabled" ';
			}

			$sReturn  = $this->composeLabel() . "<div class='br'></div>" . $header . $this->composeStringProperties() . $this->composeStringActions() . $close_header;
			$sReturn .= $this->composeStringValue();
			$sReturn .= $footer;
			return $sReturn;
		}

		/**
		 * Returns the HTML rendered field as '<input type="textarea"' with rich text editing features.
		 * @return string
		 * @access public
		 */
		function getAsRichTextarea()
		{
			$p4a =& P4A::singleton();
			$p4a->active_mask->addTempJavascript(P4A_THEME_PATH . "/widgets/rich_textarea/tiny_mce.js");
			$this->useTemplate('rich_textarea');
			
			$this->addTempVar("language", $p4a->i18n->getLanguage());
			$this->addTempVar("upload_path", P4A_UPLOADS_PATH . "/" . $this->getUploadSubpath());
			$this->addTempVar("upload", $this->upload);
			
			return $this->fetchTemplate();
		}

		/**
		 * Returns the HTML rendered field as '<input type="hidden"'.
		 * @return string
		 * @access public
		 */
		function getAsHidden()
		{
			$header 		= '<input type="hidden" ';
			$close_header 	= '/>';

			$sReturn = $header . $this->composeStringProperties() . $this->composeStringValue() . $this->composeStringActions() .  $close_header;
			return $sReturn;
		}

		/**
		 * Returns the HTML rendered field as '<div>$value</div>'.
		 * @return string
		 * @access public
		 */
		function getAsLabel()
		{
            $header         = '<div class="field_as_label" ';
            $close_header   = '>';
            $footer         = '</div>';
            $value			= '';

            if ($this->data === NULL) {
				$value = nl2br($this->composeStringValue());
            } else {
				$external_data		= $this->data->getAll() ;
				$value_field		= $this->getSourceValueField() ;
				$description_field	= $this->getSourceDescriptionField() ;

    			foreach ($external_data as $key=>$current) {
    				if ($current[ $value_field ] == $this->getNewValue()) {
    					$value = $current[ $description_field ] ;
    				}
    			}

    			if (empty($value)) {
    				$value = $this->null_message;
    			}
            }
            return $this->composeLabel() . $header . $this->composeStringProperties() . $this->composeStringActions() . $close_header . $value . $footer ;
		}

		/**
		 * Returns the HTML rendered field as combo box.
		 * @return string
		 * @access public
		 */
		function getAsSelect()
		{
			$p4a =& P4A::singleton();

			$header 			= '<select class="border_box font_normal" ';
			$close_header 		= '>';
			$footer				= '</select>';
			$header			   .= $this->composeStringActions() . $this->composeStringProperties();

			if( !$this->isEnabled() ) {
				$header .= 'disabled="disabled" ';
			}

			$header			   .= $close_header;
			$external_data		= $this->data->getAll() ;
			$value_field		= $this->getSourceValueField() ;
			$description_field	= $this->getSourceDescriptionField() ;
			$new_value			= $this->getNewValue() ;

			if ($this->isNullAllowed()) {
				if( $this->null_message === NULL ) {
					$message = $p4a->i18n->messages->get('none_selected');
				} else {
					$message = $this->null_message;
				}

				$header .= "<option value=''>" . $message . "</option>";
			}

			foreach ($external_data as $key=>$current) {
				if ($current[ $value_field ] == $new_value){
					$selected = "selected='selected'";
				} else {
					$selected = "";
				}

				$sContent  = "<option $selected value='" . htmlspecialchars($current[ $value_field ]) ."'>";
				$sContent .= htmlspecialchars($p4a->i18n->autoFormat($current[ $description_field ], $this->data->fields->$description_field->getType()));
				$sContent .= "</option>";

				$header .= $sContent;
			}

			return $this->composeLabel() . $header . $footer ;
		}

		function getAsMultiselect()
		{
			$p4a =& P4A::singleton();

			$properties = $this->composeStringProperties();
			$actions = $this->composeStringActions();

			$sReturn  = "<input type='hidden' name='".$this->getID()."' value='' />";
			$sReturn .= "<select class='border_box font_normal' multiple='multiple' " . $this->composeStringStyle() . " ";
			foreach($this->properties as $property_name=>$property_value){
				if ($property_name == "name") {
					$property_value .= '[]';
				}
				$sReturn .= $property_name . '="' . $property_value . '" ' ;
			}
			$sReturn .= "$actions>";

			$external_data		= $this->data->getAll();
			$value_field		= $this->getSourceValueField();
			$description_field	= $this->getSourceDescriptionField();
			$new_value			= $this->getNewValue();

			foreach ($external_data as $key=>$current)
			{
				if (!$new_value) {
					$new_value = array();
				}
				if (in_array($current[$value_field],$new_value)) {
					$selected = "selected='selected'";
				} else {
					$selected = "";
				}

				$sReturn .= "<option $selected value='" . htmlspecialchars($current[$value_field]) ."'>";
				$sReturn .= htmlspecialchars($p4a->i18n->autoFormat($current[ $description_field ], $this->data->fields->$description_field->getType()));
				$sReturn .= "</option>";

			}
			$sReturn .= "</select>";

			return $this->composeLabel() . $sReturn;
		}

		function getAsMulticheckbox()
		{
			$p4a =& P4A::singleton();

			$properties = $this->composeStringProperties();
			$actions = $this->composeStringActions();
			$id = $this->getId();

			$sReturn  = "<div class='font_normal' style='float:left;text-align:left;'>";
			$sReturn .= "<input type='hidden' id='$id' name='$id' value='' />";

			$external_data		= $this->data->getAll();
			$value_field		= $this->getSourceValueField();
			$description_field	= $this->getSourceDescriptionField();
			$new_value			= $this->getNewValue();
			
			$i = 0;
			foreach ($external_data as $key=>$current) {
				if (!$new_value) {
					$new_value = array();
				}
				
				if (in_array($current[$value_field], $new_value)) {
					$checked = "checked='checked'";
				} else {
					$checked = "";
				}
				$sReturn .= "<div><input type='checkbox' class='border_none' id='{$id}_{$i}' name='{$id}[]' value='{$current[$value_field]}' $checked /><label for='{$id}_{$i}'>{$current[$description_field]}</label></div>\n";
				$i++;
			}
			
			$sReturn .= "</div>";
			return $this->composeLabel() . $sReturn;
		}

		/**
		 * Used ony for select, sets the select to allow a "none selected" record.
		 * @param string		The message for "none selected"
		 * @access public
		 */
		function allowNull( $message = NULL )
		{
			$this->allow_null = true ;
			$this->null_message = $message ;
		}

		/**
		 * Used ony for select, sets the select to do not allow a "none selected" record.
		 * @access public
		 */
		function noAllowNull()
		{
			$this->allow_null = false ;
		}

		/**
		 * Used ony for select, returns if the select allows a "none selected" record.
		 * @return boolean
		 * @access public
		 */
		function isNullAllowed()
		{
			return $this->allow_null ;
		}

		/**
		 * Returns the HTML rendered field as radio buttons group.
		 * @return string
		 * @access public
		 */
		function getAsRadio()
		{
			$p4a =& P4A::singleton();

			$external_data		= $this->data->getAll() ;
			$value_field		= $this->getSourceValueField() ;
			$description_field	= $this->getSourceDescriptionField() ;
			$new_value			= $this->getNewValue();

			$enabled = '';
			if (!$this->isEnabled()) {
				$enabled = 'disabled="disabled" ';
			}

			if ($this->isNullAllowed()) {
				if ($this->null_message === NULL) {
					$message = $p4a->i18n->messages->get('none_selected');
				} else {
					$message = $this->null_message;
				}

				array_unshift($external_data, array($value_field=>'', $description_field=>$message));
			}

			foreach ($external_data as $key=>$current) {
				if ($current[$value_field] == $new_value) {
					$checked = "checked='checked'";
				} else {
					$checked = "";
				}

				$sContent .= "<div><input $enabled class='radio' name='" . $this->getID() . "' type='radio' " . $this->composeStringActions() . " $checked value='" . htmlspecialchars($current[$value_field]) ."'/>";
				$sContent .= $p4a->i18n->autoFormat($current[$description_field], $this->data->structure[$description_field]['type']);
				$sContent .= '</div>';
			}

			$return = $this->composeLabel() . "<div class='font_normal' style='float:left;text-align:left;'>$sContent</div>";
			return $return;
		}

		/**
		 * Returns the HTML rendered field as checkbox.
		 * @return string
		 * @access public
		 */
		function getAsCheckbox()
		{
			// PostgreSQL uses "t" and "f" to return boolen values
			// For all the others we assume "1" or "0"
			if ($this->getNewValue() == 't' or $this->getNewValue() == '1') {
				$checked = "checked='checked'" ;
			} else {
				$checked = '' ;
			}

			$header = "<input type='hidden' name='" . $this->getId() . "' value='0' /><input type='checkbox' class='border_none' value='1' $checked ";
			$close_header = "/>";

			if( !$this->isEnabled() ) {
				$header .= 'disabled="disabled" ';
			}

			$header .= $this->composeStringActions() . $this->composeStringProperties() . $close_header;
			return $this->composeLabel() . $header ;
		}

		/**
		 * Returns the HTML rendered field as file upload.
		 * @return string
		 * @access public
		 */
		function getAsFile()
		{
			$p4a =& P4A::singleton();

			if ($this->getNewValue() === NULL) {
				$sReturn = "<div style='float:left'><input type='file' onchange='executeEvent(\"" . $this->getID() . "\", \"onchange\");' class='border_box font_normal clickable' ";

				if (!$this->isEnabled()) {
					$sReturn .= 'disabled="disabled" ';
				}

				$sReturn .= $this->composeStringActions() . $this->composeStringProperties() . ' /></div>';
			} else {
				if (!isset($this->buttons->button_file_delete)) {
					$button_file_delete =& $this->buttons->build("p4a_button", "button_file_delete");
					$button_file_delete->setValue($p4a->i18n->messages->get('filedelete'));
					$this->intercept($button_file_delete, 'onClick', 'fileDeleteOnClick');
					
					$button_file_download =& $this->buttons->build("p4a_button", "button_file_download");
					$button_file_download->setValue($p4a->i18n->messages->get('filedownload'));
					$this->intercept($button_file_download, 'onClick', 'fileDownloadOnClick');
				}

				if ($this->isEnabled()) {
					$this->buttons->button_file_delete->enable();
				} else {
					$this->buttons->button_file_delete->disable();
				}

				$src = P4A_UPLOADS_URL . $this->getNewValue(1);

				$sReturn  = '<table class="border_box">';
				$sReturn .= '<tr><td align="left">' . $p4a->i18n->messages->get('filename') . ':&nbsp;&nbsp;</td><td align="left"><a target="_blank" href="' . $src . '">' . $this->getNewValue(0) . '</a></td></tr>';
				$sReturn .= '<tr><th align="left">' . $p4a->i18n->messages->get('filesize') . ':&nbsp;&nbsp;</th><td align="left">' . $p4a->i18n->autoFormat($this->getNewValue(2)/1024, "decimal") . ' KB</td></tr>';
				$sReturn .= '<tr><td align="left">' . $p4a->i18n->messages->get('filetype') . ':&nbsp;&nbsp;</td><td align="left">' . $this->getNewValue(3) . '</td></tr>';
				$sReturn .= '<tr><td colspan="2" align="center">' . $this->buttons->button_file_download->getAsString() . ' '  . $this->buttons->button_file_delete->getAsString() . '</td></tr>';
				$sReturn .= '</table>';
			}

			return $this->composeLabel() . $sReturn;
		}

		/**
		 * Action handler for file deletetion.
		 * @access public
		 */
		function fileDeleteOnClick()
		{
			$this->setNewValue(null);
		}
		
		/**
		 * Action handler for file preview (only images).
		 * @access public
		 */
		function filePreviewOnClick()
		{
			$p4a =& p4a::singleton();
			$p4a->openMask("P4A_Mask_Image_Preview");
			$p4a->active_mask->image->setValue(P4A_UPLOADS_URL . $this->getNewValue(1));
			$p4a->active_mask->setTitle($this->getNewValue(0));
		}
		
		/**
		 * Action handler for file download.
		 * @access public
		 */
		function fileDownloadOnClick()
		{
			$name = $this->getNewValue(0);
			$src = P4A_UPLOADS_DIR . $this->getNewValue(1);
			$size = $this->getNewValue(2);
			
			header("Cache-control: private");
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=\"$name\"");
			header("Content-Length: $size");

			echo readfile($src, "r");
			die();
		}

		/**
		 * Sets the subpath of P4A_UPLOADS_PATH where the upload will happen.
		 * @access public
		 * @param string	The subdir (can be "test", "test/", "test/test", "test/test/test/")
		 */
		function setUploadSubpath( $subpath = NULL )
		{
			$this->upload_subpath = $subpath;
		}

		/**
		 * Removes the subpath for upload.
		 * @access public
		 */
		function unsetUploadSubpath()
		{
			$this->upload_subpath = NULL;
		}

		/**
		 * Return the subpath for upload..
		 * @access public
		 */
		function getUploadSubpath()
		{
			return $this->upload_subpath;
		}

		/**
		 * Returns the HTML rendered field as image upload.
		 * @return string
		 * @access public
		 */
		function getAsImage()
		{
			$p4a =& P4A::singleton();
			if ($this->getNewValue() === NULL) {
				$sReturn = "<div style='float:left'><input onchange='executeEvent(\"" . $this->getID() . "\", \"onchange\");' type='file' class='border_box font_normal clickable' ";

				if( !$this->isEnabled() ) {
					$sReturn .= 'disabled="disabled" ';
				}

				$sReturn .= $this->composeStringActions() . $this->composeStringProperties() . ' /></div>';
			} else {
				$mime_type = explode( '/', $this->getNewValue(3) );
				$mime_type = $mime_type[0];

				if ($mime_type != 'image') {
					return $this->getAsFile();
				}

				if (!isset($this->buttons->button_file_delete)) {
					$button_file_delete =& $this->buttons->build("p4a_button", "button_file_delete");
					$button_file_preview =& $this->buttons->build("p4a_button", "button_file_preview");
					$button_file_download =& $this->buttons->build("p4a_button", "button_file_download");

					$button_file_delete->setValue($p4a->i18n->messages->get('filedelete'));
					$button_file_preview->setValue($p4a->i18n->messages->get('filepreview'));
					$button_file_download->setValue($p4a->i18n->messages->get('filedownload'));
					
					$this->intercept($button_file_delete, 'onClick', 'fileDeleteOnClick');
					$this->intercept($button_file_preview, 'onClick', 'filePreviewOnClick');
					$this->intercept($button_file_download, 'onClick', 'fileDownloadOnClick');
				}

				if ($this->isEnabled()) {
					$this->buttons->button_file_delete->enable();
				} else {
					$this->buttons->button_file_delete->disable();
				}

				$src = P4A_UPLOADS_URL . $this->getNewValue(1);

				$width = $this->getNewValue(4);
				$str_width = '';
				$height = $this->getNewValue(5);
				$str_height = '';

				if ($width > $height) {
					if ($this->max_thumbnail_size !== NULL and $width > $this->max_thumbnail_size) {
						$width = $this->max_thumbnail_size ;
						$str_width = "width=\"$width\"" ;
					}
				} else {
					if ($this->max_thumbnail_size !== NULL and $height > $this->max_thumbnail_size) {
						$height = $this->max_thumbnail_size ;
						$str_height = "height=\"$height\"" ;
					}
				}

				$sReturn  = '<table class="border_box" id="' . $this->getId() . '">' ;
				if (P4A_GD) {
					$sReturn .= '<tr><td colspan="2" align="center"><img class="image" alt="' . $p4a->i18n->messages->get('filepreview') . '" src="' . P4A_ROOT_PATH . '/p4a/libraries/phpthumb/phpThumb.php?src=' . $src . '&amp;w=' . $width . '&amp;h=' . $height . '" ' . $str_width . ' ' . $str_height . ' /></td></tr>';
				} else {
					$sReturn .= '<tr><td colspan="2" align="center"><img class="image" alt="' . $p4a->i18n->messages->get('filepreview') . '" src="' . $src . '" ' . $str_width . ' ' . $str_height . ' /></td></tr>';
				}
				$sReturn .= '<tr><th align="left">' . $p4a->i18n->messages->get('filename') . ':&nbsp;&nbsp;</th><td align="left">' . $this->getNewValue(0) . '</td></tr>';
				$sReturn .= '<tr><th align="left">' . $p4a->i18n->messages->get('filesize') . ':&nbsp;&nbsp;</th><td align="left">' . $p4a->i18n->autoFormat($this->getNewValue(2)/1024, "decimal") . ' KB</td></tr>';
				$sReturn .= '<tr><th align="left">' . $p4a->i18n->messages->get('filetype') . ':&nbsp;&nbsp;</th><td align="left">' . $this->getNewValue(3) . '</td></tr>';
				$sReturn .= '<tr><td colspan="2" align="center">' . $this->buttons->button_file_preview->getAsString() . ' '. $this->buttons->button_file_download->getAsString() . ' '  . $this->buttons->button_file_delete->getAsString() . '</td></tr>';
				$sReturn .= '</table>' ;
			}

			return $this->composeLabel() . $sReturn;
		}

		/**
		 * Sets the maximum size for image thumbnails.
		 * @param integer		Max size (width and height)
		 * @access public
		 */
		function setMaxThumbnailSize($size)
		{
			$this->max_thumbnail_size = $size;
		}

		/**
		 * Removes the maximum size for image thumbnails.
		 * @access public
		 */
		function unsetMaxThumbnailSize()
		{
			$this->max_thumbnail_size = NULL;
		}

		/**
		 * Returns the maximum size for image thumbnails.
		 * @return integer
		 * @access public
		 */
		function getMaxThumbnailSize()
		{
			return $this->max_thumbnail_size;
		}

		/**
		 * Sets the label for the field.
		 * In rendering phase it will be added with ':  '.
		 * @param string	The string to set as label.
		 * @access public
		 * @see P4A_Widget::$label
		 */
		function setLabel($value)
		{
			$this->label->setLabel($value);
		}

		/**
		 * Returns the label for the field.
		 * @return string
		 * @access public
		 */
		function getLabel()
		{
			return $this->label->getLabel();
		}

		/**
		 * Gets the HTML rendered field's label.
		 * @return string
		 * @access public
		 */
		function composeLabel()
		{
			return $this->label->getAsString();
		}

		/**
		 * Sets the alignment property for the field.
		 * @access public
		 * @param string		The align property.
		 */
		function setAlign($align)
		{
			$this->align = $align;
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
			foreach($this->properties as $property_name=>$property_value) {
				if (!(($this->type == 'password') and ($property_name == 'value'))) {
					$sReturn .= $property_name . "='" . htmlspecialchars($property_value) . "' " ;
				}
			}

			$sReturn .= $this->composeStringStyle();
			return $sReturn;
		}

		/**
		 * Returns the field's value differently if we are in an '<input value="' environment on in a '<tag>value</tag>' environment.
		 * @return string
		 * @access public
		 */
		function composeStringValue()
		{
			$value = $this->getNewValue();

			if (is_array($value)) {
				$value = join($value, ",");
			}
			
			switch ($this->type) {
				case 'text':
				case 'hidden':
				case 'date':
					return 'value="' . htmlspecialchars($value) . '" ';
					break;
				case 'textarea':
				case 'rich_textarea':
					return htmlspecialchars($value);
					break;
				case 'label':
					return $value;
					break;
			}
		}

		function enableUpload($enable = true)
		{
			$this->upload = $enable;
		}
	}
?>
