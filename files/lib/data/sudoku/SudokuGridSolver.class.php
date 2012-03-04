<?php
namespace wcf\data\sudoku;
use wcf\system\SingletonFactory;

/**
 * Solves a SudokuGrid by using a depth-search backtracking algorithm.
 *
 * @author	Oliver Kliebisch
 * @copyright	2012 Oliver Kliebisch
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf
 * @subpackage	data.sudoku
 * @category 	Community Framework
 */
class SudokuGridSolver extends SingletonFactory {

	/**
	 * Solves the given SudokuGrid.
	 *
	 * @param SudokuGrid $grid
	 */
	public function solve(SudokuGrid $grid) {
		// find starting cell
		$row = $column = 1;
		while ($grid->getCell($row, $column)->isGiven()) {
			if (!$this->propagateGrid($row, $column)) break;
		}
		// start filling values until unsolvable or end of grid reached
		$i = 0;
		do {
			// if solution takes too long, call it unsolvable.
			// TODO: Investigate typical solution iterations for
			// the different difficulty levels, to better adjust this value.
			if ($i > 20000) {
				return null;
			}
			$cell = $grid->getCell($row, $column);
			if ($cell->isGiven()) continue;
			$possibleValues = $grid->getPossibleValues($row, $column, false);
			if (!count($possibleValues)) {
				// backtrack to last non-given cell with at least 2 possible values
				do {
					$cell = $grid->getCell($row, $column);
					if (!$cell->isGiven()) {
						$cell->setValue(0);
						$cell->setPossibleValues(array());
					}
					if (!$this->propagateGrid($row, $column, true)) {
						// grid is unsolvable
						return null;
					}
				}
				while ($grid->getCell($row, $column)->isGiven() || count($grid->getCell($row, $column)->getPossibleValues()) < 2);

				$cell = $grid->getCell($row, $column);
				$possibleValues = $cell->getPossibleValues($row, $column);
				$v = $cell->getValue();
				unset ($possibleValues[$v]);
				$cell->setPossibleValues($possibleValues);
				$cell->setValue(current($possibleValues));
			}
			else {
				$value = current($possibleValues);
				$cell->setValue($value);
			}
			$i++;
		}
		while ($this->propagateGrid($row, $column));

		return $grid;
	}

	/**
	 * Propagates through the grid, similar to the Propagator classes, but this
	 * is a specialized method with reversability.
	 *
	 * @param 	integer 	$row
	 * @param 	integer 	$column
	 * @param 	boolean 	$reverse
	 */
	protected function propagateGrid(&$row, &$column, $reverse = false) {
		if ($reverse) {
			if ($column > 1) {
				$column--;
				return true;
			}
			else if ($row > 1) {
				$row--;
				$column = SudokuGrid::GRID_SIZE;
				return true;
			}
			else {
				return false;
			}
		}
		else {
			if ($column < SudokuGrid::GRID_SIZE) {
				$column++;
				return true;
			}
			else if ($row < SudokuGrid::GRID_SIZE) {
				$row++;
				$column = 1;
				return true;
			}
			else {
				return false;
			}
		}
	}
}
