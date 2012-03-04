<?php
namespace wcf\data\sudoku\propagator;
use wcf\util\MathUtil;

use wcf\data\sudoku\SudokuGrid;

/**
 * Implementation of propagator. Jumps two fields in an even or uneven manner across the grid.
 *
 * @author	Oliver Kliebisch
 * @copyright	2012 Oliver Kliebisch
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf
 * @subpackage	data.sudoku.propagator
 * @category 	Community Framework
 */
class JumpPropagator implements Propagator {

	public function propagate(&$row, &$column) {
		$evenRow = $row % 2 == 0;
		$evenColumn = $column % 2 == 0;
		if ((!$evenRow && !$evenColumn) && $column < SudokuGrid::GRID_SIZE - 1) {
			$column += 2;
		}
		else if (($evenRow && !$evenColumn) && $column > 3) {
			$column -= 2;
		}
		else if ((!$evenRow && $evenColumn) && $column < SudokuGrid::GRID_SIZE - 2) {
			$column += 2;
		}
		else if (($evenRow && $evenColumn) && $column > 2) {
			$column -= 2;
		}
		else if ($row < SudokuGrid::GRID_SIZE) {
			$row++;
			$evenRow = $row % 2 == 0;
			if (($evenRow && !$evenColumn)) {
				$column == SudokuGrid::GRID_SIZE - 1;
			}
			else if (($evenRow && $evenColumn)) {
				$column == SudokuGrid::GRID_SIZE;
			}
			else if (!$evenRow && !$evenColumn) {
				$column = 1;
			}
			else if (!$evenRow && $evenColumn) {
				$column = 2;
			}
		}
		else {
			$row = MathUtil::getRandomValue(1, SudokuGrid::GRID_SIZE);
			$column = MathUtil::getRandomValue(1, SudokuGrid::GRID_SIZE);
		}
	}
}