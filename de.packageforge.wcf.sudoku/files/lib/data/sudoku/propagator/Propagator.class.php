<?php
namespace wcf\data\sudoku\propagator;

/**
 * This defines the basic functionality of a propagator.
 *
 * @author	Oliver Kliebisch
 * @copyright	2012 Oliver Kliebisch
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf
 * @subpackage	data.sudoku.propagator
 * @category 	Community Framework
 */
interface Propagator{
	/**
	 * Moves to the next cell inside the grid
	 *
	 * @param integer $row
	 * @param integer $column
	 */
	public function propagate(&$row, &$column);
}