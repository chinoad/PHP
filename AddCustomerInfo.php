<html>
	<head>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
   <title>Customer Info</title>
</head>
<body OnLoad="document.createcustomer.nickname.focus();">

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

			$nickname="";


			$firstname="";

			$lastname="";


			$email="";

			if (isset($_POST["nickname"]))


			  $username=$_POST["nickname"];

			if (isset($_POST["firstname"]))

			  $firstname=$_POST["firstname"];

		  if (isset($_POST["lastname"]))

			  $lastname=$_POST["lastname"];



			if (isset($_POST["email"]))

			  $email=$_POST["email"];



		echo "<p></p>";

		echo "<h2Please Enter Your Information</h2>";

		echo "<p></p>";

		?>

		<h5>Complete the information in the form below and click Submit to create your account. All fields are required.</h5>

		<form name="createcustomer" method="POST" action="AddCustomerInfo.php">

		<table border="1" width="100%" cellpadding="0">

			<tr>

				<td width="157">Nickname:</td>

				<td><input type="text" name="nickname" value='<?php echo $nickname ?>' size="30"></td>

			</tr>
				<tr>

					<td width="157">Firstname:</td>

					<td><input type="text" name="firstname" value='<?php echo $firstname ?>' size="30"></td>

				</tr>

				<tr>

					<td width="157">Lastname:</td>

					<td><input type="text" name="lastname" value='<?php echo $lastname ?>' size="30"></td>

				</tr>



				<tr>

					<td width="157">Email:</td>

					<td><input type="text" name="email" value='<?php echo $email ?>' size="30"></td>

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
		$nickname = $_POST["nickname"];

	  $firstname = $_POST["firstname"];

	  $lastname = $_POST["lastname"];


	  $email = $_POST["email"];



	  $customer = new CustomerClass($nickname,$firstname,$lastname,$email);

	  	$count = countCustomer($customer);



	  	// Check for accounts that already exist and Do insert

	  	if ($count==0)

	  	{

	  		$res = insertCustomer($customer);

	  		echo "<h3>Thank you for registering with us!</h3> ";

	  	}

	  	else

	  	{

	  		echo "<h3>A customer account with that username already exists.</h3> ";

	  	}

	  }



	 function countCustomer ($customer)

	  {

	  	// Connect to the database

	   $mysqli = connectdb();

		 $nickname = $customer->getNickname();

	   $firstname = $customer->getFirstname();

	   $lastname = $customer->getLastname();

	   $email = $customer->getEmail();



			// Connect to the database

		$mysqli = connectdb();



		// Define the Query

		// For Windows MYSQL String is case insensitive

		 $Myquery = "SELECT count(*) as count from Customer

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



	  function insertCustomer ($customer)

	  {



			// Connect to the database

	   $mysqli = connectdb();

		 $nickname = $customer->getNickname();


		 $firstname = $customer->getFirstname();

	   $lastname = $customer->getLastname();


	   $email = $customer->getEmail();



			// Add Prepared Statement

			$Query = "INSERT INTO Customer

		          (nickname,firstname,lastname,email)

		           VALUES (?,?,?,?)";





			$stmt = $mysqli->prepare($Query);



	$stmt->bind_param("ssss",$nickname, $firstname, $lastname, $email);

	$stmt->execute();







		$stmt->close();

		$mysqli->close();



			return true;

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



	 // Class to construct Customer with getters/setter

	class CustomerClass

	{

	    // property declaration

			private $nickname="";

	    private $firstname="";

	    private $lastname="";

	    private $email="";



	    // Constructor

	    public function __construct($nickname, $firstname,$lastname,$email)

	    {

				$this->nickname = $nickname;

				$this->firstname = $firstname;

	      $this->lastname = $lastname;

	      $this->email = $email;


	    }



	    // Get methods
			public function getNickname ()

	    {

	    	return $this->nickname;

	    }

		  public function getFirstname ()

	    {

	    	return $this->firstname;

	    }

		  public function getLastname ()

	    {

	    	return $this->lastname;

	    }

		  public function getEmail ()

	    {

	    	return $this->email;

	    }







	    // Set methods
			public function setNickname ($value)

			{

				$this->nickname = $value;

			}

	    public function setFirstname ($value)

	    {

	    	$this->firstname = $value;

	    }

	    public function setLastname ($value)

	    {

	    	$this->lastname = $value;

	    }

	    public function setEmail ($value)

	    {

	    	$this->email = $value;

	    }





	} // End Customerclass



	?>

	</body>

	</html>

