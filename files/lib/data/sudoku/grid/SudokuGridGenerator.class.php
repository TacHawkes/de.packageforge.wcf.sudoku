<?php
namespace wcf\data\sudoku\grid;
use wcf\data\sudoku\grid\iterator\JumpIterator;
use wcf\data\sudoku\grid\iterator\LRTBIterator;
use wcf\data\sudoku\grid\iterator\RandomIterator;
use wcf\data\sudoku\grid\iterator\SIterator;
use wcf\system\SingletonFactory;
use wcf\util\MathUtil;

/**
 * Generates sudoku grids. The math and thoughts behind this algorithm are inspired
 * by: http://zhangroup.aporc.org/images/files/Paper_3485.pdf
 *
 * @author	Oliver Kliebisch
 * @copyright	2012 Oliver Kliebisch
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf
 * @subpackage	data.sudoku.grid
 * @category 	Community Framework
 */
class SudokuGridGenerator extends SingletonFactory {
	/**
	 * Number of preplaced values for terminal pattern generation.
	 * This value has been found to be a balance between computation time
	 * and success rate. Increasing this value will lead to a lower computation
	 * time but the generation of grid will fail more often (solve will fail).
	 * Decreasing it, will have the opposite effect.
	 *
	 * @var integer
	 */
	const LAS_VEGAS_N = 11;

	/**
	 * The following constants are placeholders for the different difficulty levels.
	 */
	const DIFFICULTY_EXTREMELY_EASY = 1;
	const DIFFICULTY_EASY = 2;
	const DIFFICULTY_MEDIUM = 3;
	const DIFFICULTY_HARD = 4;
	const DIFFICULTY_EVIL = 5;

	/**
	 * Generates and returns a new playable sudoku grid with the desired difficulty.
	 *
	 * @param 	integer 	$difficulty
	 */
	public function generateSudokuGrid($difficulty = self::DIFFICULTY_MEDIUM) {
		$grid = null;

		// generate terminal pattern
		do {
			$grid = new SudokuGrid(null);
			$grid = $this->lasVegas($grid);
			$grid = SudokuGridSolver::getInstance()->solve($grid);
		}
		while ($grid === null);

		// set up restrictions and iterator according to difficulty
		$iterator = null;
		$remainingCellRestriction = 0;
		$lowerRowColumnRestriction = 0;
		switch ($difficulty) {
			case self::DIFFICULTY_EXTREMELY_EASY:
				$remainingCellRestriction = MathUtil::getRandomValue(50, 60);
				$lowerRowColumnRestriction = 5;
				$iterator = new RandomIterator();
				break;
			case self::DIFFICULTY_EASY:
				$remainingCellRestriction = MathUtil::getRandomValue(36, 49);
				$lowerRowColumnRestriction = 4;
				$iterator = new RandomIterator();
				break;
			case self::DIFFICULTY_MEDIUM:
				$remainingCellRestriction = MathUtil::getRandomValue(32, 35);
				$lowerRowColumnRestriction = 3;
				$iterator = new JumpIterator();
				break;
			case self::DIFFICULTY_HARD:
				$remainingCellRestriction = MathUtil::getRandomValue(28, 31);
				$lowerRowColumnRestriction = 2;
				$iterator = new SIterator();
				break;
			case self::DIFFICULTY_EVIL:
				$remainingCellRestriction = MathUtil::getRandomValue(22, 27);
				$lowerRowColumnRestriction = 0;
				$iterator = new LRTBIterator();
				break;
		}

		// start minecraft... erm... digging
		$row = MathUtil::getRandomValue(1, SudokuGrid::GRID_SIZE);
		$column = MathUtil::getRandomValue(1, SudokuGrid::GRID_SIZE);
		$cellCount = SudokuGrid::GRID_SIZE * SudokuGrid::GRID_SIZE;
		$terminalPattern = $grid->deepCopy();
		do {
			$cell = $grid->getCell($row, $column);
			if ($cell->canBeDug()) {
				// check restrictions
				// not enough cells to dig another one. quit loop
				if ($cellCount <= $remainingCellRestriction)  {
					break;
				}

				// check if this particular cell can be dug
				$cellsInRow = 0;
				$cellsInColumn = 0;
				for ($i = 1; $i <= SudokuGrid::GRID_SIZE; $i++) {
					if ($grid->getCell($i, $column)->getValue() > 0) {
						$cellsInColumn++;
					}
					if ($grid->getCell($row, $i)->getValue() > 0) {
						$cellsInRow++;
					}
				}

				// not enough cells to dig another one
				if ($cellsInRow <= $lowerRowColumnRestriction || $cellsInColumn <= $lowerRowColumnRestriction) {
					$cell->toggleCanBeDug();
				}
				else {
					// restrictions are not violated, try if digging yields a unique solution
					$uniqueSolution = true;
					$possibleValues = $grid->getPossibleValues($row, $column, false);
					foreach ($possibleValues as $value) {
						if ($value != $cell->getValue()) {
							$testGrid = $grid->deepCopy();
							$testGrid->getCell($row, $column)->setValue($value);
							$testGrid->makeValuesGiven();
							$testGrid = SudokuGridSolver::getInstance()->solve($testGrid);
							if ($testGrid !== null) {
								$uniqueSolution = false;
								$cell->toggleCanBeDug();
								break;
							}
						}
					}

					// dig cell if unique solution exists
					if ($uniqueSolution) {
						$grid->getCell($row, $column)->setValue(0);
						$grid->getCell($row, $column)->toggleCanBeDug();
						$cellCount--;
					}
				}
			}

			// iterate through the grid
			$iterator->iterate($row, $column);
		}
		while ($grid->hasDiggableCells());

		// do some random permutations so the grid looks more interesting
		$permutations = array('digitExchange', 'columnExchange', 'blockColumnExchange', 'transpose');
		for ($i = 0; $i < 10; $i++) {
			$action = array_rand(array_flip($permutations));
			$this->{$action}($grid);
		}

		$grid->makeValuesGiven();

		return $grid;
	}

