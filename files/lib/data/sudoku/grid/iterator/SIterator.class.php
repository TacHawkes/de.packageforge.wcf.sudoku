<?php
namespace wcf\data\sudoku\grid\iterator;
use wcf\data\sudoku\grid\SudokuGrid;
use wcf\util\MathUtil;

/**
 * Implementation of iterator. Goes across the grid in an "S"-shape.
 *
 * @author	Oliver Kliebisch
 * @copyright	2012 Oliver Kliebisch
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	de.packageforge.wcf.sudoku
 * @subpackage	data.sudoku.iterator
 * @category 	Community Framework
 */
class SIterator implements GridIterator {

	public function iterate(&$row, &$column) {
		if (($row % 2 == 0 && $column > 1) || ($row % 2 != 0 && $column < SudokuGrid::GRID_SIZE)) {
			$column += ($row % 2 == 0) ? -1 : 1;
		}
		else if ($row < SudokuGrid::GRID_SIZE) {
			$row++;
		}
		else {
			$row = MathUtil::getRandomValue(1, SudokuGrid::GRID_SIZE);
			$column = MathUtil::getRandomValue(1, SudokuGrid::GRID_SIZE);
		}
	}
}