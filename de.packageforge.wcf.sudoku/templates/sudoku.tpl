{include file='documentHeader'}

<head>
	<title>Sudoku page</title>
	{include file='headInclude' sandbox=false}
</head>

<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>

{capture assign='sidebar'}
<nav id="sidebarContent" class="wcf-sidebarContent">
	<div>
		<fieldset>
			<legend>42</legend>


		</fieldset>
	</div>
</nav>
{/capture}

{include file='header' sandbox=false sidebarOrientation='left'}

<header class="wcf-container wcf-mainHeading">
	<img src="{icon size='L'}sudoku1{/icon}" alt="" class="wcf-containerIcon" />
	<hgroup class="wcf-containerContent">
		<h1>Sudoku</h1>
	</hgroup>
</header>

<section>
 <style scoped>
  table {
  	border-collapse: collapse;
  	border: solid thick;
  	width: auto !important;
  	margin: 0 auto;
  }
  colgroup, tbody {
  	border: solid medium;
  }
  td {
  	border: solid thin; height: 1.4em; width: 1.4em; text-align: center; padding: 0;
  }
 </style>
 <table>
  <colgroup><col><col><col>
  <colgroup><col><col><col>
  <colgroup><col><col><col>
  {foreach from=$grid->getGrid() key=key item=row}
  	{if $key % 3 == 0}<tbody>{/if}
  	<tr>
  	{foreach from=$row item=column}
		<td{if $column->isGiven()} style="font-weight: bold;"{/if}> {if $column->getValue() > 0}{$column->getValue()}{/if} </td>
  	{/foreach}
  	</tr>
  	{if ($key + 1) % 3 ==0}</tbody>{/if}
  {/foreach}
 </table>
</section>

{include file='footer' sandbox=false}

</body>
</html>
