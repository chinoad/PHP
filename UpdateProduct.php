<html>
	<head>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
   <title>Update Product </title>
</head>
<body >

<?php
// Check to see if Delete name is provided
if (isset($_GET["productName"])) {
  $toUpdate = $_GET["productName"];
  // A bit dangerous without checks and use of getMethod
  updateIt($toUpdate);

   echo "<p></p>";
	    echo "<a href=InsertProduct.php> Insert Another Product </a>";
	     echo "<p></p>";
	    echo "<a href=SelectProduct.php> Select Product </a>";
	     echo "<p></p>";
	    echo "<a href=DeleteProduct.php> Delete Product </a>";
	     echo "<p></p>";
	    echo "<a href=UpdateProduct.php> Update Product</a>";
}
else if (isset($_POST["UpdateMe"])) {
	// Assign values
  $productName = $_POST["productName"];
  $productPrice = $_POST["productPrice"];
  $productID = $_POST["productID"];

  $product = new ProductClass($productName,$productPrice,$productID);
  // Update the database
  FinalUpdate($product);
 echo "<p></p>";
	    echo "<a href=InsertProduct.php> Insert Another Product </a>";
	     echo "<p></p>";
	    echo "<a href=SelectProduct.php> Select Product </a>";
	     echo "<p></p>";
	    echo "<a href=DeleteProduct.php> Delete Product </a>";
	     echo "<p></p>";
	    echo "<a href=UpdateProduct.php> Update Product </a>";
}
 else {
	    show_form();

	    // Provide option for inserting another product
	    echo "<p></p>";
	    echo "<a href=InsertProduct.php> Insert Another Product </a>";
	     echo "<p></p>";
	    echo "<a href=SelectProduct.php> Select Product </a>";	   }

	?>

<?php
function show_form() {

	echo "<p></p>";
	echo "<h2>Select the Product to Delete</h2>";
	echo "<p></p>";
	// Retrieve the products
	$product = selectProducts();

	echo "<h3> " . "Number of Products in Database is:  " . sizeof($product) . "</h3>";
	// Loop through table and display
	echo "<table border='1'>";
	foreach ($product as $data) {
	echo "<tr>";
	// Provide Hyperlink for Selection
	// Could also use Form with Post method
	echo "<td> <a href=UpdateProduct.php?productName=" . $data->getProductName() . ">" . "Update" . "</a></td>";
	 echo "<td>" . $data->getProductName() . "</td>";
	 echo "<td>" . $data->getProductPrice() . "</td>";
	 echo "<td>" . $data->getProductID() . "</td>";
	echo "</tr>";
}
	echo "</table>";

} // End Show form
?>

