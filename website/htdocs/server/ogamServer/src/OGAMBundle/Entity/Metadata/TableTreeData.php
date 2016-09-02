<?php
namespace OGAMBundle\Entity\Metadata;

use Doctrine\ORM\Mapping as ORM;

/**
 * TableTreeData
 *
 * @ORM\Table(name="metadata.table_tree")
 * @ORM\Entity(repositoryClass="OGAMBundle\Repository\Metadata\tableTreeDataRepository")
 */
class TableTreeData {

	/**
	 * @ORM\ManyToOne(targetEntity="Schema")
	 * @ORM\JoinColumn(name="schema_code", referencedColumnName="schema_code")
	 * @ORM\Id
	 */
	private $schema;

	/**
	 * @ORM\ManyToOne(targetEntity="TableFormat")
	 * @ORM\JoinColumn(name="child_table", referencedColumnName="format")
	 * @ORM\Id
	 */
	private $tableFormat;

	/**
	 * @ORM\ManyToOne(targetEntity="TableFormat")
	 * @ORM\JoinColumn(name="parent_table", referencedColumnName="format")
	 */
	private $parentTableFormat;

	/**
	 *
	 * @var string @ORM\Column(name="join_key", type="string", length=255)
	 */
	private $joinKeys;

	/**
	 *
	 * @var string @ORM\Column(name="comment", type="string", length=255)
	 */
	private $comment;

	/**
	 * Set tableFormat
	 *
	 * @param string $tableFormat
	 *
	 * @return tableTreeData
	 */
	public function setTableFormat($tableFormat) {
		$this->tableFormat = $tableFormat;

		return $this;
	}

	/**
	 * Get tableFormat
	 *
	 * @return string
	 */
	public function getTableFormat() {
		return $this->tableFormat;
	}

	/**
	 * Set parentTableFormat
	 *
	 * @param string $parentTableFormat
	 *
	 * @return tableTreeData
	 */
	public function setParentTableFormat($parentTableFormat) {
		$this->parentTableFormat = $parentTableFormat;

		return $this;
	}

	/**
	 * Get parentTableFormat
	 *
	 * @return string
	 */
	public function getParentTableFormat() {
		return $this->parentTableFormat;
	}

	/**
	 * Set joinKeys
	 *
	 * @param string $joinKeys
	 *
	 * @return tableTreeData
	 */
	public function setJoinKeys($joinKeys) {
		$this->joinKeys = implode(",", $joinKeys);

		return $this;
	}

	/**
	 * Get joinKeys
	 *
	 * @return string
	 */
	public function getJoinKeys() {
		$joinKeys = array();
		$pks = explode(",", $this->joinKeys);
		foreach ($pks as $pk) {
			$joinKeys[] = trim($pk); // we need to trim all the values
		}

		return $joinKeys;
	}

	/**
	 * Set comment
	 *
	 * @param string $comment
	 *
	 * @return tableTreeData
	 */
	public function setComment($comment) {
		$this->comment = $comment;

		return $this;
	}

	/**
	 * Get comment
	 *
	 * @return string
	 */
	public function getComment() {
		return $this->comment;
	}
}

