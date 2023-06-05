<?php
$conn = mysqli_connect("localhost", "iva", "12345", "cw");


$sql = "SELECT * FROM dle_users";
if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $query = "SELECT time_end FROM log_action WHERE task_id = 5 AND user_id = " . $row["user_id"];
        $resEndTime = $conn->query($query);
        if ($resEndTime->num_rows > 0) {
            $resEndTimeArray = $resEndTime->fetch_assoc();
            $given_datetime = $resEndTimeArray['time_end'];
            $currentDateTime = new DateTime();
            $given_datetime = new DateTime($given_datetime);
            $timeDifference = $currentDateTime->diff($given_datetime);
            
            if ($timeDifference->days < 7) {

                $stmt = $conn->prepare("UPDATE dle_users SET cleanerPosition = ? WHERE user_id = ?");
                $completionStatusSend = 1;
                $who_idSend = $row["user_id"];
                $stmt->bind_param("ii", $completionStatusSend, $who_idSend);
                
                if ($stmt->execute()) {
                    $successMsg = 'Record updated successfully';
                } else {
                    $errorMsg = 'Error updating record: ' . $stmt->error;
                }
            } else {
                $stmt = $conn->prepare("UPDATE dle_users SET cleanerPosition = ? WHERE user_id = ?");
                $completionStatusSend = 0;
                $who_idSend = $row["user_id"];
                $stmt->bind_param("ii", $completionStatusSend, $who_idSend);
                
                if ($stmt->execute()) {
                    $successMsg = 'Record updated successfully';
                } else {
                    $errorMsg = 'Error updating record: ' . $stmt->error;
                }
            }
        }
    }
}



$sql = "SELECT date_Checked FROM clean_up WHERE 1=1";
$date_checked_result = $conn->query($sql);
$date_checked_results= mysqli_fetch_all($date_checked_result , MYSQLI_ASSOC);
$date_checked_results_array= array_column($date_checked_results, 'date_Checked');
$dateChecked = $date_checked_results_array[0];
 $today = date('Y-m-d');
if($today != $dateChecked){
$sql = "SELECT user_id FROM dle_users WHERE cleanerPosition = 1 ORDER BY difficulty_Elo";
$result = $conn->query($sql);
$count = $result->num_rows;
$user_ids = array(); // Initialize an empty array

if ($count > 0) {
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $user_ids = array_column($rows, 'user_id');
}

$query = "SELECT name FROM clean_up  ORDER BY difficulty DESC";
$res =  $conn->query($query);
$clean_up = $res->num_rows;
$clean_upNames = array(); // Initialize an empty array

if ($clean_up > 0) {
    $res_rows = mysqli_fetch_all($res, MYSQLI_ASSOC);
    $clean_upNames = array_column($res_rows, 'name');
}

$taskGroupRemainder = $clean_up % $count;

$taskGroupSize = ($clean_up - $taskGroupRemainder) / $count;

$currentRegressor = 0;
$regressionsLeftUntilTheNextRegressor = $taskGroupSize;// i was binging ORV the day before :)
for ($i = 0; $i < count($clean_upNames); $i++) {
	
	if($regressionsLeftUntilTheNextRegressor == 0){
		$regressionsLeftUntilTheNextRegressor = $taskGroupSize;
		$currentRegressor++;
	}

$sql = "UPDATE clean_up SET who_id="  .  $user_ids[$currentRegressor]  .  " WHERE name='"  .  $clean_upNames[$i] . "'";
	$conn->query($sql);
	$regressionsLeftUntilTheNextRegressor--;
	
$sql = "SELECT difficulty FROM clean_up WHERE name = '" . $clean_upNames[$i] . "'";
$task_difficulty_result = $conn->query($sql);
    $task_difficulty_row = $task_difficulty_result->fetch_assoc();
    $task_difficulty = $task_difficulty_row['difficulty'];
   
$sql = "UPDATE dle_users SET difficulty_Elo = difficulty_Elo + " . $task_difficulty . " WHERE user_id = '" . $user_ids[$currentRegressor] . "'";
$conn->query($sql);
}
}





