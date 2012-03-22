{include file='documentHeader'}

<head>
	<title>Sudoku page</title>
	{include file='headInclude' sandbox=false}
	<style type="text/css">
		.sudokuTable {
  			border-collapse: collapse;
  			border: solid thick;
  			width: auto !important;
  			margin: 0 auto;
  			font-size: 2em;
  		}
  		.sudokuTable colgroup, .sudokuTable tbody {
  			border: solid medium;
  		}

  		.sudokuTable tr:nth-child(2n+1) td {
			background-color: white;
		}
  		.sudokuTable td {
  			border: solid thin; height: 1.4em; width: 1.4em; text-align: center; padding: 0;
  			border-right-color: black !important;
  			background-color: white;
  		}

  		.wcf-sudokuGivenValue {
  			background-color: darkGray !important;
  			font-weight: bold;
  		}

  		.wcf-sudokuSelectedCell {
			border-width: thick !important;
			border-color: black !important;
  		}

  		.clear {
  			width: 65px;
  		}
	</style>

	<script type="text/javascript" src="{@$__wcf->getPath('wcf')}js/WCF.Sudoku.js"></script>
	<script type="text/javascript">
		//<![CDATA[
		$(function() {
			WCF.TabMenu.init();
			new WCF.Sudoku.Table('#sudokuTable1');
		});
		//]]>
	</script>
</head>

<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>

{capture assign='sidebar'}
<nav id="sidebarContent" class="wcf-sidebarContent">
	<div>
		<fieldset>
			<legend>Wie wird gespielt?</legend>

			<p>Das Ziel des Spiels ist es das links stehende Tableau bestehend aus 9x9 Felder so mit Zahlen zu füllen, dass in jedem 3x3 Block, in jeder Zeile und in jeder Spalte die Zahlen von 1 bis 9 genau einmal vorkommen.</p>
		</fieldset>

		<fieldset>
			<legend>Tipps</legend>


		</fieldset>
	</div>
	<span class="wcf-collapsibleSidebarButton" title="{lang}wcf.global.button.collapsible{/lang}"><span></span></span>
</nav>
{/capture}

{include file='header' sandbox=false sidebarOrientation='right'}

<header class="wcf-container wcf-mainHeading">
	<img src="{icon size='L'}sudoku{/icon}" alt="" class="wcf-containerIcon" />
	<hgroup class="wcf-containerContent">
		<h1>Sudoku</h1>
	</hgroup>
</header>

<div class="wcf-tabMenuContainer" data-active="game">
	<nav class="wcf-tabMenu">
		<ul>
			<li><a href="#game" title="game">{lang}wcf.sudoku.game{/lang}</a></li>
		</ul>
	</nav>

	<div id="game" class="wcf-border wcf-tabMenuContent">
		<hgroup class="wcf-subHeading">
			<h1>{lang}wcf.sudoku.newGame{/lang}</h1>
		</hgroup>
		<ol class="wcf-applicationList">
			<li class="wcf-infoPackageApplication">
				<fieldset>
					<legend>Easy</legend>
 			<table class="sudokuTable" id="sudokuTable1">
  				<colgroup><col><col><col></colgroup>
  				<colgroup><col><col><col></colgroup>
  				<colgroup><col><col><col></colgroup>
  				{foreach from=$grid->getGridData() key=rowNumber item=row}
  					{if $rowNumber % 3 == 0}<tbody>{/if}
  					<tr>
  					{foreach from=$row key=columnNumber item=column}
						<td id="sudokuCell-{@$rowNumber + 1}-{@$columnNumber + 1}"{if $column->isGiven()} class="wcf-sudokuGivenValue"{/if}> <span class="wcf-sudokuValue">{if $column->getValue() > 0}{$column->getValue()}{/if}</span> </td>
  					{/foreach}
  					</tr>
  					{if ($rowNumber + 1) % 3 ==0}</tbody>{/if}
  				{/foreach}
 			</table>

 			<footer>
				<nav>
					<ul class="wcf-smallButtons">
						<li><a href="{link controller='Sudoku'}action=newSudokus{/link}" class="wcf-button"><img src="{@RELATIVE_WCF_DIR}icon/update1.svg" alt="" title="{lang}wcf.sudoku.button.newSudokus{/lang}" /> <span>{lang}wcf.sudoku.button.newSudokus{/lang}</span></a></li>
					</ul>
				</nav>
			</footer>
				</fieldset>
			</li>
		</ol>

 		<hgroup class="wcf-subHeading">
			<h1>{lang}wcf.sudoku.savedGames{/lang}</h1>
		</hgroup>

	</div>
</div>

{include file='footer' sandbox=false}

</body>
</html>
