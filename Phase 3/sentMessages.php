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
  background-color: #DFDFDF;
  color: #444;
  cursor: pointer;
}

/* Style the collapsible content actions. Note: hidden by default */
.actions {
  padding: 0 18px;
  display: none;
  overflow: hidden;
  background-color: #f1f1f1;
}

.button {
  color: #444;
  cursor: pointer;
}

/* Add a background color to the button if it is clicked on (add the .active class with JS), and when you move the mouse over it (hover) */
.active, .activecontent:hover, .content:hover, .collapsible:hover {
  background-color: #ccc;
}
.activecontent {
  background-color: #DFDFDF;
}

</style>
</head>
<body>

  <form id='search' action='messages.php' method='post' accept-charset='UTF-8'>
      <fieldset>
          <legend>Search Messages</legend>
          <input type='hidden' name='submitted' id='submitted' value='1'/>
          <label for='firstname'>First Name:</label>
          <input type='text' name='firstname' id='firstname'maxlength="50"/>
          <label for='lastname'>Last Name:</label>
          <input type='text' name='lastname' id='lastname' maxlength="50"/>
          <label for='messagecontent'>Message Content:</label>
          <input type='text' name='messagecontent' id='messagecontent' maxlength="100"/>
          <input type='submit' name='Search' value='Search'/>
          <input type='submit' name='Clear' value='Clear'/>       

      </fieldset>
  </form>

<?php
// Echo session variables that were set on previous page
//print_r($_SESSION);
$user_id = $_SESSION['userID'];
$firstname = $_SESSION['firstname'];
$lastname = $_SESSION['lastname'];
//echo 'You are signed in as ' . $firstname .  ' ' . $lastname . '.';

include 'test_con.php';

try {
	// Connect to DB
  $pdo = connect();
  
  if(isset($_POST['View_Attachment'])) {
    $_SESSION['view_attach_id'] = trim($_POST['msg_attach_id'], '/');
    header('Location: viewAttach.php');
  }
  
  if(isset($_POST['Search'])) {
    $searchFName = trim($_POST['firstname']);
    $searchLName = trim($_POST['lastname']);
    $searchMessageContent = trim($_POST['messagecontent']);
    $messages_query = $pdo->prepare("SELECT DISTINCT message.create_date, sender.first_name, sender.last_name, subject, message, message.msg_id, sender.user_id, attach_id
									FROM msg_recipient
									INNER JOIN message ON message.msg_id = msg_recipient.msg_id
									INNER JOIN user sender ON sender.user_id = message.creator_id
									WHERE message.creator_id = ? 
                  AND (sender.first_name LIKE ? OR sender.last_name LIKE ? OR subject LIKE ? OR message LIKE ?)
									ORDER BY message.create_date DESC;");
    $messages_query->bindParam(1, $user_id);
    $messages_query->bindParam(2, $searchFName);
    $messages_query->bindParam(3, $searchLName);
    $messages_query->bindParam(4, $searchMessageContent);
    $messages_query->bindParam(5, $searchMessageContent);
  } elseif (!isset($_POST['Search']) || isset($_POST['Clear'])) {
    
    $messages_query = $pdo->prepare("SELECT DISTINCT message.create_date, sender.first_name, sender.last_name, subject, message, message.msg_id, sender.user_id, attach_id
									FROM msg_recipient
									    INNER JOIN message ON message.msg_id = msg_recipient.msg_id
									    INNER JOIN user sender ON sender.user_id = message.creator_id
                                    WHERE message.creator_id = ? 
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
			<th>To</th>
			<th>Subject</th>
		</tr>";
	foreach ( $result as $row ) {
    echo "
    <tbody class='msg'>
			<tr class='collapsible'>
				<td>", $row['create_date'], "</td>
				<td>", $row['first_name'], ' ', $row['last_name'], "</td>
				<td>", $row['subject'], "</td>
			</tr>	
			<tr class='content'>
				<td colspan='3'>", $row['message'], "</td>
      </tr>
      <tr class='actions'>
        <td colspan='3'>";
        if ($row['attach_id'] != null) echo "
          <form id='message_actions' action='messages.php' method='post' accept-charset='UTF-8'>
            <fieldset>
              <legend>Message Actions</legend>
                <input type='hidden' name='msg_id' value=", $row['msg_id'] ,"/>
                <input type='hidden' name='msg_attach_id' value=", $row['attach_id'] ,"/>
                <input type='submit' name='View_Attachment' value='View Attachment'/>
            </fieldset>
          </form>";
        echo "</td>
      </tr>
    </tbody>
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
var coll2 = document.getElementsByClassName("content");
var i;

for (i = 0; i < coll.length; i++) {
  coll[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var content = this.nextElementSibling;
    var actions = content.nextElementSibling;
    if (content.style.display === "table-row") {
      content.style.display = "none";
      actions.style.display = "none";
      this.parentElement.style.border = "none";
    } else {
      content.style.display = "table-row";
      this.parentElement.style.border = "2px solid #000000";
    }
  });
  coll2[i].addEventListener("click", function() {
    this.classList.toggle("activecontent");
    var actions = this.nextElementSibling;
    if (actions.style.display === "table-row") {
      actions.style.display = "none";
    } else {
      actions.style.display = "table-row";
    }
  });
}
</script>
  <br><br>
  <form id='buttons' action='home.php' method='post'>
    <input type='submit' name='home' value='Home'/>
  </form>

</body>
</html>
