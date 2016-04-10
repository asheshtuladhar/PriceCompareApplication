<!DOCTYPE html>
<html>
<title>Price Compare</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="w3.css">
<body>

<header class="w3-container w3-red">
	<h1>Price Compare</h1>
</header>
<div class="w3-container"> 

<form class="w3-container" action="index.php" method="get">
<p>

<input class="w3-input w3-border" name = "searchdata" type="text" required>
<label class="w3-label w3-validate">Search Mobile</label> </p>

<input id ="search" class="w3-input w3-border" type="submit" value="search">


 
</form>

<div class="w3-row-padding w3-margin-top">

<?php
//get web page data by using URL 

error_reporting(E_ALL & ~E_NOTICE);
if(isset($_GET['searchdata'])){

	$search = $_GET['searchdata'];
	$search = strtolower($search);

	$search = str_replace(" ","+",$search);
	$web_page_data = file_get_contents("http://www.pricetree.com/search.aspx?q=".$search);
echo "Below data is output of above webpage data" ."<br>";


//we need particular data from page not entire page ... echo $web_page_data;

$item_list = explode('<div class="items-wrap">', $web_page_data); 
/*print_r($item_list);
*/

if(sizeof($item_list)<2){
	echo 'No RESULTS';
	

}
//avoid array[0] and loop for 4 items wrap
//variable to check no data
$count = 9;
for($i=1;$i<10;$i++){
	//echo $item_list[$i]; //this is array seperated based on split string <div class="items-wrap">
	// i want title and other info.
	//it is printing 9 items.
	//for those items i want itemimage url and item link
	//from list item split based on href=" and then " because we want url between them

	$url_link1 = explode('href="',$item_list[$i]);
	$url_link2 = explode('"', $url_link1[1]); //$url_link[0] will be before http=" data
	//echo $url_link2[0]."<br>"; //split by " and before that

	// now image link, same as above but split with data-original = "

	$image_link1 = explode('data-original="',$item_list[$i]);
	$image_link2 = explode('"', $image_link1[1]); //$url_link[0] will be before http=" data
	//echo $image_link2[0]."<br>"; //split by " and before that
	//i want title and only available
	//getting title split between title=" and "

	$title1 = explode('title="', $item_list[$i]);
	$title2 = explode('"', $title1[1]);

	//get only available items
	//split between avail-store and div

	$available1 = explode('avail-stores">',$item_list[$i]);
	$available = explode('</div>', $available1[1]);
	if(strcmp($available[0],"Not available")==0){
		//means not available
		$count = $count-1;
		continue;
		//goto next item in for loop
	}
	$item_title = $title2[0];
	if(strlen($item_title)<2){
		continue;
	}
	$item_like = $url_link2[0];
	$item_image_link = $image_link2[0];
	$item_id1 = explode("-", $item_like);
	$item_id = end($item_id1); //split with  "-" and print last one after split that is id
	//show image and product title
	echo '
	<br>
	<div class="w3-row">
		<div class="w3-col s3 w3-row-padding">
			
				<div class="w3-card-2" style ="background-color:teal; color:white;">
					<img src="'.$item_image_link.'" style="width:100%">
	<div class="w3-container">
	<h5>'.$item_title.'</h5>
	</div>
				</div>
			
		</div>

	';


	/*echo ."<br>";
	echo."<br>";
	echo $item_image_link."<br>";
	echo $item_id."<br>";*/

	//goto price tree access api to get price list
	//price list will be accessable based on $item_id

	$request = "http://www.pricetree.com/dev/api.ashx?pricetreeId=".$item_id."&apikey=7770AD31-382F-4D32-8C36-3743C0271699";
	$response = file_get_contents($request);
	$results = json_decode($response, TRUE);
	//print_r($results);
//table needs to be open before for each 
//3 parts  image and 9 parts table in a web page width

	echo '
	<div class="w3-col s9">
		<div class="w3-card-2 ">
			<table class="w3-table w3-striped w3-bordered w3-card-4">
				<thead>
					<tr class="w3-blue">
  						<th>Seller_Name</th>
  						<th>Price</th>
  						<th>Buy Here</th>
					</tr>
				</thead>
';
	foreach ($results['data'] as $itemdata) {
		$seller = $itemdata['Seller_Name'];
		$price= $itemdata['Best_Price'];
		$product_link= $itemdata['Uri'];

//echo $seller.",".$price.",".$product_link."<br>";

echo '
<tr>
  <td>'.$seller.'</td>
  <td>'.$price.'</td>
  <td><a href="'.$product_link.'">Buy</a></td>
</tr>
	

';
}
//close tuable after for each
echo '</table>
	</div>
	</div>
	</div>
';
}

if($count == 0){
	echo '<p><b>No products available</p>';
}
}else{
	echo "Use this to search mobile phones"." <b>Get the best price from online stores.";
}



?>



</div>
</div>
</div>
<footer class="w3-container w3-teal">
 
  <p>Copyright @ me</p>
</footer>
</body>
</html> 
