<?php  

include('../includes/db.php');
//importing a csv file directly into mysql
//http://www.tech-recipes.com/rx/2345/import_csv_file_directly_into_mysql/ 

// Get parameters from URL
$center_lat = $_GET["lat"];
$center_lng = $_GET["lng"];
$radius = $_GET["radius"];
$limit = $_GET["limit"];
$brand = $_GET['brand'];

/*
$center_lat = "40.7203421";
$center_lng = "-74.0079781";
$radius = "10";
*/

// Start XML file, create parent node
$dom = new DOMDocument("1.0");
$node = $dom->createElement("markers");
$parnode = $dom->appendChild($node);

// Search the rows in the markers table
$query = sprintf("SELECT address1, city, state, zip, latitude, longitude, ( 3959 * acos( cos( radians('%s') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( latitude ) ) ) ) AS distance FROM arbys_locations HAVING distance < '%s' ORDER BY distance LIMIT 0 , %s",
  mysql_real_escape_string($center_lat),
  mysql_real_escape_string($center_lng),
  mysql_real_escape_string($center_lat),
  mysql_real_escape_string($radius),
  mysql_real_escape_string($limit));
  
  
//$query = "SELECT * FROM event";

$result = mysql_query($query, CONN);
if (!$result) {
  die("Invalid query: " . mysql_error());
}

header("Content-type: text/xml");

// Iterate through the rows, adding XML nodes for each
while ($row = @mysql_fetch_assoc($result)){
  $node = $dom->createElement("marker");
  $newnode = $parnode->appendChild($node);
  $newnode->setAttribute("address1", $row['address1']);
  $newnode->setAttribute("city", $row['city']);
  $newnode->setAttribute("state", $row['state']);
  $newnode->setAttribute("zip", $row['zip']);
  $newnode->setAttribute("latitude", $row['latitude']);
  $newnode->setAttribute("longitude", $row['longitude']);
  $newnode->setAttribute("distance", $row['distance']);
}

echo $dom->saveXML();

//$xml_output .= '	<marker name="'.$row["title"].'" address="'.$row["address"].'" city="'.$row["city"].'" state="'.$row["state"].'" zipcode="'.$row["zipcode"].'" lat="'.$row["lat"].'" lng="'.$row["lng"].'" website="'.$row["website"].'" type="'.$row["category"].'"  />

?>