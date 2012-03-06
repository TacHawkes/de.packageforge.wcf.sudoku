<?php
namespace wcf\data\sudoku\grid\iterator;
use wcf\data\sudoku\grid\SudokuGrid;
use wcf\util\MathUtil;

/**
 * Implementation of iterator. Randomly jumps around the grid.
 *
 * @author	Oliver Kliebisch
 * @copyright	2012 Oliver Kliebisch
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	de.packageforge.wcf.sudoku
 * @subpackage	data.sudoku.iterator
 * @category 	Community Framework
 */
class RandomIterator implements GridIterator {

	public function iterate(&$row, &$column) {
		$row = MathUtil::getRandomValue(1, SudokuGrid::GRID_SIZE);
		$column = MathUtil::getRandomValue(1, SudokuGrid::GRID_SIZE);
	}
}