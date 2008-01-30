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
 * Navigation toolbar for data source operations.
 * This toolbar has "first", "prev", "next", "last", "exit" buttons.
 * @author Fabrizio Balliano <fabrizio.balliano@crealabs.it>
 * @author Andrea Giardina <andrea.giardina@crealabs.it>
 * @package p4a
 * @see P4A_Toolbar
 */
class P4A_Navigation_Toolbar extends P4A_Toolbar
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
		$first =& $this->addButton('first', 'first');
		$first->setLabel("Go to the first element");
		$first->setAccessKey(8);

		$prev =& $this->addButton('prev', 'prev');
		$prev->setLabel("Go to the previous element");
		$prev->setAccessKey(4);

		$next =& $this->addButton('next', 'next');
		$next->setLabel("Go to the next element");
		$next->setAccessKey(6);

		$last =& $this->addButton('last', 'last');
		$last->setLabel("Go to the last element");
		$last->setAccessKey(2);

		$this->addSeparator();

		$print =& $this->addButton('print', 'print');
		$print->dropAction('onclick');
		$print->setProperty('onclick', 'window.print(); return false;');
		$print->setAccessKey("P");

		$exit =& $this->addButton('exit', 'exit', 'right');
		$exit->setLabel("Go back to the previous mask");
		$exit->setAccessKey("X");
	}

	/**
	 * @param P4A_Mask $mask
	 */
	public function setMask(P4A_Mask $mask)
	{
		$this->_mask_name = $mask->getName();

		$this->buttons->first->implementMethod('onClick', $mask, 'firstRow');
		$this->buttons->prev->implementMethod('onClick', $mask, 'prevRow');
		$this->buttons->next->implementMethod('onClick', $mask, 'nextRow');
		$this->buttons->last->implementMethod('onClick', $mask, 'lastRow');
		$this->buttons->exit->implementMethod('onClick', $mask, 'showPrevMask');
	}
}