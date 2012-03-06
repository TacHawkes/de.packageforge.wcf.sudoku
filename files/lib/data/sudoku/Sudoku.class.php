<?php
namespace wcf\data\sudoku;
use wcf\data\DatabaseObject;

/**
 * Represents a sudoku.
 *
 * @author	Oliver Kliebisch
 * @copyright	2012 Oliver Kliebisch
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	de.packageforge.wcf.sudoku
 * @subpackage	data.sudoku
 * @category 	Community Framework
 */
class Sudoku extends DatabaseObject {
	/**
	 * @see	wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'sudoku';

	/**
	 * @see	wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'sudokuID';
}
