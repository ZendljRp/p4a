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
	 * Tabular rapresentation of a data source.
	 * This is a complex widget that's used to allow users to navigate
	 * data sources and than (for example) edit a record or view details etc...
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @package p4a
	 */
	class P4A_TABLE extends P4A_WIDGET
	{
		/**
		 * Data source associated with the table.
		 * @var string
		 * @access private
		 */
		var $data = NULL;
		
		/**
		 * The data browser that navigates the table's data source.
		 * @var string
		 * @access private
		 */
		var $data_browser = NULL;
		
		/**
		 * The gui widgets to allow table navigation.
		 * @var table_navigation_bar
		 * @access private
		 */
		var $navigation_bar = NULL;
		
		/**
		 * The table toolbar.
		 * @var toolbar
		 * @access private
		 */
		var $toolbar = NULL;
		
		/**
		 * A little bar that displays a title for the table.
		 * This also allows collapsing/expanding the table view.
		 * @var link
		 * @access private
		 */
		var $title_bar = NULL;
		
		/**
		 * All the table's rows.
		 * @var table_rows
		 * @access private
		 */
		var $table_rows = NULL;
		
		/**
		 * Decides if the table will show the "field's header" row.
		 * @var boolean
		 * @access private
		 */
		var $show_header_row = TRUE;
		
		/**
		 * Stores the table's structure (table_cols).
		 * @var array
		 * @access private
		 */
		var $cols = array();
		
		/**
		 * Defines if the table is rollable or not.
		 * @var boolean
		 * @access private
		 */
		var $rollable = true;
		
		/**
		 * Decides if the table is collapsed or expanded.
		 * @var boolean
		 * @access private
		 */
		var $expand = TRUE;
				
		/**
		 * Class constructor.
		 * @param string				Mnemonic identifier for the object.
		 * @access private
		 */
		function &p4a_table($name)
		{
			parent::p4a_widget($name);
			$this->useTemplate('table');
			$this->setStyleProperty('border-collapse', 'collapse');
		}
		
		/**
		 * Sets the title for the table
		 * @param string		The title.
		 * @access public
		 * @see $title
		 */
		function setTitle($title)
		{
			if ($this->title_bar === NULL){
				$this->build("p4a_link",'title_bar')
			}
			$this->title_bar->setValue($title);
			$this->title_bar->addAction('onClick');
			$this->intercept($this->title_bar, 'onClick', 'rollup');
		}

		/**
		 * Sets the data source that the table will navigate.
		 * It also adds the data browser.
		 * @param data_source		The data source.
		 * @access public
		 */
		function setSource(&$data_source)
		{
			unset($this->data);
			$this->data =& $data_source;
			
			$this->setDataBrowser($this->data->getDataBrowser());
            $this->setDataStructure($this->data->getFields());
			
			$this->build("p4a_table_rows", "table_rows");
            if ($this->data_browser->getNumPages() > 1){
            	$this->addNavigationBar();
            }
		}
		
		/**
		 * Sets the table's structure (fields).
		 * @param array		All the fields.
		 * @access public
		 */
		function setDataStructure($array_fields)
		{
			$this->cols = array();
			foreach($array_fields as $field)
			{
				$this->addCol($field);
			}
		}
		
		/**
		 * Sets the data browser that will navigate the data source.
		 * @param data_browser		The data browser.
		 * @access public
		 */
		function setDataBrowser(&$data_browser)
		{
			unset($this->data_browser);
			$this->data_browser =& $data_browser;
		}
		
		/**
		 * Adds a column to the data structure.
		 * @param string		Column name.
		 * @access public
		 */
		function addCol($column_name)
		{
			$this->cols->build("p4a_table_col",$column_name);
		}
		
		/**
		 * Returns the HTML rendered object.
		 * @access public
		 */
		function getAsString()
		{
			$this->clearDisplay();
			
			if (!$this->isVisible()) {
				return '';
			}
			
			$this->display('expand', $this->expand);
			$this->display('table_properties', $this->composeStringProperties());
			
			if ($this->toolbar !== NULL and 
				$this->toolbar->isVisible())
			{
				$this->display('toolbar', $this->toolbar->getAsString());
			}
			
			if ($this->navigation_bar !== NULL and 
				$this->navigation_bar->isVisible())
			{
				$this->display('navigation_bar', $this->navigation_bar);
			}
			
			if ($this->title_bar !== NULL and 
				$this->title_bar->isVisible())
			{
				if ($this->isRollable()) {
					$this->display('title_bar', $this->title_bar);
				} else {
					$this->display('title_bar', $this->title_bar->getValue());
				}
			}
			
			if($this->show_header_row)
			{
				$headers = array();
				$i = 0;
				
				$is_orderable	= false;
				$order_field	= NULL;
				$order_mode		= NULL;
				
				if( $this->data->getObjectType() == 'db_source' )
				{
					$is_orderable = true;
					
					if( $this->data->hasOrder() )
					{
    					$order			= $this->data->getOrder();
    					$order_field	= $order[0]['field'];
    					$order_mode		= $order[0]['mode'];
					}
				}
				
				foreach($this->cols as $col)
				{
					if ($col->isVisible())
					{
						$headers[$i]['properties']	= $col->composeStringProperties();											
						$headers[$i]['value']		= $col->getLabel();
						$headers[$i]['action']		= $col->composeStringActions();
						$headers[$i]['order']		= NULL;
						
						if( $is_orderable and ( $order_field == $col->getName() ) )
						{
							 $headers[$i]['order'] = $order_mode ;
						}
						
						$i++;	
					}
				}
				$this->display('headers', $headers);
			}
			
			if ($this->data->getNumRows()){
				$this->display('table_rows_properties', $this->table_rows->composeStringProperties());
				$this->display('table_rows', $this->table_rows->getRows());
			}else{
				$this->display('table_rows_properties', NULL);
				$this->display('table_rows', NULL);
			}
			
			$visible_cols = $this->getVisibleCols();
			if( sizeof( $visible_cols ) > 0 ) {
				$this->display('table_cols', 'TRUE');
			} else {
				$this->display('table_cols', NULL);
			}
			
			return $this->fetchTemplate();
		}
		
		//todo
		function newToolbar($toolbar)
		{ 
			unset($this->toolbar);
			$this->build("P4A_Toolbar", "toolbar");
		}
		
		/**
		 * Adds a generic toolbar to the table.
		 * @access public
		 */		
		 
		function addToolbar(&$toolbar = NULL)
		{
			unset($this->toolbar);
			$this->toolbar =& $toolbar;
		}
		
		/**
		 * Makes the toolbar visible.
		 * @access public
		 */
		function showToolbar()
		{
			if (is_object($this->toolbar)){
				$this->toolbar->setVisible();	
			}else{
				ERROR('NO TOOLBAR');
			}
		}
		
		/**
		 * Makes the toolbar invisible.
		 * @access public
		 */
		function hideToolbar()
		{
			if (is_object($this->toolbar)){
				$this->toolbar->setInvisible();	
			}else{
				ERROR('NO TOOLBAR');
			}
		}
		
		/**
		 * Adds the navigation bar to the table.
		 * @access public
		 */
		function addNavigationBar(){
			$this->build("p4a_table_navigation_bar", "navigation_bar");
		}
		
		/**
		 * Makes the navigation bar visible.
		 * @access public
		 */
		function showNavigationBar()
		{
			if ($this->navigation_bar === NULL ){
				$this->addNavigationBar();
			}
			$this->navigation_bar->setVisible();
		}
		
		/**
		 * Makes the navigation bar hidden.
		 * @access public
		 */
		function hideNavigationBar()
		{
			if ($this->navigation_bar !== NULL ){
				$this->navigation_bar->setInvisible();
			}
		}
		
		/**
		 * Sets the title bar visible
		 * @access public
		 */
		function showTitleBar(){
			if ($this->title_bar !== NULL){
				$this->setTitle($this->name);
			}
			$this->title_bar->setVisible();
		}
		
		/**
		 * Sets the title bar hidden
		 * @access public
		 */
		function showHeaderRow()
		{
			$this->show_header_row = TRUE;
		}
		
		/**
		 * Sets the header row hidden
		 * @access public
		 * @see $show_header_row
		 */
		function hideHeaderRow()
		{
			$this->show_header_row = FALSE;
		}
		
		/**
		 * Returns true if the table is rollable
		 * @access public
		 */
		function isRollable()
		{
			return $this->rollable;
		}
		
		/**
		 * Enable table roolup when clicking on table title.
		 * @access public
		 */
		function enableRollup()
		{
			$this->rollable = true;
		}
		
		/**
		 * Disable table roolup when clicking on table title.
		 * @access public
		 */
		function disableRollup()
		{
			$this->rollable = false;
		}
		
		/**
		 * Sets the table collapsed if it was expanded or sets the table expanded if it was collapsed.
		 * @access public
		 */
		function rollup()
		{
			$this->expand = ! $this->expand;
		}
		
		/**
		 * Sets the table expanded.
		 * @access public
		 */
		function expand()
		{
			$this->expand = TRUE;			
		}
		
		/**
		 * Sets the table collapsed.
		 * @access public
		 */
		function collapse()
		{
			$this->expand = FALSE;			
		}
		
		/**
		 * Return an array with all columns id.
		 * @access public
		 * @return array
		 */
		function getCols()
		{
			return array_keys($this->cols);
		}
		
		/**
		 * Return an array with all id of visible columns.
		 * @access public
		 * @return array
		 */
		function getVisibleCols()
		{
			$return = array();
			
			foreach( array_keys( $this->cols ) as $col )
			{
				if( $this->cols[$col]->isVisible() )
				{
					$return[] = $col;
				}
			}
			
			return $return;
		}
		
		/**
		 * Return an array with all id of invisible columns.
		 * @access public
		 * @return array
		 */
		function getInvisibleCols()
		{
			$return = array();
			
			foreach( array_keys( $this->cols ) as $col )
			{
				if( !$this->cols[$col]->isVisible() )
				{
					$return[] = $col;
				}
			}
			
			return $return;
		}
		
		/**
		 * Sets all passed columns visible.
		 * If no array is given, than sets all columns visible.
		 * @access public
		 * @params array	Columns id in indexed array.
		 */
		function setVisibleCols( $cols = array() )
		{
			if( sizeof( $cols ) == 0 )
			{
				$cols = $this->getCols();
			}
			
			foreach( $cols as $col )
			{
				$this->cols[$col]->setVisible();
			}
		}
		
		/**
		 * Sets all passed columns invisible.
		 * If no array is given, than sets all columns invisible.
		 * @access public
		 * @params array	Columns id in indexed array.
		 */
		function setInvisibleCols( $cols = array() )
		{
			if( sizeof( $cols ) == 0 )
			{
				$cols = $this->getCols();
			}
			
			foreach( $cols as $col )
			{
				$this->cols[$col]->setInvisible();
			}
		}
	}
	
	/**
	 * Keeps the data for a single table column.
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @package p4a
	 */
	class P4A_TABLE_COL extends P4A_WIDGET
	{
		/**
		 * Keeps the header string.
		 * @var string
		 * @access private
		 */
		var $header = NULL;
		
		/**
		 * Data source for the field.
		 * @var data_source
		 * @access private
		 */
		var $data = NULL;

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
		 * Class constructor.
		 * @param string		Mnemonic identifier for the object.
		 * @access private
		 */
		function &p4a_table_col($name)
		{
			parent::p4a_widget($name);
			$this->setDefaultLabel();
			$this->addAction('onClick');
		}
		
		/**
		 * Sets the header for the column.
		 * @param string		The header
		 * @access public
		 * @see $header
		 */
		function setHeader($header)
		{
			$this->setLabel($header);
		}
		
		/**
		 * Returns the header for the column.
		 * @access public
		 * @see $header
		 */
		function getHeader()
		{
			return $this->getLabel();
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
			
			if( $this->data->pk !== NULL )
			{
				if( $this->getSourceValueField() === NULL )
				{
					$this->setSourceValueField( $this->data->pk ) ;
				}
				
				if( $this->getSourceDescriptionField() === NULL )
				{
					$aFields = $this->data->getFields() ;
					
					$iDescriptionFieldIndex = 0 ;
					
					if( ( sizeof( $aFields ) > 1 ) and ( $aFields[0] == $this->data->pk ) ) {
						$iDescriptionFieldIndex = 1 ;
					}
					
					$this->setSourceDescriptionField( $aFields[ $iDescriptionFieldIndex ] ) ;
				}				
			}
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
		 * Translate the value with the description
		 * @param string		The value to translate		 
		 * @return string
		 * @access public
		 */
		function getDescription($value)
		{
			if (! $this->data){
				return $value;
			}else{
				$row = $this->data->getPkRow($value);
				if (is_array($row)){
					return $row[$this->data_description_field];
				}else{
					return $value;
				}
			}
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
		 * Sets the column as formatted.
		 * @access public
		 */
		function setFormatted( $value = true )
		{
			$this->formatted = $value;
		}
		
		/**
		 * Sets the column as not formatted.
		 * @access public
		 */
		function unsetFormatted()
		{
			$this->formatted = false;
		}
		
		/**
		 * Sets the formatter and format for the column.
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
		
		function onClick()
		{
			if( $this->parent->data->getObjectType() == 'db_source' )
			{
				$new_order = 'ASC';
				
				if( $this->parent->data->hasOrder() )
				{
					$order = $this->parent->data->dropMasterOrder();
					
					if( $order['field'] == $this->getName() )
					{
    					if( $order['mode'] == 'ASC' ) {
    						$new_order = 'DESC';
    					}
					}
					else
					{
						$new_order = $order['mode'];
					}
				}
				
				$this->parent->data->addMasterOrder($this->getName(), $new_order);
				$this->parent->data->load();
			}
		}
	}
	
	/**
	 * Keeps all the data for all the rows.
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @package p4a
	 */
	class P4A_TABLE_ROWS extends P4A_WIDGET
	{
		/**
		 * Class constructor.
		 * By default we add an onClick action.
		 * @param string		Mnemonic identifier for the object
		 * @access private
		 */
		function &p4a_table_rows($name = 'table_rows')
		{
			parent::p4a_widget($name);
			$this->addAction('onClick');
		}
		
		/**
		 * Sets the max height for the data rows.
		 * This is done adding a scrollbar to the table body.
		 * @param integer		The desidered height.
		 * @param string		Measure unit
		 * @access public
		 */
		function setMaxHeight( $height, $unit = 'px' )
		{
			$this->setStyleProperty( 'max-height', $height . $unit );
		}
		 
		/**
		 * Retrive data for the current page.
		 * @return array
		 * @access private
		 */
		function getRows()
		{
			$aReturn = array();
			$rows = $this->parent->data_browser->getCurrentPage();
			$aCols = array();
			foreach($this->parent->cols as $col_name=>$col)
			{
				if ($col->visible){
					$aCols[] = $col_name;
				}
			}
			
			$i = 0;
			foreach($rows as $row_number=>$row)
			{
				$j = 0;
				$action = $this->composeStringActions($row_number);
				if ($row_number == $this->parent->data_browser->getRowNumber()){
					$aReturn[$i]['row']['active'] = TRUE;
				}else{
					$aReturn[$i]['row']['active'] = FALSE;
				}
				foreach($aCols as $col_name)
				{
					$aReturn[$i]['cells'][$j]['action'] = $action; 
					if ($this->parent->cols[$col_name]->data)
					{
						$aReturn[$i]['cells'][$j]['value'] = $this->parent->cols[$col_name]->getDescription($row[$col_name]);
					}
					elseif ($this->parent->cols[$col_name]->isFormatted())
					{
						if( ( $this->parent->cols[$col_name]->formatter_name === NULL ) and ( $this->parent->cols[$col_name]->format_name === NULL ) )
						{
							$aReturn[$i]['cells'][$j]['value'] = $this->p4a->i18n->autoFormat($row[$col_name], $this->parent->data->structure[$col_name]['type']);
						}
						else
						{
							$aReturn[$i]['cells'][$j]['value'] = $this->p4a->i18n->{$this->parent->cols[$col_name]->formatter_name}->format( $row[$col_name], $this->p4a->i18n->{$this->parent->cols[$col_name]->formatter_name}->getFormat( $this->parent->cols[$col_name]->format_name ) );
						}
					}
					else
					{
						$aReturn[$i]['cells'][$j]['value'] = $row[$col_name];
					}
					
					$aReturn[$i]['cells'][$j]['row_number'] = $i;
					$j++;						
				}
				$i++;
			}
			return $aReturn;
		}
		
		/**
		 * onClick action handler for the row.
		 * We move pointer to the clicked row.
		 * @param array		All passed params.
		 * @access public
		 */
		function onClick($aParams)
		{
			if( $this->actionHandler('beforeClick', $aParams) == ABORT ) return ABORT;
			
			if( $this->parent->data_browser->moveRow($aParams[0]) == ABORT ) return ABORT;
			
			if( $this->actionHandler('afterClick', $aParams) == ABORT ) return ABORT;
		}
	}
	
	/**
	 * The gui widgets to navigate the table.
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @package p4a
	 */
	class P4A_TABLE_NAVIGATION_BAR extends P4A_TOOLBAR
	{
		/**
		 * Class constructor.
		 * @param string		Mnemonic identifier for the object.
		 * @access private
		 */
		function &p4a_table_navigation_bar()
		{
			parent::p4a_toolbar();
			$this->setStyleProperty('display', 'block');
			
			$this->newButton('button_first', 'little_first');
			$this->buttons['button_first']->addAction('onClick');
			$this->intercept($this->buttons['button_first'], 'onClick', 'firstOnClick');
			
			$this->newButton('button_prev', 'little_prev');
			$this->buttons['button_prev']->addAction('onClick');
			$this->intercept($this->buttons['button_prev'], 'onClick', 'prevOnClick');
			
			$this->newButton('button_next', 'little_next');
			$this->buttons['button_next']->addAction('onClick');
			$this->intercept($this->buttons['button_next'], 'onClick', 'nextOnClick');
			
			$this->newButton('button_last', 'little_last');
			$this->buttons['button_last']->addAction('onClick');
			$this->intercept($this->buttons['button_last'], 'onClick', 'lastOnClick');
			
			$this->addSpace(20);
			
			$this->addButton(new p4a_label('current_page'));
			
			$this->addSpace(20);
			
			$this->addButton(new p4a_field('field_num_page'));
			$this->buttons['field_num_page']->setWidth(30);
			$this->buttons['field_num_page']->addAction('onReturnPress');
			$this->intercept($this->buttons['field_num_page'], 'onReturnPress', 'goOnClick');

			$this->newButton('button_go', 'go');
			$this->buttons['button_go']->addAction('onClick');
			$this->intercept($this->buttons['button_go'], 'onClick', 'goOnClick');
		}
		
		function getAsString()
		{
			$this->buttons['field_num_page']->setLabel( $this->p4a->i18n->messages->get( 'go_to_page' ) );
			$this->buttons['field_num_page']->setNewValue($this->parent->data_browser->getNumPage());
			$current_page  = $this->p4a->i18n->messages->get('current_page');
			$current_page .= '&nbsp;';
			$current_page .= $this->parent->data_browser->getNumPage();
			$current_page .= '&nbsp;';
			$current_page .= $this->p4a->i18n->messages->get('of_pages');
			$current_page .= '&nbsp;';
			$current_page .= $this->parent->data_browser->getNumPages();
			$current_page .= '&nbsp;';
			$this->buttons['current_page']->setValue($current_page);
			return parent::getAsString();
		}
		
		/**
		 * Action handler for "next" button click.
		 * @access public
		 */
		function nextOnClick()
		{
			$this->parent->data_browser->moveNextPage();
		}
		
		/**
		 * Action handler for "previous" button click.
		 * @access public
		 */
		function prevOnClick()
		{
			$this->parent->data_browser->movePrevPage();
		}
		
		/**
		 * Action handler for "first" button click.
		 * @access public
		 */
		function firstOnClick()
		{
			$this->parent->data_browser->moveFirstPage();
		}

		/**
		 * Action handler for "last" button click.
		 * @access public
		 */
		function lastOnClick()
		{
			$this->parent->data_browser->moveLastPage();
		}
		
		/**
		 * Action handler for "go" button click.
		 * @access public
		 */
		function goOnClick()
		{
			$this->parent->data_browser->movePage($this->buttons['field_num_page']->getNewValue());
		}

	} 
?>