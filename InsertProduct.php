<html>
	<head>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
   <title>Product Info</title>
</head>
<body OnLoad="document.createproduct.productname.focus();">

<?php

			if(isset($_POST["CreateSubmit"]))

			{



		   	validate_form();

			}

			else

			{

				$messages = array();

		    show_form($messages);

	  	}

		?>



	<?php

	function show_form($messages) {





			// Assign post values if exist

			$productName="";

			$productPrice="";

			$productID="";


			if (isset($_POST["productName"]))


			  $productName=$_POST["productName"];

			if (isset($_POST["productPrice"]))

			  $productPrice=$_POST["productPrice"];

		  if (isset($_POST["productID"]))

			  $productID=$_POST["productID"];

		echo "<p></p>";

		echo "<h2>Please Enter Product Info</h2>";

		echo "<p></p>";

		?>

		<form name="createproduct" method="POST" action="InsertProduct.php">

		<table border="1" width="100%" cellpadding="0">

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

					<td width="157"><input type="submit" value="Submit" name="CreateSubmit"></td>

					<td>&nbsp;</td>

				</tr>

		</table>

		</form>



		<?php

	} // End Show form

	?>



	<?php

	function validate_form()

	{



		$messages = array();

	  $redisplay = false;

	  // Assign values
		$productName = $_POST["productName"];

	  $productPrice = $_POST["productPrice"];

	  $productID = $_POST["productID"];

	  $product = new ProductClass($productName,$productPrice,$productID);

	  	$count = countProduct($product);


	  	// Check for accounts that already exist and Do insert

	  	if ($count==0)

	  	{

	  		$res = insertProduct($product);

	  		echo "<h3>Product added successfully</h3> ";

	  	}

	  	else

	  	{

	  		echo "<h3>Product already exists.</h3> ";

	  	}

	  }



	 function countProduct ($product)

	  {

	  	// Connect to the database

	   $mysqli = connectdb();

		 $produdctName = $product->getProductName();

	   $productPrice = $product->getProductPrice();

	   $productID = $product->getProductID();


			// Connect to the database

		$mysqli = connectdb();



		// Define the Query

		// For Windows MYSQL String is case insensitive

		 $Myquery = "SELECT count(*) as count from Products

			   where username='$username'";



		 if ($result = $mysqli->query($Myquery))

		 {

		    /* Fetch the results of the query */

		    while( $row = $result->fetch_assoc() )

		    {

		  	  $count=$row["count"];

		    }



	 	    /* Destroy the result set and free the memory used for it */

		    $result->close();

	   }



		$mysqli->close();



		return $count;



	  }



	  function insertProduct ($product)

	  {



			// Connect to the database

	   $mysqli = connectdb();

		 $productName = $product->getProductName();


		 $productPrice = $product->getProductPrice();

	   $productID = $product->getProductID();

			// Add Prepared Statement

			$Query = "INSERT INTO Products

		          (productName, productPrice, ProductID)

		           VALUES (?,?,?)";





			$stmt = $mysqli->prepare($Query);



	$stmt->bind_param("ssi",$productName, $productPrice, $productID);

	$stmt->execute();







		$stmt->close();

		$mysqli->close();



			return true;

		}



	  function getDbparms()

		 {

		 	$trimmed = file("parms/dbparms.txt",FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

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



	class ProductClass

	{

	    // property declaration

			private $productName="";

	    private $productPrice="";

	    private $productID="";

	    // Constructor

	    public function __construct($productName, $productPrice,$productID)

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


	}



	?>

	</body>

	</html>

