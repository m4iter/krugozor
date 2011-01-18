<?
	$time_end = getmicrotime();
	$time = $time_end - $time_start;
	?>
<div style="margin-top:3em;" ondblclick="this.style.display='none'">
	<table border="1" cellpadding="5" cellspacing="0" width="100%">
	<? $i=0; foreach(database::getInstance()->getQueries() as $key => $val): ?>
		<tr bgcolor="#<?=$i%2 == 0 ? 'F5F5F5' : 'FFFFFF'?>"><td><?=str_replace("\t", "&nbsp;&nbsp;&nbsp;&nbsp;", nl2br($val))?></td></tr>
	<? $i++; endforeach; ?>
	</table>
	
	<table border="1" cellpadding="5" cellspacing="0" width="100%"><tr><td>Время работы скрипта: </td><td><strong><?=$time?></strong> сек.</td></tr>
	</table>
</div>