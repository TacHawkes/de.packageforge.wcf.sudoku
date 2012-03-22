<?php
namespace wcf\data\sudoku\grid;
use wcf\system\SingletonFactory;

/**
 * Solves a SudokuGrid by using a constraints subset backtracking algorithm.
 * This is an adapted 'kudoku' algorithm from 'Attractive Chaos' licensed under MIT license.
 * See: http://attractivechaos.wordpress.com/2011/06/19/an-incomplete-review-of-sudoku-solver-implementations/
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

	protected function updateStateVector($R, $C, &$sr, &$sc, $r, $v) {
		$min = 10;
		$minC = 0;

		for ($c2 = 0; $c2 < 4; ++$c2) {
			$sc[$C[$r][$c2]] += $v << 7;
		}
		for ($c2 = 0; $c2 < 4; ++$c2) {
			$r2; $rr; $cc2; $c = $C[$r][$c2];
			if ($v > 0) {
				for ($r2 = 0; $r2 < SudokuGrid::GRID_SIZE; ++$r2) {
					if ($sr[$rr = $R[$c][$r2]]++ != 0) continue;
					for ($cc2 = 0; $cc2 < 4; ++$cc2) {
						$cc = $C[$rr][$cc2];
						if (--$sc[$cc] < $min) {
							$min = $sc[$cc];
							$minC = $cc;
						}
					}
				}
			}
			else {
				for ($r2 = 0; $r2 < SudokuGrid::GRID_SIZE; ++$r2) {
					if (--$sr[$rr = $R[$c][$r2]] != 0) continue;
					$p = $C[$rr];
					++$sc[$p[0]];
					++$sc[$p[1]];
					++$sc[$p[2]];
					++$sc[$p[3]];
				}
			}
		}

		return $min << 16 | $minC;
	}

	protected function generateMatrix() {
		// index arrays, storing non-empty entries of the sparse matrix
		$R = $C = array();
		$i; $j; $r; $c; $c2;

		$s = SudokuGrid::GRID_SIZE;
		$b = SudokuGrid::BLOCK_SIZE;
		for ($i = $r = 0; $i < $s; ++$i) {
			for ($j = 0; $j < $s; ++$j) {
				for ($k = 0; $k < $s; ++$k) {
					$C[$r][0] = $s * $i + $j;
					$C[$r][1] = (floor($i/$b)*$b + floor($j / $b)) * $s + $k + $s*$s;
					$C[$r][2] = $s * $i + $k + 2*$s*$s;
					$C[$r][3] = $s * $j + $k + 3*$s*$s;
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

	protected function findSolutions($gridArray, $maxSolutions = 1) {
		$s = SudokuGrid::GRID_SIZE;
		$b = SudokuGrid::BLOCK_SIZE;
		$t = $this->generateMatrix();
		$R = $t[0];
		$C = $t[1];
		unset($t);

		$r; $c; $r2; $min; $cand; $dir; $hints = 0;
		$sr = $sc = $cr = $cc = $out = $ret = array();
		for ($r = 0; $r < 9*$s*$s; ++$r) $sr[$r] = 0;
		for ($c = 0; $c < 4*$s*$s; ++$c) $sc[$c] = $s;
		for ($i = 0; $i < $s*$s; ++$i) {
			$a = $gridArray[$i] - 1;
			if ($a >= 0) {
				$this->updateStateVector($R, $C, $sr, $sc, $i * $s + $a, 1);
				$hints++;
			}
			$cr[$i] = $cc[$i] = -1;
			$out[$i] = $a + 1;
		}
		for ($i = 0, $dir = 1, $cand = 10 << 16 | 0;;) {
			while ($i >= 0 && $i < $s*$s - $hints) {
				if ($dir == 1) {
					$min = $cand >> 16;
					$cc[$i] = $cand & 0xffff;
					if ($min > 1) {
						for ($c = 0; $c < 4 * $s*$s; ++$c) {
							if ($sc[$c] < $min) {
								$min = $sc[$c];
								$cc[$i] = $c;
								if ($min <= 1) break;
							}
						}
					}
					if ($min == 0 || $min == 10) $cr[$i--] = $dir = -1;
				}
				if ($i < 0) {
					break;
				}
				$c = $cc[$i];
				if ($dir == -1 && $cr[$i] >= 0) {
					$this->updateStateVector($R, $C, $sr, $sc, $R[$c][$cr[$i]], -1);
				}
				for ($r2 = $cr[$i] + 1; $r2 < $s; ++$r2) {
					if ($sr[$R[$c][$r2]] == 0) break;
				}
				if ($r2 < $s) {
					$cand = $this->updateStateVector($R, $C, $sr, $sc, $R[$c][$r2], 1);
					$cr[$i++] = $r2;
					$dir = 1;
				}
				else {
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