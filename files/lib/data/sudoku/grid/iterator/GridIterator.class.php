<?php
namespace wcf\data\sudoku\grid\iterator;

/**
 * This defines the basic functionality of a iterator.
 *
 * @author	Oliver Kliebisch
 * @copyright	2012 Oliver Kliebisch
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	de.packageforge.wcf.sudoku
 * @subpackage	data.sudoku.grid.iterator
 * @category 	Community Framework
 */
interface GridIterator{
	/**
	 * Moves to the next cell inside the grid
	 *
	 * @param integer $row
	 * @param integer $column
	 */
	public function iterate(&$row, &$column);
}