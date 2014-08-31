<!DOCTYPE HTML PUBLIC
"-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html401/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <title>Search Page</title>
</head>
<body bgcolor="white">
<h3> Winestore Database Search v2.0</h3> 
<p> Enter your search terms</p>

	<?php 
		require "db.php"; 
		
		// SelectDistinct() function 
		function selectDistinct ($connection, $tableName, $attributeName, $pulldownName, $defaultValue) {
		$defaultWithinResultSet = FALSE; 
			
			//  Query to find distinct values of $attributeName in $tableName 
			$distinctQuery = "SELECT DISTINCT {$attributeName} FROM {$tableName} ORDER BY {$attributeName}"; 
			
			//Run the distinctQuery on the databaseName
			if (!($resultId = @ mysql_query ($distinctQuery, $connection)))
				showerror(); 
				
			// Start the select widget 
			
			print "\n<select name=\"{$pulldownName}\">"; 
			
			if (!(isset($defaultValue))) 
				{
					// Change defaultValue to something else so it only runs once
					$defaultValue = "null";
					print "\n\t<option selected value=\"All\">All";
				}
				
			// Retrieve each row from the query 
			while ($row = @ mysql_fetch_array($resultId)) {
				
				// Get the value for the attribute to be displayed
				$result = $row[$attributeName]; 
				
				// Print
				if ($result == $defaultValue)  
					print "\n\t<option selected value=\"{$result}\">{$result}"; 
				else
					// No, just show as an option 
					print "\n\t<option value=\"{$result}\">{$result}"; 
				print "</option>"; 
			}
			print "\n</select>"; 
		} // End function 
	?>

  <form action="answer.php" method="GET">
    <br>Wine Name: 
    <input type="text" name="wineName" value="">
	<br>Winery Name:
	<input type="text" name="wineryName" value=""> 
	<?php 
		// Connect to the server 
		if (!($connection = @ mysql_connect(DB_HOST, DB_USER, DB_PW))) {
			showerror(); 
		}
		
		if (!mysql_select_db(DB_NAME, $connection)) {
			showerror(); 
		}
		
		print "<br>\nRegion Name: ";
		
		selectDistinct($connection, "region", "region_name", "regionName", "All"); 
		
		print "<br>\nGrape Variety: "; 
		
		selectDistinct($connection, "grape_variety", "variety", "grapeVariety"); 
		
		print "<br>\n Minimum Year: ";  
		
		selectDistinct($connection, "wine", "year", "minYear");
		
		print "<br>\n Maximum Year: "; 
		
		selectDistinct($connection, "wine", "year", "maxYear"); 
		
	?>
	<br>Minimum Stock Count: 
	<input type="number" name="minStock" value=""> 
	<br>Maximum Stock Count: 
	<input type="number" name="maxStock" value="">
	<br>Minimum Price: 
	<input type="number" name="minPrice" value=""> 
	<br>Maximum Price: 
	<input type="number" name="maxPrice" value="">
    <br><br><input type="submit" value="Search">
  </form>
  <br>
</body>
</html>