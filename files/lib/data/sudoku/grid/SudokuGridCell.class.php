<?php
namespace wcf\data\sudoku\grid;

/**
 * Represents a sudoku grid cell.
 *
 * @author	Oliver Kliebisch
 * @copyright	2012 Oliver Kliebisch
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf
 * @subpackage	data.sudoku.grid
 * @category 	Community Framework
 */
class SudokuGridCell {
	/**
	 * The value of this cell.
	 *
	 * @var integer
	 */
	protected $value = 0;

	/**
	 * Stores whether this cell can be digged out of the grid.
	 *
	 * @var boolean
	 */
	protected $canBeDug = true;

	/**
	 * Stores whether this cell value is a given value.
	 *
	 * @var boolean
	 */
	protected $given = false;

	/**
	 * Stores all possible values for this cell, if empty.
	 *
	 * @var array<mixed>
	 */
	protected $possibleValues = array();

	/**
	 * Creates a new SudokuGridCell object.
	 *
	 * @param 	integer 	$value
	 * @param 	boolean		$given
	 */
	public function __construct($value = 0, $given = false) {
		$this->value = $value;
		$this->given = $given;
	}

	/**
	 * Sets the value of this cell.
	 *
	 * @param 	integer		$value
	 * @param 	boolean 	$given
	 * @param	boolean		$locked
	 */
	public function setValue($value, $given = false) {
		$this->value = $value;
		$this->given = $given;
	}

	/**
	 * Returns the value of this cell.
	 *
	 * @return 	integer
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * Returns whether this cell is given.
	 *
	 * @return	boolean
	 */
	public function isGiven() {
		return $this->given;
	}

	/**
	 * Sets this cell to be given.
	 *
	 * @param 	boolean 	$given
	 */
	public function setGiven($given = true) {
		$this->given = $given;
	}

	/**
	 * Sets the possible values for this cell.
	 *
	 * @param 	array<integer> 	$values
	 */
	public function setPossibleValues($values) {
		$this->possibleValues = $values;
	}

	/**
	 * Returns the possible values for this cell.
	 *
	 * @return	array<integer>
	 */
	public function getPossibleValues() {
		return $this->possibleValues;
	}

	/**
	 * Returns whether this cell can be dug.
	 *
	 * @return	boolean
	 */
	public function canBeDug() {
		return $this->canBeDug;
	}

	/**
	 * Toggle whether this cell can be dug.
	 */
	public function toggleCanBeDug() {
		$this->canBeDug = !$this->canBeDug;
	}

	/**
	 * Outputs the cell value as a string.
	 */
	public function __toString() {
		return $this->getValue();
	}
}