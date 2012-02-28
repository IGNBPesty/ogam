<?php
/**
 * © French National Forest Inventory
 * Licensed under EUPL v1.1 (see http://ec.europa.eu/idabc/eupl).
 */

/**
 * Represent a node in a taxonomic referential.
 *
 * @package objects
 * @SuppressWarnings checkUnusedVariables
 */
class Genapp_Object_Metadata_TaxrefNode extends Genapp_Object_Metadata_TreeNode {

	/**
	 * Indicate that the taxon is a reference.
	 */
	var $isReference = false;

	/**
	 * The childs.
	 * @var Array[TaxrefNodes]
	 */
	var $children = array();

	/**
	 * The short name of the taxon.
	 */
	var $name;

	/**
	 * The complete name of the taxon.
	 */
	var $completeName;

	/**
	 * The vernacular name of the taxon.
	 */
	var $vernacularName;



	/**
	 * Add a child.
	 *
	 * @param Genapp_Object_Metadata_TreeNode $child a node to add
	 */
	public function addChild($child) {
		$this->children[] = $child;
	}

	/**
	 * Return a node in the tree structure.
	 *
	 * @param String $code a node code
	 * @return TreeNode the TreeNode found, null if not found
	 */
	public function getNode($aCode) {

		if ($this->code == $aCode) {
			return $this;
		} else {
			foreach ($this->children as $child) {
				if ($child->getNode($aCode)) {
					return $child;
				}
			}
		}

		return null;
	}

	/**
	 * Serialize the object as a JSON.
	 *
	 * @return JSON the descriptor
	 */
	public function toJSON() {

		$return = '';
		if (empty($this->code) && empty($this->label)) {
			// Case when the root is just a placeholder, we return only the children
			foreach ($this->children as $child) {
				$return .= $child->toJSON().',';
			}
			$return = substr($return, 0, -1); // remove the last comma
		} else {
			// We return the root itself plus the children
			$return .= '{"text":'.json_encode($this->name);
			$return .= ',"id":'.json_encode($this->code);
			if ($this->isLeaf) {
				$return .= ',"leaf":true';
			}
			$return .= ',"vernacularName":'.json_encode($this->vernacularName);
			$return .= ',"isReference":'.json_encode($this->isReference);

			if (!empty($this->children)) {
				$return .= ',"children": [';
				foreach ($this->children as $child) {
					$return .= $child->toJSON().',';
				}
				$return = substr($return, 0, -1); // remove the last comma
				$return .= ']';
			}
			$return .= '}';
		}

		return $return;
	}

	/**
	 * Return a array to full a select list.
	 *
	 * @return array the array formated
	 */
	public function formatForList() {

		$return = array();
		if (!empty($this->code)) {
			// We return the root itself plus the children
			$return['code'] = $this->code;
			$return['label'] = $this->name;
			if ($this->isReference) {
				$return['description'] = '<b>'.$this->completeName.'</b>';
			} else {
				$return['description'] = $this->completeName;
			}
			if (!empty($this->vernacularName)) {
				$return['description'] .= '<br/>&nbsp;&nbsp;&nbsp;<i>'.$this->vernacularName.'</i>';
			}
			if (!empty($this->name)) {
				$return['description'] .= '<br/>&nbsp;&nbsp;&nbsp;<i>'.$this->name.'</i>';
			}
		}

		return $return;
	}

}