<?php
$data = array(array("Description", "Category", "Start", "End"));

eachDate(function($id, $name, $category, $start, $end) use (&$data){
	$data[] = array($name, $category, $start->date->format("Y-m-d H:i"), $end->date->format("Y-m-d H:i"));
});

$csv = new CSVWriter();
$csv->separator = array_read_key("separator", $_GET, ",");
return $csv->writeToString($data);