<!DOCTYPE HTML PUBLIC
"-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html401/loose.dtd">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <title>Answer Page</title>
</head>
<body bgcolor="white">

<?php 
	
	function showerror() {
		die("Error " . mysql_errno() . " : " . mysql_error());
	}
	
	require 'db.php';

	// Show all wines in a region in a <table>
	function displayWines($connection, $query, $wineName, $wineryName, $regionName, $grapeVariety, $year) {
    // Run the query on the server
		if (!($result = @ mysql_query ($query, $connection))) {
			showerror();
		}

    // Find out how many rows are available
		$rowsFound = @ mysql_num_rows($result);

		// If the query has results ...
		if ($rowsFound > 0) {
			// ... print out a header
			print "You searched for: $wineName, $wineryName, $region, $grapeVariety, $minYear, $maxYear, $minStock, $maxStock, $minPrice, $maxPrice<br><br>";

			// and start a <table>.
			print "\n<table>\n<tr>" .
				"\n\t<th>Wine ID</th>" .
				"\n\t<th>Wine Name</th>" .
				"\n\t<th>Winery Name</th>" .
				"\n\t<th>Region Name</th>" . 
				"\n\t<th>Grape Variety</th>" . 
				"\n\t<th>Year</th>\n</th>" . 
				"\n\t<th>Cost</th>\n</th>" . 
				"\n\t<th>Stock Count</th>\n</th>" . 
				"\n\t<th>Total Sold</th>\n</th>" .
				"\n\t<th>Revenue</th>\n</th>"; 
			
			// Fetch each of the query rows
			while ($row = @ mysql_fetch_array($result)) {
			// Print one row of results
			print "\n<tr>\n\t<td>{$row["wine_id"]}</td>" .
				"\n\t<td>{$row["wine_name"]}</td>" .
				"\n\t<td>{$row["winery_name"]}</td>" .
				"\n\t<td>{$row["region_name"]}</td>" .
				"\n\t<td>{$row["variety"]}</td>" .
				"\n\t<td>{$row["year"]}</td>" .
				"\n\t<td>{$row["cost"]}</td>" . 
				"\n\t<td>{$row["on_hand"]}</td>" .
				"\n\t<td>{$row["total_sold"]}</td>" .
				"\n\t<td>{$row["revenue"]}</td>\n</tr>"; 
			} //end while loop body
			
			//finish table 
			print "\n</table>"; 
		} //end if $rowsFound body 
		
		//Report how many rows were found
		print "<br>{$rowsFound} records found matching your criteria<br>"; 
	} //end of function
	
	// Connect to the MySQL server
	if (!($connection = @ mysql_connect(DB_HOST, DB_USER, DB_PW))) {
		die("Could not connect");
	}
	
	//get user data 
	$wineName = $_GET['wineName']; 
	$wineryName = $_GET['wineryName'];
	$regionName = $_GET['regionName']; 
	$grapeVariety = $_GET['grapeVariety']; 
	$minYear = $_GET['minYear'];
	$maxYear = $_GET['maxYear'];
	$minStock = $_GET['minStock'];
	$maxStock = $_GET['maxStock'];
	$minPrice = $_GET['minPrice'];
	$maxPrice = $_GET['maxPrice'];
	
	if (!mysql_select_db(DB_NAME, $connection)) {
		showerror();
	}
		
	//start a query 
	$query = 
	"SELECT 
		w.wine_id, 
		w.wine_name, 
		wy.winery_name, 
		r.region_name, 
		w.year, 
		gv.variety,
		inv.cost,
		inv.on_hand, 
		(SELECT SUM(items.qty) FROM items WHERE items.wine_id = w.wine_id) AS total_sold,
		(SELECT SUM(items.price) FROM items WHERE items.wine_id = w.wine_id) AS revenue
	FROM 
		grape_variety AS gv
			JOIN 
				wine_variety ON gv.variety_id = wine_variety.variety_id
			JOIN wine AS w ON wine_variety.wine_id =  w.wine_id
			JOIN winery AS wy ON w.winery_id = wy.winery_id
			JOIN region AS r ON wy.region_id = r.region_id 
			JOIN inventory AS inv ON w.wine_id = inv.wine_id
	"; 
		
	
	if (!empty($wineName)) {
		$query .= " AND w.wine_name = '{$wineName}'";
	}
	
	if (!empty($wineryName)) {
		$query .= " AND winery_name = '{$wineryName}'";
	}
	
	// If the user has specified a region, add the regionName 
	// as an AND clause
	if (isset($regionName) && $regionName != "All") {
		$query .= " AND region_name = '{$regionName}'"; 
	} 
	
	// If the user has specified a variety, add the grapeVariety 
	// as an AND clause
	if (!empty($grapeVariety) && $grapeVariety != "All") {
		$query .= " AND variety = '{$grapeVariety}'"; 
	}
	
	if (!empty($minYear) && $minYear != "All") {
		$query .= " AND w.year >= '{$minYear}'";
	}
	
	if (!empty($maxYear) && $maxYear != "All") { 
		$query .= " AND w.year <= '{$maxYear}'";
	}
	
	if (!empty($minStock) && $minStock != "All") {
		$query .= " AND inv.on_hand >= '{$minStock}'";
	}
	
	if (!empty($maxStock) && $maxStock != "All") { 
		$query .= " AND inv.on_hand <= '{$maxStock}'";
	}
	
	if (!empty($minPrice) && $minPrice != "All") {
		$query .= " AND inv.cost >= '{$minCost}'";
	}
	
	if (!empty($maxPrice) && $maxPrice != "All") {
		$query .= " AND inv.cost <= '{$maxCost}'";
	}
	
	//order the list 
	$query .= " ORDER BY wine_name"; 
	
	//run query, show results 
	displayWines($connection, $query, $wineName, $wineryName, $regionName, $grapeVariety, $year); 
	
?>

</body>
</html>