$sql = "SELECT clean_up.name, id, who_id, when_, active, difficulty, date_Checked, dle_users.name AS uname 
        FROM clean_up 
        LEFT JOIN dle_users ON dle_users.user_id = who_id
        ORDER BY difficulty DESC";
// gets a bunch of stuff from the table
if ($result = $conn->query($sql)) {

    $rowsCount = $result->num_rows; // количество полученных строк
    echo "<p>Получено объектов: $rowsCount</p>";
    echo "<table><tr><th>id</th><th>name</th><th>username</th><th>who_id</th></tr>";

    $i = 0;
    foreach ($result as $row) {
        $appliedStyle = "background-color:#FF5C5C"; // light red colour

        // gets the name of the person based on who_id
        $query = "SELECT name FROM dle_users WHERE user_id =" . $row["who_id"];
        $res = mysqli_query($conn, $query);
        $resArray = mysqli_fetch_array($res);

        date_default_timezone_set('Africa/Cairo');

        // Get today's date in the format "Y-m-d" (e.g. 2023-03-23)
        $today = date('Y-m-d');

        // gets the time the user finished his daily duty
        $query = "SELECT time_end FROM log_action WHERE task_id = 5 and user_id = " . $row["who_id"];
        $resEndTime = mysqli_query($conn, $query);
        $resEndTimeArray = mysqli_fetch_array($resEndTime);
        $given_datetime = $resEndTimeArray['time_end'];

        // Transforms the date time into a date
        $given_date = date('Y-m-d', strtotime($given_datetime));

        // Compare today's date with the given date
        if ($given_date == $today){
			$task_accomplished_today = true;
		if ($row['date_Checked'] == $today){
            $checkedToday = true;
            $reset == false;
			}
    else {
            $reset = true;
        }
		}
		else{
		$task_accomplished_today = false;
		}

        if ($reset) {
            // Prepare and execute query
            $stmt = mysqli_prepare($conn, "UPDATE clean_up SET checked = ?, date_Checked = ? WHERE who_id = ?");
            $who_idSend = mysqli_real_escape_string($conn, $who_id);
            $completionStatusSend = 0;
            $date_Checked = gmdate('Y-m-d H:i:s', time() + 2 * 60 * 60); // Egypt time is 2 hours ahead of GMT
            mysqli_stmt_bind_param($stmt, "isi", $completionStatusSend, $date_Checked, $row['who_id']);
            if (mysqli_stmt_execute($stmt)) {
                // Query successful
                $successMsg = 'Record updated successfully';
            } else {
                // Query failed
                $errorMsg = 'Error updating record: ' . mysqli_error($conn);
            }
        }
       
        $query = "SELECT id FROM log_action WHERE task_id = 5 and user_id = " . $row["who_id"];
        // gets all the daily duty activities of the currently processed user
        $res = mysqli_query($conn, $query);

        

		
        if ($res->num_rows > 0 && $task_accomplished_today == true) {
            $query = "SELECT checked FROM clean_up WHERE checked = 1 and id =" . $row["id"];
            $res = mysqli_query($conn, $query);
            if ($res->num_rows > 0 && $checkedToday) {
                $appliedStyle = "background-color:lightblue";
            } else {
                $appliedStyle = "background-color:lightgreen";
            }
        }
        $i = $i + 1;
        echo "<tr>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td class='locations' onclick='updateCompletionStatus(" . $row["id"] . ")'>" . $row["name"] . "</td>";
        echo "<td style='$appliedStyle' class='hobbits' data-userid=" . $row["who_id"] . " onclick='jf(" . $row["id"] . ")'  id=" . $row["id"] . ">" . $resArray[0] . "</td> ";
        echo "<td " . "class='executorIds'>" . $row["who_id"] . "</td>";
        echo "<td class='difficulty'>" . $row['difficulty'] . "</td>";
		
		
		
		
    }
    echo "</table>";
echo '<button onclick="resetELOscores()">Reset ELO here</button>';
    $result->free();
}
mysqli_close($conn);
?>
