<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title>Show List of Products</title>

</head>

<body OnLoad="document.selectproduct.productname.focus();">

<?php
    show_form();
	    echo "<p></p>";
	    echo "<a href=InsertProduct.php> Insert Another Product </a>";
	?>
<?php

function show_form() {

	// Retrieve the product
	$product = selectProducts();
	echo "<h3> " . "Number of Products in Database is:  " . sizeof($product) . "</h3>";
	// Loop through table and display
	echo "<table border='1'>";
	foreach ($product as $data) {
	echo "<tr>";

	 echo "<td>" . $data->getProductName() . "</td>";

	 echo "<td>" . $data->getProductPrice() . "</td>";

	 echo "<td>" . $data->getProductID() . "</td>";

	echo "</tr>";

}

	echo "</table>";
} // End Show form

?>



<?php
  function selectProducts()
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



 // Class to construct Products with getters/setter

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

	  public function getProductID ()

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

?>

</body>

</html>

