<?php

class mysql_manager extends p4a
{
    function &mysql_manager()
    {
        $this->p4a();
        $this->openMask("db_configuration");
    }
    
    function &main()
    {
        if (isset($this->dsn)){
            define("P4A_DSN", $this->dsn);
        }
        parent::main();
    }
    
    function menuClick(&$object)
    {
        $name = $object->getName();
        if (!isset($this->masks->$name)) {
            $this->masks->build('table_mask', $name);
        }
        $this->openMask($name);
    }
	
	function editMask()
	{
		$mask =& $this->active_mask;
		$table =& $mask->table;
		$table->setStyleProperty("border", "1px dashed red");
		
		$fields =& $this->active_mask->fields;
		while($field =& $fields->nextItem()){
			$field->setStyleProperty("border", "1px dashed #ccc");
			$field->setStyleProperty("cursor", "pointer");
			$field->setNewValue("");
			$field->addAction("onClick");
			$mask->intercept($field, "onClick", "editField");
			$field->enable();
		}
	}
}
?>
