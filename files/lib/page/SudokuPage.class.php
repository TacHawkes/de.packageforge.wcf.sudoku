<?php
namespace wcf\page;
use wcf\data\sudoku\SudokuGridGenerator;

use wcf\data\sudoku\SudokuGrid;

use wcf\data\sudoku\SudokuGridSolver;

use wcf\util\StringUtil;

use wcf\system\acl\ACLHandler;

use wcf\system\menu\page\PageMenu;
use wcf\system\WCF;
use wcf\system\event\EventHandler;

/**
 * Shows the sudoku page.
 *
 * @author	Oliver Kliebisch
 * @copyright	2012 Oliver Kliebisch
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	de.packageforge.wcf.sudoko
 * @subpackage	page
 * @category	Community Framework
 */
class SudokuPage extends AbstractPage {
	/**
	 * @see wcf\page\AbstractPage::readData()
	 */
	public function readData() {
		parent::readData();

	}

	/**
	 * @see wcf\page\AbstractPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();

		$grid = array(
			0 => array(
				0 => 0,
				1 => 9,
				2 => 0,
				3 => 6,
				4 => 0,
				5 => 1,
				6 => 0,
				7 => 0,
				8 => 0
				),
			1 => array(
				0 => 0,
				1 => 0,
				2 => 0,
				3 => 0,
				4 => 3,
				5 => 0,
				6 => 9,
				7 => 0,
				8 => 1
				),
			2 => array(
				0 => 0,
				1 => 3,
				2 => 0,
				3 => 2,
				4 => 0,
				5 => 8,
				6 => 0,
				7 => 0,
				8 => 0
				),
			3 => array(
				0 => 7,
				1 => 0,
				2 => 9,
				3 => 0,
				4 => 0,
				5 => 0,
				6 => 0,
				7 => 0,
				8 => 4
				),
			4 => array(
				0 => 0,
				1 => 4,
				2 => 0,
				3 => 3,
				4 => 0,
				5 => 7,
				6 => 0,
				7 => 9,
				8 => 0
				),
			5 => array(
				0 => 8,
				1 => 0,
				2 => 3,
				3 => 0,
				4 => 1,
				5 => 0,
				6 => 5,
				7 => 0,
				8 => 7
				),
			6 => array(
				0 => 0,
				1 => 5,
				2 => 0,
				3 => 7,
				4 => 0,
				5 => 2,
				6 => 0,
				7 => 1,
				8 => 0
				),
			7 => array(
				0 => 9,
				1 => 0,
				2 => 4,
				3 => 0,
				4 => 5,
				5 => 0,
				6 => 7,
				7 => 0,
				8 => 6
				),
			8 => array(
				0 => 0,
				1 => 1,
				2 => 0,
				3 => 9,
				4 => 0,
				5 => 6,
				6 => 0,
				7 => 5,
				8 => 8
				),
		);
		// $grid = SudokuGridSolver::getInstance()->solve($grid);
		$grid = SudokuGridGenerator::getInstance()->generateSudokuGrid();

		WCF::getTPL()->assign(array(
			'grid' => $grid
		));
	}

	/**
	 * @see wcf\page\Page::show()
	 */
	public function show() {
		PageMenu::getInstance()->setActiveMenuItem('wcf.header.menu.sudoku');

		parent::show();
	}
}