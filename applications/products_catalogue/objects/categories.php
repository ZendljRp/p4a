<?php

class Categories extends P4A_Mask
{
	function &Categories()
	{
		$this->p4a_mask();
		$p4a =& p4a::singleton();

		$this->build("p4a_message", "message");
		$this->message->setWidth("300");

		$p4a->brands->firstRow();
		$this->setSource($p4a->categories);

		$this->fields->category_id->disable();

		$this->build("p4a_standard_toolbar", "toolbar");
		$this->toolbar->setMask($this);

		$this->build("p4a_table", "table");
		$this->table->setSource($p4a->categories);
		$this->table->showNavigationBar();
		$this->table->setWidth(500);

		$this->build("p4a_frame", "sheet");
		$this->sheet->setWidth(700);
		$this->sheet->anchorCenter($this->message);
		$this->sheet->anchor($this->table);

		$this->fields->category_id->setLabel("Category ID");
		$this->table->cols->category_id->setLabel("Category ID");
		$this->table->showNavigationBar();

		$this->build("p4a_fieldset", "fields_sheet");
		$this->fields_sheet->setTitle("Category detail");
		$this->fields_sheet->anchor($this->fields->category_id);
		$this->fields_sheet->anchor($this->fields->description);
		$this->fields_sheet->anchor($this->fields->visible);

 		$this->sheet->anchor($this->fields_sheet);

		//Mandatory Fields
	    $this->mf = array("description");
		foreach($this->mf as $mf){
			$this->fields->$mf->label->setFontWeight("bold");
		}

		$this->display("menu", $p4a->menu);
		$this->display("top", $this->toolbar);
		$this->display("main", $this->sheet);

		$this->setFocus($this->fields->description);
	}

	function saveRow()
	{
		$errors = array();

		foreach ($this->mf as $field) {
			if (strlen($this->fields->$field->getNewValue()) == 0) {
				$errors[] = $field;
			}
		}

		if (sizeof($errors) > 0) {
			$this->message->setValue("Please fill all required fields");

			foreach ($errors as $field) {
				$this->fields->$field->setStyleProperty("border", "1px solid red");
			}
		} else {
			parent::saveRow();
		}
	}

	function main()
	{
		parent::main();

		foreach ($this->mf as $field) {
			$this->fields->$field->unsetStyleProperty("border");
		}
	}
}

?>