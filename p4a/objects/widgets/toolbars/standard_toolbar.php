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
	 * Standard toolbar for data source operations.
	 * This toolbar has "confirm", "cancel", "first", "prev", "next", "last", "new", "delete", "exit" buttons. 
	 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
	 * @author Andrea Giardina <andrea.giardina@crealabs.it>
	 * @package p4a
	 * @see TOOLBAR
	 */
	class P4A_STANDARD_TOOLBAR extends P4A_TOOLBAR
	{
		/**
		 * Class costructor.
		 * @param string				Mnemonic identifier for the object.
		 * @param mask					The mask on wich the toolbar will operate.
		 * @access private
		 */
		function &p4a_standard_toolbar($name, &$mask)
		{
			parent::p4a_toolbar($name);
			$this->mask = &$mask;
			
			$this->addButton(new p4a_button('confirm', 'big_confirm'));
			$this->buttons['confirm']->implementMethod('onClick', $this->mask, 'updateRow');

			$this->addButton(new p4a_button('cancel', 'big_cancel'));
			$this->buttons['cancel']->implementMethod('onClick', $this->mask, 'reloadRow');
			
			$this->addSeparator();

			$this->addButton(new p4a_button('first', 'big_first'));
			$this->buttons['first']->implementMethod('onClick', $this->mask, 'firstRow');

			$this->addButton(new p4a_button('prev', 'big_prev'));
			$this->buttons['prev']->implementMethod('onClick', $this->mask, 'prevRow');

			$this->addButton(new p4a_button('next', 'big_next'));
			$this->buttons['next']->implementMethod('onClick', $this->mask, 'nextRow');
			
			$this->addButton(new p4a_button('last', 'big_last'));
			$this->buttons['last']->implementMethod('onClick', $this->mask, 'lastRow');
			
			$this->addSeparator();
			
			$this->addButton(new p4a_button('new', 'big_new'));
			$this->buttons['new']->implementMethod('onClick', $this->mask, 'newRow');
			
			$this->addButton(new p4a_button('delete', 'big_delete'));
			$this->buttons['delete']->requireConfirmation('onClick', NULL, 'confirm_delete');
			$this->buttons['delete']->implementMethod('onClick', $this->mask, 'deleteRow');
			
			$this->addSeparator();
			
			$this->addButton(new p4a_button('print', 'big_print'));
			$this->buttons['print']->dropAction('onClick');
			$this->buttons['print']->setProperty('onClick', 'window.print(); return false;');

			$this->addButton(new p4a_button('exit', 'big_exit'));
			$this->buttons['exit']->implementMethod('onClick', $this->mask, 'showPrevMask');
		}
	}