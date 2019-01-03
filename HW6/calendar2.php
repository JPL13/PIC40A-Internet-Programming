#!/usr/local/bin/php -d display_errors=STDOUT
<?php

// For each slot (person and time) your code should call a function called get_events($person, $timestamp). 
//This function will return a string containing all events that should be displayed in the time slot in question.
date_default_timezone_set('America/Los_Angeles'); 

$time_stamp=(isset($_GET['time_stamp']))?$_GET['time_stamp']:time();

$today = date("D, F j, Y, g:i a",$time_stamp);
$start_hour_offset = -3;
$end_hour = 12; // How many hours to show total

function get_hour_string($time_stamp){
$hour = date("g", $time_stamp);
$am_or_pm = date("a",$time_stamp);
return "$hour.00$am_or_pm";
}


function get_events($person, $timestamp){
$event_string=" ";

$database = "dbjpiao.db";          

try  
{     
     $db = new SQLite3($database);
}
catch (Exception $exception)
{
    echo '<p>There was an error connecting to the database!</p>';

    if ($db)
    {
        echo $exception->getMessage();
    }
        
}

$sql = "SELECT * FROM event_table WHERE name='$person' AND time>=$timestamp AND time<$timestamp+3600";
$result = $db->query($sql);

 while($record = $result->fetchArray(SQLITE3_ASSOC))
   
  $event_string.= $record['event_title']. ": " . $record['event_message']. "<br>";


 return $event_string;
}

   print('<?xml version = "1.0" encoding="utf-8"?>');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
 "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
      xmlns:v="urn:schemas-microsoft-com:vml">
<head>
<title>Calendar</title> 
<link rel="stylesheet" type="text/css" href="calendar.css" />

</head>
<body>

<div class="container">
<h1>Bruin Family Schedule for <?php print"$today" ?> </h1>
<table id="event_table">


<?php

print "	<tr> \n";

print "	<th class='hr_td_'> &nbsp; </th> <th class='table_header'>Mom</th><th class='table_header'>Dad</th><th class='table_header'>Joe</th> \n";
print "	</tr> \n";

    for ($i=0; $i<=$end_hour;++$i)
	{

	$hour_string = get_hour_string($time_stamp + $i*3600);
	$str=date("n/j/Y/H", $time_stamp + $i*3600);           
    $date_arr=explode("/", $str);
    $ts=mktime($date_arr[3], 0, 0, $date_arr[0], $date_arr[1], $date_arr[2]); 	
	
	      
	$mom_events = get_events("Mom", $ts);
    $dad_events = get_events("Dad", $ts);
    $joe_events = get_events("Joe", $ts);


	
	if ($i%2 == 0){
	
			print "<tr class='even_row'> \n";

		print "<td class='hr_td'>$hour_string </td> <td>$mom_events </td> <td> $dad_events </td> <td>$joe_events</td>\n";
		print "	</tr> \n";
}
	if ($i%2 !=0){

		print "<tr class='odd_row'>\n";
		print "<td class='hr_td'>$hour_string </td> <td>$mom_events </td> <td>$dad_events </td> <td> $joe_events</td>\n";
		print "</tr>\n";

	}
}

?>	
	
</table>

<?php
print "<div>";
 print "<form id='prev' method='get' action='calendar2.php'>";
 print "<p>";

     $prev=$time_stamp-12*3600;
 print "<input type='hidden' name='time_stamp' value= '$prev' />";

print "<input type='submit' value='Previous twelve hours'/>";
print "</p>";
print "</form>";

print "<form id='next' method='get' action='calendar2.php'>";
print "<p>";
	
     $next=$time_stamp+12*3600;	
print "<input type='hidden' name='time_stamp' value= '$next'/>";


print "<input type='submit' value='Next twelve hours'/>";
print "</p>";
print "</form>";

print "<form id='today' method='get' action='calendar2.php'>";
print "<p>";
print "<input type='submit' value='Today'/>";
print "</p>";
print "</form>";

print "</div>";
?>

</div>
</body>
</html>

