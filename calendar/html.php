<?php

$config = new ConfigFile("config.ini");
$config->load();

$html = "<!DOCTYPE html>
<html>
	<head>
		<title>" . htmlentities($config->name, ENT_QUOTES, "utf-8") . "</title>
		<style type=\"text/css\">
			.content {
				display: grid;
				grid-template-columns: [start] max-content [middle] max-content [separator] auto [end];
			}
			.comment {
				grid-column-start: start;
				grid-column-end: end;
			}
			.entry {
				display: grid;
				grid-column-start: start;
				grid-column-end: end;
				grid-template-columns: subgrid;
			}
			.date {
				grid-column-start: start;
				grid-column-end: middle;
			}
			.separator {
				grid-column-start: middle;
				grid-column-end: separator;
			}
			.name {
				grid-column-start: separator;
				grid-column-end: end;
			}
		</style>
	</head>
	<body>
		<h1>" . htmlentities($config->name, ENT_QUOTES, "utf-8") . "</h1>
		<section class=\"content\">";

eachDate(function($id, $name, $category, $startDate, $endDate) use (&$html, $config){
	$html .= "<div class=\"entry\">";
	
	$html .= "<span class=\"date\">" .
		$startDate->format($config->date_format, $config->date_format . " " . $config->time_format);
	if ($startDate->withTime || !$endDate->autoCreated){
		$html .= " - ";
		if ($startDate->date->format("Y-m-d") === $endDate->date->format("Y-m-d")){
			$html .= $endDate->date->format($config->time_format);
		}
		else {
			if ($endDate->withTime){
				$html .= $endDate->format($config->date_format, $config->date_format . " " . $config->time_format);
			}
			else {
				$end = clone $endDate->date;
				$end->modify("-1 day");
				$html .= $end->format($config->date_format);
			}
		}
	}
	$html .= "</span>";
	$html .= "<span class=\"separator\">:&nbsp;</span>";
	$html .= "<span class=\"name\" title=\"" . htmlentities($category, ENT_QUOTES, "utf-8") . "\">" .
		htmlentities($name, ENT_QUOTES, "utf-8") .
		"</span>";
	
	$html .= "</div>\n";
}, function($commentLine) use (&$html){
	$commentLine = trim($commentLine, "	 #");
	if (substr($commentLine, 0, 1) === "-"){
		return;
	}
	$html .= "<h2 class=\"comment\">" . htmlentities($commentLine, ENT_QUOTES, "utf-8") . "</h2>\n";
});
$html .= "
		</section>
		<section>
			<h2>Links</h2>
			<a href=\"?type=ics\">ICS</a>
			<a href=\"?type=csv\">CSV</a>
			<a href=\"?type=txt\">TXT</a>
			<a href=\"?type=json\">JSON</a>
		</section>
	</body>
</html>";
return $html;