<?php
//todo
class P4A_COLLECTION extends P4A_Object
{
	var $_pointer = 0;
	function &p4a_collection($name = null)
	{
		parent::P4AObject($name);
	}

	//todo da modificare, sbagliata in caso di destroy di un figlio
	function &nextItem()
	{
		$p4a =& P4A::singleton();
		if ($this->_pointer < count($this->_objects)){
			$id = $this->_objects[$this->_pointer];
			$this->_pointer++;
			return $p4a->objects[$id];
		}else{
			$this->_pointer = 0;
		}
	}

	//todo
	function getNumItems()
	{
 		return count($this->_objects);
	}

	function reset()
	{
		$this->_pointer = 0;
	}
}
?>