<?php

  function getProduct ($product) {
  	// Connect to the database
   $mysqli = connectdb();

   // Add Prepared Statement
		$Query = "Select productName, productPrice, productID from Products
		         where productName = ?";

		$stmt = $mysqli->prepare($Query);

// Bind and Execute
$stmt->bind_param("s", $product);
$result = $stmt->execute();

 $stmt->bind_result($productName,$productPrice,$productID);

  /* fetch values */
    $stmt->fetch();
  $productData = new ProductClass($productName,$productPrice,$productID);

// Clean-up
	$stmt->close();
   $mysqli->close();
   return $productData;
  }
  function updateIt($product) {


	$product = getProduct($product);
	// Extract data
	$productName = $product->getProductName();
	$productPrice = $product->getProductPrice();
	$productID = $product->getProductID();

	// Show the data in the Form for update
	?>
	<p></p>

	<form name="updateProduct" method="POST" action="UpdateProduct.php">
	<table border="1" width="75%" cellpadding="0">
			<tr>
				<td width="157">Product Name:</td>
				<td><input type="text" name="productName" value='<?php echo $productName ?>' size="30"></td>
			</tr>
			<tr>
				<td width="157">Product Price:</td>
				<td><input type="text" name="productPrice" value='<?php echo $productPrice ?>' size="30"></td>
			</tr>
			<tr>
				<td width="157">Product ID:</td>
				<td><input type="text" name="productID" value='<?php echo $productID ?>' size="30"></td>
			</tr>

			<tr>
				<td width="157"><input type="submit" value="Update" name="UpdateMe"></td>
				<td>&nbsp;</td>
			</tr>
	</table>
	</form>

  <?php
  }
  function selectProducts ()
  {

		// Connect to the database
   $mysqli = connectdb();


		// Add Prepared Statement
		$Query = "Select productName, productPrice, productID from Products";

		$result = $mysqli->query($Query);
		$myProducts = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
    	// Assign values
    	$productName = $row["productName"];
    	$productPrice = $row["productPrice"];
    	$productID = $row["productID"];

       // Create a Product instance
       $productData = new ProductClass($productName,$productPrice,$productID);
       $myProducts[] = $productData;
      }
 }

	$mysqli->close();

	return $myProducts;

	}

  function getDbparms()
	 {
	 	$trimmed = file('parms/dbparms.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	$key = array();
	$vals = array();
	foreach($trimmed as $line)
	{
		  $pairs = explode("=",$line);
	    $key[] = $pairs[0];
	    $vals[] = $pairs[1];
	}
	// Combine Key and values into an array
	$mypairs = array_combine($key,$vals);

	// Assign values to ParametersClass
	$myDbparms = new DbparmsClass($mypairs['username'],$mypairs['password'],
	                $mypairs['host'],$mypairs['db']);

	// Display the Paramters values
	return $myDbparms;
	 }

  function connectdb() {
		// Get the DBParameters
	  $mydbparms = getDbparms();

	  // Try to connect
	  $mysqli = new mysqli($mydbparms->getHost(), $mydbparms->getUsername(),
	                        $mydbparms->getPassword(),$mydbparms->getDb());

	   if ($mysqli->connect_error) {
	      die('Connect Error (' . $mysqli->connect_errno . ') '
	            . $mysqli->connect_error);
	   }
	  return $mysqli;
	}

 class DBparmsClass
	{
	    // property declaration
	    private $username="";
	    private $password="";
	    private $host="";
	    private $db="";

	    // Constructor
	    public function __construct($myusername,$mypassword,$myhost,$mydb)
	    {
	      $this->username = $myusername;
	      $this->password = $mypassword;
			  $this->host = $myhost;
				$this->db = $mydb;
	    }

	    // Get methods
		  public function getUsername ()
	    {
	    	return $this->username;
	    }
		  public function getPassword ()
	    {
	    	return $this->password;
	    }
		  public function getHost ()
	    {
	    	return $this->host;
	    }
		  public function getDb ()
	    {
	    	return $this->db;
	    }

	    // Set methods
	    public function setUsername ($myusername)
	    {
	    	$this->username = $myusername;
	    }
	    public function setPassword ($mypassword)
	    {
	    	$this->password = $mypassword;
	    }
	    public function setHost ($myhost)
	    {
	    	$this->host = $myhost;
	    }
	    public function setDb ($mydb)
	    {
	    	$this->db = $mydb;
	    }

	} // End DBparms class

 // Class to construct Students with getters/setter
class ProductClass
{
    // property declaration
    private $productName="";
    private $productPrice="";
    private $productID="";

    // Constructor
    public function __construct($productName,$productPrice,$productID)
    {
      $this->productName = $productName;
      $this->productPrice = $productPrice;
      $this->productID = $productID;
    }

    // Get methods
	  public function getProductName ()
    {
    	return $this->productName;
    }
	  public function getProductPrice ()
    {
    	return $this->productPrice;
    }
	  public function getProductID()
    {
    	return $this->productID;
    }


    // Set methods
    public function setProductName ($value)
    {
    	$this->productName = $value;
    }
    public function setProductPrice ($value)
    {
    	$this->productPrice = $value;
    }
    public function setProductID ($value)
    {
    	$this->productID = $value;
    }


} // End ProductClass

// Final Update
function FinalUpdate($product) {
	// Assign values
  $productName = $product->getProductName();
  $productPrice = $product->getProductPrice();
  $productID = $product->getProductID();

  // update
  // Connect to the database
   $mysqli = connectdb();

		// Add Prepared Statement
		$Query = "Update Products set productName = ?,
		         productPrice = ?, productID = ?,
		         where productName = ?";



		$stmt = $mysqli->prepare($Query);

$stmt->bind_param("ssis", $productName, $productPrice, $productID,$productName);
$stmt->execute();

 //Clean-up
	$stmt->close();
   $mysqli->close();

}

?>
</body>
</html>

