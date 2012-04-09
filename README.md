Sudoku 1.0 pre-development
===============================

This is a simple sudoku game for WoltLab Community Framework 2.0.

Version notes
-------------

The current state is just an early implementation of a solver and generator with a simple proof-of-concept output. The frontend is meant
to just show 'something'. It will be completely reworked as soon as the framework reaches a more stable state and standard templates will not
change anymore. For now it demonstrates a working solver and generator for sudokus. Next implementation steps require proper database object handling
regarding the storage of game states and player sessions.

Contribution
------------

Every developer is welcome to provide bug fixes or enhancements. The usual WCF code style rules apply (taken from the WCF 2.0 readme):

* Testing is key, you MUST try out your changes before submitting pull requests
* You MUST save your files with Unix-style line endings (\n)
* You MUST NOT include the closing tag of a PHP block at the end of file, provide an empty newline instead
* You MUST use tabs for indentation
    * Tab size of 8 is required
    * Empty lines MUST be indented equal to previous line
* All comments within source code MUST be written in English language

Follow the above conventions if you want your pull requests accepted.

License
-------

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public License
as published by the Free Software Foundation; either version 2.1
of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA