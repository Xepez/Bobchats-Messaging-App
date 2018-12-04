<?php
session_start();
$_SESSION['reply'] = false;
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

/* Style the collapsible content actions. Note: hidden by default */
.actions {
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

  <form id='login' action='messages.php' method='post' accept-charset='UTF-8'>
      <fieldset>
          <legend>Search Messages</legend>
          <input type='hidden' name='submitted' id='submitted' value='1'/>
          <label for='firstname'>First Name:</label>
          <input type='text' name='firstname' id='firstname'maxlength="50"/>
          <label for='lastname'>Last Name:</label>
          <input type='text' name='lastname' id='lastname' maxlength="50"/>
          <label for='lastname'>Message Content:</label>
          <input type='text' name='messagecontent' id='messagecontent' maxlength="100"/>
          <input type='submit' name='Search' value='Search'/>
          <input type='submit' name='Clear' value='Clear'/>       

      </fieldset>
  </form>

<?php
// Echo session variables that were set on previous page
print_r($_SESSION);
$user_id = $_SESSION['userID'];
$firstname = $_SESSION['firstname'];
$lastname = $_SESSION['lastname'];
//echo 'You are signed in as ' . $firstname .  ' ' . $lastname . '.';

include 'test_con.php';

try {
	// Connect to DB
  $pdo = connect();
  
  if(isset($_POST['Reply'])) {
    echo print_r($_POST);
    $_SESSION['reply'] = true;
    $_SESSION['reply_msg_id'] = trim($_REQUEST['msg_id'], '/');
    $_SESSION['reply_user_id'] = trim($_REQUEST['creator_id'], '/');
    $_SESSION['reply_user_fname'] = trim($_POST['creator_fname'], '/');
    $_SESSION['reply_user_lname'] = trim($_POST['creator_lname'], '/');
    header('Location: message.php');
  }
  if(isset($_POST['Forward'])) {
    
  }
  if(isset($_POST['Delete'])) {
    
  }
  
  if(isset($_POST['Search'])) {
    $searchFName = trim($_POST['firstname']);
    $searchLName = trim($_POST['lastname']);
    $searchMessageContent = trim($_POST['messagecontent']);
    $messages_query = $pdo->prepare("SELECT message.create_date, sender.first_name, sender.last_name, subject, message, message.msg_id, sender.user_id
									FROM msg_recipient
									INNER JOIN message ON message.msg_id = msg_recipient.msg_id
									INNER JOIN user sender ON sender.user_id = message.creator_id
									WHERE recipient_id = ? 
                  AND (sender.first_name LIKE ? OR sender.last_name LIKE ? OR subject LIKE ? OR message LIKE ?)
									ORDER BY message.create_date DESC;");
    $messages_query->bindParam(1, $user_id);
    $messages_query->bindParam(2, $searchFName);
    $messages_query->bindParam(3, $searchLName);
    $messages_query->bindParam(4, $searchMessageContent);
    $messages_query->bindParam(5, $searchMessageContent);
  } elseif (!isset($_POST['Search']) || isset($_POST['Clear'])) {
    $messages_query = $pdo->prepare("SELECT message.create_date, sender.first_name, sender.last_name, subject, message, message.msg_id, sender.user_id
									FROM msg_recipient
									INNER JOIN message ON message.msg_id = msg_recipient.msg_id
									INNER JOIN user sender ON sender.user_id = message.creator_id
									WHERE recipient_id = ? 
									ORDER BY message.create_date DESC;");
    $messages_query->bindParam(1, $user_id);
  }
    
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
				<td colspan='3'>", $row['message'], "</td>
      </tr>
      <tr class='actions'>
        <td colspan='3'>
          <form id='message_actions' action='messages.php' method='post' accept-charset='UTF-8'>
            <fieldset>
              <legend>Message Actions</legend>
                <input type='hidden' name='msg_id' value=", $row['msg_id'] ,"/>
                <input type='hidden' name='creator_id' value=", $row['user_id'] ,"/>
                <input type='hidden' name='creator_fname' value=", $row['first_name'] ,"/>
                <input type='hidden' name='creator_lname' value=", $row['last_name'] ,"/>
                <input type='submit' name='Reply' value='Reply'/>
                <input type='submit' name='Forward' value='Forward'/>
                <input type='submit' name='Delete' value='Delete'/>
            </fieldset>
          </form>
        </td>
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
    var actions = content.nextElementSibling;
    if (content.style.display === "table-row") {
      content.style.display = "none";
      actions.style.display = "none";
    } else {
      content.style.display = "table-row";
      actions.style.display = "table-row";
    }
  });
}
</script>
</body>
</html>
