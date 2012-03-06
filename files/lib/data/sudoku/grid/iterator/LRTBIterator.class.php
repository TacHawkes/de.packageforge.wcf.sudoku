<?php
namespace wcf\data\sudoku\grid\iterator;
use wcf\data\sudoku\grid\SudokuGrid;
use wcf\util\MathUtil;

/**
 * Implementation of iterator. Goes from left to right first and then from top to bottom.
 *
 * @author	Oliver Kliebisch
 * @copyright	2012 Oliver Kliebisch
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	de.packageforge.wcf.sudoku
 * @subpackage	data.sudoku.iterator
 * @category 	Community Framework
 */
class LRTBIterator implements GridIterator {

	public function iterate(&$row, &$column) {
		if ($column < SudokuGrid::GRID_SIZE) {
			$column++;
		}
		else if ($row < SudokuGrid::GRID_SIZE) {
			$row++;
			$column = 1;
		}
		else {
			$row = MathUtil::getRandomValue(1, SudokuGrid::GRID_SIZE);
			$column = MathUtil::getRandomValue(1, SudokuGrid::GRID_SIZE);
		}
	}
}