<?php
namespace wcf\data\sudoku\propagator;
use wcf\data\sudoku\SudokuGrid;
use wcf\util\MathUtil;

/**
 * Implementation of propagator. Randomly jumps around the grid.
 *
 * @author	Oliver Kliebisch
 * @copyright	2012 Oliver Kliebisch
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf
 * @subpackage	data.sudoku.propagator
 * @category 	Community Framework
 */
class RandomPropagator implements Propagator {

	public function propagate(&$row, &$column) {
		$row = MathUtil::getRandomValue(1, SudokuGrid::GRID_SIZE);
		$column = MathUtil::getRandomValue(1, SudokuGrid::GRID_SIZE);
	}
}