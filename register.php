<?php
class PDOConnect {
	
	private $dsn, $host, $db, $username, $password, $conn;
	
	public function __construct($host, $db, $username, $password) {
		$this->host = $host;
		$this->db = $db;
		$this->username = $username;
		$this->password = $password;
		$this->dsn = "mysql:host={$this->host};dbname={$this->db}";
		
		$this->conn = new PDO($this->dsn, $this->username, $this->password);
	}

	public function registerUser($params) {
		try {
		    // execute the stored procedure
		    $sql = 'CALL sp_register(:personal_id,:firstname,:lastname,:salutation,:gender,:comment,@existing)';
		    $stmt = $this->conn->prepare($sql);
		 
			//bind all parameters 
		    $stmt->bindParam(':personal_id', $params['personal_id'], PDO::PARAM_INT);
		    $stmt->bindParam(':firstname', $params['firstname'], PDO::PARAM_STR);
		    $stmt->bindParam(':lastname', $params['lastname'], PDO::PARAM_STR);
		    $stmt->bindParam(':salutation', $params['salutation'], PDO::PARAM_STR);
		    $stmt->bindParam(':gender', $params['gender'], PDO::PARAM_STR);
		    $stmt->bindParam(':comment', $params['comment'], PDO::PARAM_STR);

		    //execute the prepared statement
		    $stmt->execute();
		    $stmt->closeCursor();

		    // execute the second query to get customer's level
		    $r = $this->conn->query("SELECT @existing AS existing")->fetch(PDO::FETCH_ASSOC);
		    if ($r) {
		    	return $r['existing'];
		    }
		} catch (PDOException $pe) {
		    return $pe->getMessage();
		}
	}
}

//supposing that the database connection comes from a config file
$host = 'localhost';
$dbname = 'exam';
$username = 'root';
$password = '';

//if (isset($_POST['submit'])) {
	//now let's connect to our database using the class above
	$DB = new PDOConnect($host, $dbname, $username, $password);
	$params = array(
		'personal_id' => !empty($_POST['personal_id']) ? $_POST['personal_id'] : 0,
		'firstname' => !empty($_POST['firstname']) ? $_POST['firstname'] : '',
		'lastname' => !empty($_POST['lastname']) ? $_POST['lastname'] : '',
		'salutation' => !empty($_POST['salutation']) ? $_POST['salutation'] : '',
		'gender' => !empty($_POST['gender']) ? $_POST['gender'] : '',
		'comment' => !empty($_POST['comment']) ? $_POST['comment'] : '',
	);
	$existing = $DB->registerUser($params);	

	$result['success'] = ($existing == 'N') ? true : false;
	$result['error'] = ($existing == 'Y') ? " Duplicate Personal Id" : ($existing != 'N') ? $existing  : null;

	echo json_encode($result);
//}
