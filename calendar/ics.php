<?php
$calendar = new Calendar();
CalendarTimezone::addGermanTimezone($calendar);

$config = new ConfigFile("config.ini");
$config->load();
$calendar->name = $config->name;
$calendar->{"X-WR-CALNAME"} = $config->name;
$calendar->description = $config->description;
$calendar->{"X-WR-CALDESC"} = $config->description;
$calendar->url = $config->url;

$nowString = (new DateTime())->format("Ymd\THis");

eachDate(function(int $id, String $name, String $category, CalendarDate $start, CalendarDate $end) use ($calendar, $nowString, $config){
	$duration = $start->date->diff($end->date);
	
	$event = new CalendarEvent();
	$event->uid = str_replace(array("{name}", "{id}"), array(preg_replace("/[^0-9a-z]/i", "_", $name), $id), $config->uid_template);
	$event->dtstamp = $nowString;
	$event->dtstamp->tzid = "Europe/Berlin";
	if ($start->withTime){
		$event->dtstart = $start->date->format("Ymd\THis");
		// $event->dtstart->VALUE = "DATETIME";
		$event->duration = $duration->format("P%aDT%hH%iM");
	}
	else {
		$event->dtstart = $start->date->format("Ymd");
		$event->dtstart->VALUE = "DATE";
		$event->duration = $duration->format("P%aD");
	}
	$event->dtstart->tzid = "Europe/Berlin";
	$event->summary = $name;
	$event->categories = $category;
	$calendar->addChild($event);
});

return $calendar->view(false, false);