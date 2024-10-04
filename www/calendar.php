<?php
chdir("../calendar/");
include_once("loadFramework.php");

class CalendarDate {
	public ?DateTime $date = null;
	public bool $withTime;
	private static DateTimeZone $timezone;
	static function init(){
		self::$timezone = new DateTimeZone("Europe/Berlin");
	}
	
	function __construct($dateString, ?CalendarDate $start = null){
		if ($dateString){
			$withTime = DateTime::createFromFormat("d.m.Y H:i|", $dateString, self::$timezone);
			if ($withTime){
				$this->withTime = true;
				$this->date = $withTime;
				return;
			}
			$withoutTime = DateTime::createFromFormat("d.m.Y|", $dateString, self::$timezone);
			if ($withoutTime){
				$this->withTime = false;
				$this->date = $withoutTime;
				return;
			}
			if (!$start || !$start->date){
				return;
			}
			$withStartDate = DateTime::createFromFormat("d.m.Y H:i", $start->date->format("d.m.Y ") . $dateString, self::$timezone);
			if ($withStartDate){
				$start->withTime = true;
				$this->withTime = true;
				$this->date = $withStartDate;
				return;
			}
		}
	}
		}
	}
	
	function getEnd(){
		$end = new self("");
		$end->withTime = $this->withTime;
		/** @var Datetime $date */
		$date = clone $this->date;
		if ($this->withTime){
			$date->modify("+1 hour");
		}
		else {
			$date->modify("+1 day");
		}
		$end->date = $date;
		return $end;
	}
}
CalendarDate::init();

function parseRange($dateDefinition){
	$dates = explode("-", $dateDefinition, 2);
	$start = new CalendarDate($dates[0]);
	
	$end = false;
	if (count($dates) == 2){
		$end = new CalendarDate($dates[1], $start);
		if (!$end->withTime){
			$end->date->modify("+1 day");
		}
	}
	elseif ($start->date) {
		$end = $start->getEnd();
	}
	return array($start, $end);
}

function eachDate($callback){
	global $dataFile;
	$lines = preg_split("/[\\n\\r]+/", file_get_contents($dataFile), 0, PREG_SPLIT_NO_EMPTY);
	$id = 1;
	foreach ($lines as $line){
		$line = ltrim($line);
		if (!$line || substr($line, 0, 1) === "#"){
			continue;
		}
		list($dateDefinition, $name, $category) = explode("\t", $line, 3);
		list($start, $end) = parseRange($dateDefinition);
		
		if ($name && $start->date && $end->date){
			$callback($id, $name, $category, $start, $end);
			$id += 1;
		}
	}
}

$types = array(
	"ics" => "text/calendar;charset=UTF-8",
	"txt" => "text/plain;charset=UTF-8",
	"json" => "application/json",
	"csv" => "text/csv;charset=UTF-8");

$type = strToLower(array_read_key("type", $_GET, "ics"));

if (!array_key_exists($type, $types)){
	$type = "ics";
}

header("Content-Type: " . $types[$type]);
$dataFile = "data.txt";

if ($type === "txt"){
	echo file_get_contents($dataFile);
	die();
}

$cacheFile = "cache/" . $type;
if (!file_exists("cache")){
	mkdir("cache");
}
$useCache = !array_key_exists("noCache", $_GET);
if (
	$useCache &&
	file_exists($cacheFile) &&
	filemtime($cacheFile) >= filemtime($dataFile)
){
	echo file_get_contents($cacheFile);
	die();
}

$content = include($type . ".php");
if ($useCache){
	file_put_contents($cacheFile, $content);
}
echo $content;