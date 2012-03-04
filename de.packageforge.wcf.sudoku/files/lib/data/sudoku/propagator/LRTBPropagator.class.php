<?php
namespace wcf\data\sudoku\propagator;
use wcf\util\MathUtil;

use wcf\data\sudoku\SudokuGrid;

/**
 * Implementation of propagator. Goes from left to right first and then from top to bottom.
 *
 * @author	Oliver Kliebisch
 * @copyright	2012 Oliver Kliebisch
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf
 * @subpackage	data.sudoku.propagator
 * @category 	Community Framework
 */
class LRTBPropagator implements Propagator {

	public function propagate(&$row, &$column) {
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