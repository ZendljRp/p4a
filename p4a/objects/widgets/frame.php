<?php

class P4A_Frame extends P4A_Widget
{

	var $_map = array();
	var $_row = 1;

	function &P4A_Frame($name)
	{
		parent::P4A_Widget($name);
	}

	function _anchor(&$object, $margin = "20px", $float="left")
	{
		if (is_object($object)) {
			$to_add = array("id"=>$object->getId(), "margin" => $margin, "float" => $float);
			$this->_map[$this->_row][]  = $to_add;
		}
	}

	function anchor(&$object, $margin = "0px", $float="left")
	{
		$this->newRow();
		$this->_anchor($object, $margin, $float);
	}

	function anchorRight(&$object, $margin = "10px")
	{
		$this->_anchor($object, $margin, "right");
	}

	function anchorLeft(&$object, $margin = "10px")
	{
		$this->_anchor($object, $margin, "left");
	}

	function anchorCenter(&$object, $margin = "10px")
	{
		$this->_anchor($object, $margin, "none");
	}

	function newRow()
	{
		$this->_row++;
	}

	function getAsString()
	{
		if (!$this->isVisible()) {
			return "";
		}

		$p4a =& P4A::singleton();
		$properties = $this->composeStringProperties();
		$actions = $this->composeStringActions();

		$string  = "<div class='frame' $properties $actions >";
		foreach($this->_map as $objs){
			$one_visible = false;
			$row = "\n<div class='row' style='border:1px solid white'>";
			foreach ($objs as $obj) {
				$object =& $p4a->getObject($obj["id"]);
				$as_string = $object->getAsString();
				if (strlen($as_string)>0) {
					$one_visible = true;
					$float = $obj["float"];
					$margin = "margin-" . $obj["float"];
					$margin_value = $obj["margin"];
					$row .= "\n\t<div style='padding:2px;float:$float;$margin:$margin_value'>";
					$row .= "\n\t\t$as_string" ;
					$row .= "\n\t</div>";
				}
			}

			$row .= "\n\n\t<div class='br'></div>\n";
			$row .= "\n</div>\n";

			if ($one_visible) {
				$string .= $row;
			}
		}
		$string .= "</div>\n\n";
		return $string;
	}
}

?>
