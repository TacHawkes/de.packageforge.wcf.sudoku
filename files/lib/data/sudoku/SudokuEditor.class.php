<?php
namespace wcf\data\sudoku;
use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit sudokus.
 *
 * @author	Oliver Kliebisch
 * @copyright	2012 Oliver Kliebisch
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf
 * @subpackage	data.sudoku
 * @category 	Community Framework
 */
class SudokuEditor extends DatabaseObjectEditor {
	/**
	 * @see	wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = 'wcf\data\spider\Sudoku';
}
