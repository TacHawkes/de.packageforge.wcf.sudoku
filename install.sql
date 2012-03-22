/**** tables ****/
DROP TABLE IF EXISTS wcf1_sudoku;
CREATE TABLE wcf1_sudoku (
  sudokuID int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  difficulty tinyint(1) NOT NULL DEFAULT '3',
  time int(10) NOT NULL DEFAULT '0',
  grid text,
  timesPlayed int(10) NOT NULL DEFAULT '0',
  lastPlayedTime int(10) NOT NULL DEFAULT '0',
  timesSolved int(10) NOT NULL DEFAULT '10'  
);

DROP TABLE IF EXISTS wcf1_sudoku_to_user;
CREATE TABLE wcf1_sudoku_to_user (
	sudokuID INT(10) NOT NULL,
	userID INT(10) NOT NULL,
	currentGrid text,
	timeElapsed int(10) NOT NULL DEFAULT '0'
	UNIQUE KEY sudokuID (sudokuID, userID)
);

/**** foreign keys ****/

ALTER TABLE wcf1_sudoku_to_user ADD FOREIGN KEY (sudokuID) REFERENCES wcf1_sudoku (sudokuID) ON DELETE CASCADE;
ALTER TABLE wcf1_sudoku_to_user ADD FOREIGN KEY (userID) REFERENCES wcf1_user (userID) ON DELETE CASCADE;