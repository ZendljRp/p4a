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
 * Simple toolbar.
 * This toolbar has "confirm", "cancel", "print", "exit" buttons.
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @copyright CreaLabs SNC
 * @package p4a
 */
class P4A_Simple_Toolbar extends P4A_Toolbar
{
	/**
	 * @param string $name Mnemonic identifier for the object
	 */
	public function __construct($name)
	{
		parent::__construct($name);
		$this->addDefaultButtons();
	}
	
	private function addDefaultButtons()
	{
		$this->addButton('new', 'new');
		$this->buttons->new->setLabel("Insert a new element");
		$this->buttons->new->setProperty("accesskey", "N");
		
		$this->addButton('save', 'save');
		$this->buttons->save->setLabel("Confirm and save");
		$this->buttons->save->setAccessKey("S");

		$this->addButton('cancel', 'cancel');
		$this->buttons->cancel->setLabel("Cancel current operation");
		$this->buttons->cancel->setAccessKey("Z");

		$this->addSeparator();

		$this->addButton('delete', 'delete');
		$this->buttons->delete->setLabel("Delete current element");
		$this->buttons->delete->requireConfirmation();

		$this->addSeparator();

		$this->addButton('print', 'print');
		$this->buttons->print->dropAction('onclick');
		$this->buttons->print->setProperty('onclick', 'window.print(); return false;');
		$this->buttons->print->setAccessKey("P");

		$this->addButton('exit', 'exit', 'right');
		$this->buttons->exit->setLabel("Go back to the previous mask");
		$this->buttons->exit->setAccessKey("X");
	}

	/**
	 * @param P4A_Mask $mask
	 */
	public function setMask(P4A_Mask $mask)
	{
		$this->_mask_name = $mask->getName();

		$this->buttons->save->implementMethod('onClick', $mask, 'saveRow');
		$this->buttons->cancel->implementMethod('onClick', $mask, 'reloadRow');
		$this->buttons->new->implementMethod('onClick', $mask, 'newRow');
		$this->buttons->delete->implementMethod('onClick', $mask, 'deleteRow');
		$this->buttons->exit->implementMethod('onClick', $mask, 'showPrevMask');
	}
}