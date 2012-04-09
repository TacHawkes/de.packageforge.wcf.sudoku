<?php
namespace wcf\data\sudoku\grid;
use wcf\system\SingletonFactory;

/**
 * Solves a SudokuGrid by using a constraints subset backtracking algorithm.
 * This is an adapted 'kudoku' algorithm from 'Attractive Chaos' licensed under MIT license.
 * See: http://attractivechaos.wordpress.com/2011/06/19/an-incomplete-review-of-sudoku-solver-implementations/
 * Comments partially taken from the original C-implementation: https://raw.github.com/attractivechaos/plb/master/sudoku/sudoku_v1.c
 *
 * @author	Attractive Chaos, Oliver Kliebisch
 * @copyright	2011 Attractive Chaos, 2012 Oliver Kliebisch
 * @license	MIT License, GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.woltlab.wcf
 * @subpackage	data.sudoku.grid
 * @category 	Community Framework
 */
class SudokuGridSolver extends SingletonFactory {

	/**
	 * Solves the given sudoku.
	 *
	 * @param SudokuGrid $grid
	 */
	public function solve(SudokuGrid $grid) {
		// primitive datatypes are faster for solving
		$g = array();
		for ($i = 0; $i < SudokuGrid::GRID_SIZE; $i++) {
			for ($j = 0; $j < SudokuGrid::GRID_SIZE; $j++) {
				$g[] = $grid->getCell($i + 1, $j + 1)->getValue();
			}
		}

		$solutions = $this->findSolutions($g);

		if (!count($solutions)) return null;

		foreach ($solutions as $solution) {
			for ($i = 0; $i < SudokuGrid::GRID_SIZE; $i++) {
				for ($j = 0; $j < SudokuGrid::GRID_SIZE; $j++) {
					if (!$grid->getCell($i + 1, $j +1)->isGiven()) {
						$grid->getCell($i + 1, $j + 1)->setValue($solution[$i*SudokuGrid::GRID_SIZE + $j]);
					}
				}
			}
		}

		return $grid;
	}


	/**
	 * Update the state vectors when we pick up choice r; v=1 for setting choice; v=-1 for reverting
	 *
	 * @param array $R
	 * @param array $C
	 * @param array $sr
	 * @param array $sc
	 * @param integer $r
	 * @param integer $v
	 */
	private function updateStateVector($R, $C, &$sr, &$sc, $r, $v) {
		$min = 10;
		$minC = 0;

		for ($c2 = 0; $c2 < 4; ++$c2) {
			$sc[$C[$r][$c2]] += $v << 7;
		}
		for ($c2 = 0; $c2 < 4; ++$c2) { // update # available choices
			$r2; $rr; $cc2; $c = $C[$r][$c2];
			if ($v > 0) { // move forward
				for ($r2 = 0; $r2 < SudokuGrid::GRID_SIZE; ++$r2) {
					if ($sr[$rr = $R[$c][$r2]]++ != 0) continue; // update the row status
					for ($cc2 = 0; $cc2 < 4; ++$cc2) {
						$cc = $C[$rr][$cc2];
						if (--$sc[$cc] < $min) { // update # allowed choices
							// register the minimum number
							$min = $sc[$cc];
							$minC = $cc;
						}
					}
				}
			}
			else { // revert
				for ($r2 = 0; $r2 < SudokuGrid::GRID_SIZE; ++$r2) {
					// update the row status
					if (--$sr[$rr = $R[$c][$r2]] != 0) continue;
					$p = $C[$rr];
					// update the count array
					++$sc[$p[0]];
					++$sc[$p[1]];
					++$sc[$p[2]];
					++$sc[$p[3]];
				}
			}
		}

		// return the col that has been modified and with the minimal available choices
		return $min << 16 | $minC;
	}

	/**
	 * Generate the sparse representation of the binary matrix
	 */
	private function generateMatrix() {
		// index arrays, storing non-empty entries of the sparse matrix
		$R = $C = array();
		$i; $j; $r; $c; $c2;

		$s = SudokuGrid::GRID_SIZE;
		$b = SudokuGrid::BLOCK_SIZE;
		for ($i = $r = 0; $i < $s; ++$i) {
			for ($j = 0; $j < $s; ++$j) {
				for ($k = 0; $k < $s; ++$k) {
					$C[$r][0] = $s * $i + $j; // row-column constraint
					$C[$r][1] = (floor($i/$b)*$b + floor($j / $b)) * $s + $k + $s*$s; // box-number constraint
					$C[$r][2] = $s * $i + $k + 2*$s*$s; // row-number constraint
					$C[$r][3] = $s * $j + $k + 3*$s*$s; // col-number constraint
					++$r;
				}
			}
		}

		for ($c = 0; $c < 3*$s*$s; ++$c) {
			$R[$c] = array();
		}

		for ($r = 0; $r < 9*$s*$s; ++$r) {
			for ($c2 = 0; $c2 < 4; ++$c2) {
				$R[$C[$r][$c2]][] = $r;
			}
		}

		return array($R, $C);
	}