	/**
	 * Exchanges two digits within the grid. This is an allowed permutation, which has
	 * no effect on the difficulty or feasibility of the grid.
	 *
	 * @param 	SudokuGrid 	$grid
	 */
	protected function digitExchange($grid) {
		$digit1 = MathUtil::getRandomValue(1, SudokuGrid::GRID_SIZE);
		do {
			$digit2 = MathUtil::getRandomValue(1, SudokuGrid::GRID_SIZE);
		}
		while ($digit2 == $digit1);

		for ($i = 1; $i <= SudokuGrid::GRID_SIZE; $i++) {
			for ($j = 1; $j <= SudokuGrid::GRID_SIZE; $j++) {
				if ($grid->getCell($i, $j)->getValue() == $digit1) {
					$grid->getCell($i, $j)->setValue($digit2);
					continue;
				}
				if ($grid->getCell($i, $j)->getValue() == $digit2) {
					$grid->getCell($i, $j)->setValue($digit1);
				}
			}
		}
	}

	/**
	 * Exchanges two columns within a block range. This is an allowed permutation, which has
	 * no effect on the difficulty or feasibility of the grid.
	 *
	 * @param 	SudokuGrid 	$grid
	 */
	protected function columnExchange($grid) {
		$blocksPerRow = SudokuGrid::GRID_SIZE / SudokuGrid::BLOCK_SIZE;
		$list = array();

		for ($i = 1; $i < SudokuGrid::GRID_SIZE - SudokuGrid::BLOCK_SIZE + 1; $i += $blocksPerRow) {
			$list[] = $i;
		}

		$column = array_rand(array_flip($list));
		$tempGrid = $grid->deepCopy();

		for ($i = 1; $i <= SudokuGrid::GRID_SIZE; $i++) {
			$grid->getCell($i, $column)->setValue($tempGrid->getCell($i, $column + SudokuGrid::BLOCK_SIZE - 1)->getValue());
			$grid->getCell($i, $column + SudokuGrid::BLOCK_SIZE - 1)->setValue($tempGrid->getCell($i, $column)->getValue());
		}
	}

	/**
	 * Exchanges two columns of blocks within the grid. This is an allowed permutation, which has
	 * no effect on the difficulty or feasibility of the grid.
	 *
	 * @param 	SudokuGrid 	$grid
	 */
	protected function blockColumnExchange($grid) {
		$blocksPerRow = SudokuGrid::GRID_SIZE / SudokuGrid::BLOCK_SIZE;
		$block1 = MathUtil::getRandomValue(1, $blocksPerRow);
		do {
			$block2 = MathUtil::getRandomValue(1, $blocksPerRow);
		}
		while ($block1 == $block2);

		$tempGrid = $grid->deepCopy();

		for ($i = 1; $i <= SudokuGrid::GRID_SIZE; $i++) {
			for ($j = 1; $j <= SudokuGrid::BLOCK_SIZE; $j++) {
				$block1Index = ($block1 - 1)*SudokuGrid::BLOCK_SIZE + $j;
				$block2Index = ($block2 - 1)*SudokuGrid::BLOCK_SIZE + $j;
				$grid->getCell($i, $block1Index)->setValue($tempGrid->getCell($i, $block2Index)->getValue());
				$grid->getCell($i, $block2Index)->setValue($tempGrid->getCell($i, $block1Index)->getValue());
			}
		}
	}

	/**
	 * Transposes the whole grid (equivalent to a matrix transposition or rotating the grid by 90 degrees).
	 * This is an allowed permutation, which has no effect on the difficulty or feasibility of the grid.
	 *
	 * @param 	SudokuGrid 	$grid
	 */
	protected function transpose($grid) {
		$tempGrid = $grid->deepCopy();

		for ($i = 1; $i <= SudokuGrid::GRID_SIZE; $i++) {
			for ($j = 1; $j <= SudokuGrid::GRID_SIZE; $j++) {
				$grid->getCell($i, $j)->setValue($tempGrid->getCell($j, $i)->getValue());
			}
		}
	}

	/**
	 * Fills the empty grid with some random variables in compliance to the game rules.
	 *
	 * @param 	SudokuGrid 	$grid
	 */
	protected function lasVegas(SudokuGrid $grid) {
		for ($i = 0; $i < self::LAS_VEGAS_N; $i++) {
			do {
				$row = MathUtil::getRandomValue(1, SudokuGrid::GRID_SIZE);
				$column = MathUtil::getRandomValue(1, SudokuGrid::GRID_SIZE);
			}
			while ($grid->getCell($row, $column)->getValue() > 0);

			$cell = $grid->getCell($row, $column);
			$possibleValues = $grid->getPossibleValues($row, $column);
			$cell->setValue(array_rand($possibleValues), true);
		}

		return $grid;
	}
}