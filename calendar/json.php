<?php
$data = array();

eachDate(function($id, $name, $category, $startDate, $endDate) use (&$data){
	$data[] = array(
		"name" => $name,
		"category" => $category,
		"start" => $startDate->date->format("Y-m-d H:i:s"),
		"end" => $endDate->date->format("Y-m-d H:i:s")
	);
});

return json_encode($data);