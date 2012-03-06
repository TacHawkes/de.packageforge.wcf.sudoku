{include file='documentHeader'}

<head>
	<title>Sudoku page</title>
	{include file='headInclude' sandbox=false}

	<script type="text/javascript">
		//<![CDATA[
		$(function() {
			WCF.TabMenu.init();
		});
		//]]>
	</script>
</head>

<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>

{capture assign='sidebar'}
<nav id="sidebarContent" class="wcf-sidebarContent">
	<div>
		<fieldset>
			<legend>sort</legend>


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

<div class="wcf-tabMenuContainer">
	<nav class="wcf-tabMenu">
		<ul>
			<li><a href="#game" title="{lang}wcf.sudoku.game{/lang}">{lang}wcf.sudoku.game{/lang}</a></li>
		</ul>
	</nav>

	<div id="game" class="wcf-border wcf-tabMenuContent" data-menu-item="game">
		<hgroup class="wcf-subHeading">
			<h1>{lang}wcf.sudoku.newGame{/lang}</h1>
		</hgroup>
		<ol class="wcf-applicationList">
			<li class="wcf-infoPackageApplication">
				<fieldset>
					<legend>Easy</legend>
					<style scoped>
  				table {
  					border-collapse: collapse;
  					border: solid thick;
  					width: auto !important;
  					margin: 0 auto;
  					font-size: 2em;
  				}
  				colgroup, tbody {
  					border: solid medium;
  				}
  				td {
  					border: solid thin; height: 1.4em; width: 1.4em; text-align: center; padding: 0;
  					border-right-color: black !important;
  				}
 			</style>
 			<table>
  				<colgroup><col><col><col></colgroup>
  				<colgroup><col><col><col></colgroup>
  				<colgroup><col><col><col></colgroup>
  				{foreach from=$grid->getGridData() key=key item=row}
  					{if $key % 3 == 0}<tbody>{/if}
  					<tr>
  					{foreach from=$row item=column}
						<td{if $column->isGiven()} style="font-weight: bold;"{/if}> {if $column->getValue() > 0}{$column->getValue()}{/if} </td>
  					{/foreach}
  					</tr>
  					{if ($key + 1) % 3 ==0}</tbody>{/if}
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
