<?php
namespace wcf\data\sudoku;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of sudokus.
 *
 * @author	Oliver Kliebisch
 * @copyright	2012 Oliver Kliebisch
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf
 * @subpackage	data.sudoku
 * @category 	Community Framework
 */
class SudokuList extends DatabaseObjectList {
	/**
	 * @see	wcf\data\DatabaseObjectList::$className
	 */
	public $className = 'wcf\data\sudoku\Sudoku';
}
