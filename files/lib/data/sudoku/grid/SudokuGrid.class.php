<?php
namespace wcf\data\sudoku\grid;

/**
 * Represents a sudoku grid.
 *
 * @author	Oliver Kliebisch
 * @copyright	2012 Oliver Kliebisch
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf
 * @subpackage	data.sudoku.grid
 * @category 	Community Framework
 */
class SudokuGrid {
	const GRID_SIZE = 9;
	const BLOCK_SIZE = 3;

	/**
	 * Contains the grid data.
	 *
	 * @var array<mixed>
	 */
	protected $grid = array();

	/**
	 * Creates a new instance of the SudukoGrid class.
	 *
	 * @param	mixed		$grid
	 */
	public function __construct($grid = null) {
		$this->grid = $grid;
		$this->initGrid();
	}

	/**
	 * Initalizes the grid, validates and fills in missing values.
	 */
	protected function initGrid() {
		if ($this->grid !== null && is_string($this->grid)) {
			// unflatten data
			$t = $this->grid;
			$this->grid = array();
			for ($i = 0; $i < SudokuGrid::GRID_SIZE; $i++) {
				$this->grid[$i] = array();
				for ($j = 0; $j < SudokuGrid::GRID_SIZE; $j++) {
					$this->grid[$i][$j] = $t{$i*SudokuGrid::GRID_SIZE + $j};
				}
			}
		}
		else if ($this->grid === null) {
			$this->grid = array();
		}
		for ($i = 0; $i < self::GRID_SIZE; $i++) {
			if (!isset($this->grid[$i])) $this->grid[$i] = array();
			for ($j = 0; $j < self::GRID_SIZE; $j++) {
				if (isset($this->grid[$i][$j]) && !$this->grid[$i][$j] instanceof SudokuGridCell) {
					if (is_int($this->grid[$i][$j]) && $this->grid[$i][$j] > 0) {
						$this->grid[$i][$j] = new SudokuGridCell($this->grid[$i][$j], true);
					}
					else {
						$this->grid[$i][$j] = new SudokuGridCell();
					}
				}
				else if (!isset($this->grid[$i][$j])) {
					$this->grid[$i][$j] = new SudokuGridCell();
				}
			}
		}
	}

	/**
	 * Returns the possible values for a given cell.
	 *
	 * @param 	integer 	$row
	 * @param 	integer 	$column
	 * @param 	boolean 	$cache
	 * @return	array<integer>
	 */
	public function getPossibleValues ($row, $column, $cache = true) {
		if (!$cache || !count($this->getCell($row, $column)->getPossibleValues())) {
			$blocksPerRow = self::GRID_SIZE / self::BLOCK_SIZE;
			$blockNumber = (int) (($row - 1) / self::BLOCK_SIZE) * $blocksPerRow + ((int) (($column - 1) / self::BLOCK_SIZE));
			$possibleValues = array();
			for ($i = 1; $i <= self::GRID_SIZE; $i++) {
				$possibleValues[$i] = $i;
			}

			// get other block cells value
			for ($i = ((int) ($blockNumber / self::BLOCK_SIZE)) * self::BLOCK_SIZE; $i < ((int) ($blockNumber / self::BLOCK_SIZE)) * self::BLOCK_SIZE + self::BLOCK_SIZE ; $i++) {
				for ($j = ($blockNumber % self::BLOCK_SIZE) * self::BLOCK_SIZE; $j < ($blockNumber % self::BLOCK_SIZE) * self::BLOCK_SIZE + self::BLOCK_SIZE; $j++) {
					if (($x = $this->getCell($i + 1, $j + 1)->getValue()) > 0) {
						unset ($possibleValues[$x]);
					}
				}
			}

			// get other row/column cells value
			for ($i = 1; $i <= self::GRID_SIZE; $i++) {
				if (($x = $this->getCell($i, $column)->getValue()) > 0) {
					unset ($possibleValues[$x]);
				}
				if (($x = $this->getCell($row, $i)->getValue()) > 0) {
					unset ($possibleValues[$x]);
				}
			}

			// store result in cell. if possible values are present
			// only delete values, but don't add some (needed for smart reduction)
			if (count($this->getCell($row, $column)->getPossibleValues())) {
				$this->getCell($row, $column)->setPossibleValues(array_intersect($this->getCell($row, $column)->getPossibleValues(), $possibleValues));
			}
			else {
				$this->getCell($row, $column)->setPossibleValues($possibleValues);
			}
		}

		return $this->getCell($row, $column)->getPossibleValues();
	}

	/**
	 * Returns the desired cell.
	 *
	 * @param 	integer 	$row
	 * @param 	integer 	$column
	 * @return	SudokuGridCell
	 */
	public function getCell ($row, $column) {
		return $this->grid[$row - 1][$column - 1];
	}

	/**
	 * Returns the whole grid data.
	 *
	 * @param	mixed
	 */
	public function getGridData() {
		return $this->grid;
	}

	/**
	 * Returns true if the grid has diggable cells.
	 *
	 * @return 	boolean
	 */
	public function hasDiggableCells() {
		for ($i = 1; $i <= self::GRID_SIZE; $i++) {
			for ($j = 1; $j <= self::GRID_SIZE; $j++) {
				if ($this->getCell($i, $j)->canBeDug()) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * All values in this grid will be marked as 'given'.
	 */
	public function makeValuesGiven() {
		for ($i = 1; $i <= self::GRID_SIZE; $i++) {
			for ($j = 1; $j <= self::GRID_SIZE; $j++) {
				if ($this->getCell($i, $j)->getValue() > 0) {
					$this->getCell($i, $j)->setGiven();
				}
			}
		}
	}

	/**
	 * Compares this grid to another one and returns true if they
	 * contain the same values.
	 *
	 * @param 	SudokuGrid 	$grid
	 * @return	boolean
	 */
	public function compareTo(SudokuGrid $grid) {
		for ($i = 1; $i <= self::GRID_SIZE; $i++) {
			for ($j = 1; $j <= self::GRID_SIZE; $j++) {
				if ($this->getCell($i, $j)->getValue() != $grid->getCell($i, $j)->getValue()) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Returns a flattened representation of the grid for data storage.
	 *
	 * @return	string
	 */
	public function getFlattenedGrid() {
		$s = '';
		for ($i = 1; $i <= self::GRID_SIZE; $i++) {
			for ($j = 1; $j <= self::GRID_SIZE; $j++) {
				$s .= $this->getCell($i, $j)->getValue();
			}
		}

		return $s;
	}

	/**
	 * Ugly method to receive a deep copy of this object.
	 * FIXME: Add a proper implementation.
	 *
	 * @return 	SudokuGrid
	 */
	public function deepCopy() {
       		return unserialize(serialize($this));
	}
}