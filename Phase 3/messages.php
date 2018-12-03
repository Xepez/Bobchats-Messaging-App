<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
<style>
table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

td, th {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
}

tr:nth-child(even) {
    background-color: #dddddd;
}

/* Style the button that is used to open and close the collapsible content */
.collapsible {
  background-color: #eee;
  color: #444;
  cursor: pointer;
  padding: 8px;
  border: none;
  text-align: left;
  outline: none;
  font-size: 15px;
}

/* Style the collapsible content. Note: hidden by default */
.content {
  padding: 0 18px;
  display: none;
  overflow: hidden;
  background-color: #f1f1f1;
}

/* Add a background color to the button if it is clicked on (add the .active class with JS), and when you move the mouse over it (hover) */
.active, .collapsible:hover {
  background-color: #ccc;
}
</style>
</head>
<body>

<?php
// Echo session variables that were set on previous page
//print_r($_SESSION);
$user_id = $_SESSION['userID'];
$firstname = $_SESSION['firstname'];
$lastname = $_SESSION['lastname'];
echo 'You are signed in as ' . $firstname .  ' ' . $lastname . '.';


include 'test_con.php';

try {
	// Connect to DB
	$pdo = connect();
	
    $messages_query = $pdo->prepare("SELECT message.create_date, sender.first_name, sender.last_name, subject, message
									FROM msg_recipient
									INNER JOIN message ON message.msg_id = msg_recipient.msg_id
									INNER JOIN user sender ON sender.user_id = message.creator_id
									WHERE recipient_id = ?
									ORDER BY message.create_date DESC;");
    $messages_query->bindParam(1, $user_id);
    
	/* execute statement */
    $messages_query->execute();
	
	$result = $messages_query->fetchAll();
	/* fetch values */
	echo "
	<h2>Recieved Messages</h2>
	<table>
		<tr>
			<th>Date</th>
			<th>From</th>
			<th>Subject</th>
		</tr>";
	foreach ( $result as $row ) {
		echo "
			<tr class='collapsible'>
				<td>", $row['create_date'], "</td>
				<td>", $row['first_name'], ' ', $row['last_name'], "</td>
				<td>", $row['subject'], "</td>
			</tr>
			
			<tr class='content'>
				<td width='90%'; colspan='2'>", $row['message'], "</td>
				<td>Reply</td>
			</tr>
		";
	}
	echo "
	</table>";
    //$messages_query->bind_result($date, $fname, $lname, $subject, $message);
	
     /* fetch values */
    //while ($messages_query->fetch()) {
    //    printf ("%s %s\n", $fname, $lname);
    //}
	
	
} catch(PDOException $e) {
    echo $e;
}
?>
<script>
var coll = document.getElementsByClassName("collapsible");
var i;

for (i = 0; i < coll.length; i++) {
  coll[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var content = this.nextElementSibling;
    if (content.style.display === "table-row") {
      content.style.display = "none";
    } else {
      content.style.display = "table-row";
    }
  });
}
</script>
</body>
</html>
