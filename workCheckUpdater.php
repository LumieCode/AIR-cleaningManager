<?php
$id = $_POST['id'];

// Connect to database
$link = mysqli_connect("localhost", "iva", "12345", "cw");
if (!$link) {
    die("Connection failed: " . mysqli_connect_error());
}

// Prepare and execute query
$stmt = mysqli_prepare($link, "UPDATE clean_up SET checked = ?, date_Checked = ? WHERE id = ?");
$idSend = mysqli_real_escape_string($link, $id);
$completionStatusSend = 1;
$date_Checked = gmdate('Y-m-d H:i:s', time() + 2*60*60); // Egypt time is 2 hours ahead of GMT
mysqli_stmt_bind_param($stmt, "isi", $completionStatusSend, $date_Checked, $idSend);
if (mysqli_stmt_execute($stmt)) {
    // Query successful
    $successMsg = 'Record updated successfully';
} else {
    // Query failed
    $errorMsg = 'Error updating record: ' . mysqli_error($link);
}

// Close statement and connection
mysqli_stmt_close($stmt);
mysqli_close($link);

// Display messages
if (isset($successMsg)) {
    echo '<div style="background-color: lightgreen; padding: 10px;">' . $successMsg . '</div>';
} else if (isset($errorMsg)) {
    echo '<div style="background-color: lightcoral; padding: 10px;">' . $errorMsg . '</div>';
} else {
    echo 'This should be working';
}
?>
