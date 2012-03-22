<?php
namespace wcf\data\sudoku;
use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes sudoku-related actions.
 *
 * @author	Oliver Kliebisch
 * @copyright	2012 Oliver Kliebisch
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf
 * @subpackage	data.sudoku
 * @category 	Community Framework
 */
class SudokuAction extends AbstractDatabaseObjectAction {
	/**
	 * @see wcf\data\AbstractDatabaseObjectAction::$className
	 */
	protected $className = 'wcf\data\spider\SudokuEditor';
}