	/**
	 * Solves the given grid. maxSolutions allows to define
	 * a limit if only a certain amount of solutions is neccessary.
	 *
	 * For Sudoku, there are 9x9x9=729 possible choices (9 numbers to choose for
	 *  each cell in a 9x9 grid), and 4x9x9=324 constraints with each constraint
   	 * representing a set of choices that are mutually conflictive with each other.
   	 * The 324 constraints are classified into 4 categories:
   	 *
      	 * 1. row-column where each cell contains only one number
      	 * 2. box-number where each number appears only once in one 3x3 box
      	 * 3. row-number where each number appears only once in one row
      	 * 4. col-number where each number appears only once in one column
   	 *
      	 * Each category consists of 81 constraints. We number these constraints from 0
      	 * to 323. In this program, for example, constraint 0 requires that the (0,0)
      	 * cell contains only one number; constraint 81 requires that number 1 appears
      	 * only once in the upper-left 3x3 box; constraint 162 requires that number 1
      	 * appears only once in row 1; constraint 243 requires that number 1 appears
      	 * only once in column 1.
   	 *
      	 * Noting that a constraint is a subset of choices, we may represent a
      	 * constraint with a binary vector of 729 elements. Thus we have a 729x324
      	 * binary matrix M with M(r,c)=1 indicating the constraint c involves choice r.
      	 * Solving a Sudoku is reduced to finding a subset of choices such that no
      	 * choices are present in the same constaint. This is equivalent to finding the
      	 * minimal subset of choices intersecting all constraints, a minimum hitting
      	 * set problem or a eqivalence of the exact cover problem.
   	 *
      	 * The 729x324 binary matrix is a sparse matrix, with each row containing 4
      	 * non-zero elements and each column 9 non-zero elements. In practical
      	 * implementation, we store the coordinate of non-zero elements instead of
      	 * the binary matrix itself. We use a binary row vector to indicate the
      	 * constraints that have not been used and use a column vector to keep the
      	 * number of times a choice has been forbidden. When we set a choice, we will
      	 * use up 4 constraints and forbid other choices in the 4 constraints. When we
      	 * make wrong choices, we will find an unused constraint with all choices
      	 * forbidden, in which case, we have to backtrack to make new choices. Once we
      	 * understand what the 729x324 matrix represents, the backtracking algorithm
      	 * itself is easy.
   	 *
      	 * A major difference between the algorithm implemented here and Guenter
      	 * Stertenbrink's suexco.c lies in how to count the number of the available
      	 * choices for each constraint. Suexco.c computes the count with a loop, while
      	 * the algorithm here keeps the count in an array. The latter is a little more
      	 * complex to implement as we have to keep the counts synchronized all the time,
      	 * but it is 50-100% faster, depending on the input.
	 *
	 * @param 	array 		$gridArray
	 * @param 	integer 	$maxSolutions
	 * @return	array
	 */
	protected function findSolutions($gridArray, $maxSolutions = 1) {
		$s = SudokuGrid::GRID_SIZE;
		$b = SudokuGrid::BLOCK_SIZE;
		$t = $this->generateMatrix();
		$R = $t[0];
		$C = $t[1];
		unset($t);

		$r; $c; $r2; $min; $cand; $dir; $hints = 0; // dir=1: forward; dir=-1: backtrack
		$sr = $sc = $cr = $cc = $out = $ret = array(); // sr[r]: # times the row is forbidden by others; cr[i]: row chosen at step i
		for ($r = 0; $r < 9*$s*$s; ++$r) $sr[$r] = 0; // no row is forbidden
		for ($c = 0; $c < 4*$s*$s; ++$c) $sc[$c] = $s; // 9 allowed choices; no constraint has been used
		for ($i = 0; $i < $s*$s; ++$i) {
			$a = $gridArray[$i] - 1; // number from -1 to 8
			if ($a >= 0) {
				// set the choice
				$this->updateStateVector($R, $C, $sr, $sc, $i * $s + $a, 1);
				// count the number of hints
				$hints++;
			}
			$cr[$i] = $cc[$i] = -1;
			$out[$i] = $a + 1;
		}
		for ($i = 0, $dir = 1, $cand = 10 << 16 | 0;;) {
			while ($i >= 0 && $i < $s*$s - $hints) { // maximum 81-hints steps
				if ($dir == 1) {
					$min = $cand >> 16;
					$cc[$i] = $cand & 0xffff;
					if ($min > 1) {
						for ($c = 0; $c < 4 * $s*$s; ++$c) {
							if ($sc[$c] < $min) {
								$min = $sc[$c];
								// choose the top constraint
								$cc[$i] = $c;
								// this is for acceleration; slower without this line
								if ($min <= 1) break;
							}
						}
					}
					// backtrack
					if ($min == 0 || $min == 10) $cr[$i--] = $dir = -1;
				}
				if ($i < 0) {
					break;
				}
				$c = $cc[$i];
				if ($dir == -1 && $cr[$i] >= 0) {
					// revert the choice
					$this->updateStateVector($R, $C, $sr, $sc, $R[$c][$cr[$i]], -1);
				}
				// search for the choice to make
				for ($r2 = $cr[$i] + 1; $r2 < $s; ++$r2) {
					// found if the state equals 0
					if ($sr[$R[$c][$r2]] == 0) break;
				}
				if ($r2 < $s) {
					// set the choice
					$cand = $this->updateStateVector($R, $C, $sr, $sc, $R[$c][$r2], 1);
					// moving forward
					$cr[$i++] = $r2;
					$dir = 1;
				}
				else {
					// backtrack
					$cr[$i--] = $dir = -1;
				}
			}
			if ($i < 0) break;
			$y = array();
			for ($j = 0; $j < $s*$s; ++$j) $y[$j] = $out[$j];
			for ($j = 0; $j < $i; ++$j) {
				$r = $R[$cc[$j]][$cr[$j]];
				$y[floor($r/$s)] = $r % $s + 1;
			}
			$ret[] = $y;
			if (count($ret) >= $maxSolutions) break;
			--$i;
			$dir = -1;
		}

		return $ret;
	}
}