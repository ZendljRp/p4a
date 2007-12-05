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

class Products_Catalogue extends P4A
{
	function Products_Catalogue()
	{
		parent::p4a();
		$this->setTitle("Products Catalogue");

		// Menu
		$this->build("p4a_toolbar", 'menu');
		$this->menu->addButton("products");
		$this->intercept($this->menu->items->products, "onClick", "menuClick");
		$this->menu->addButton("support_tables");
		$this->menu->items->support_tables->addMenu();
		$this->menu->items->support_tables->menu->addItem("categories");
		$this->intercept($this->menu->items->support_tables->menu->items->categories, "onClick", "menuClick");
		$this->menu->items->support_tables->menu->addItem("brands");
		$this->intercept($this->menu->items->support_tables->menu->items->brands, "onClick", "menuClick");
		

		// Data sources
		$this->build("p4a_db_source", "brands");
		$this->brands->setTable("brands");
		$this->brands->setPk("brand_id");
		$this->brands->addOrder("description");
		$this->brands->load();
		$this->brands->fields->brand_id->setSequence("brands_brand_id");

		$this->build("p4a_db_source", "categories");
		$this->categories->setTable("categories");
		$this->categories->setPk("category_id");
		$this->categories->addOrder("description");
		$this->categories->load();
		$this->categories->fields->category_id->setSequence("categories_category_id");

		// Primary action
		$this->openMask("products");
	}

	function menuClick()
	{
		$this->openMask($this->active_object->getName());
	}
}