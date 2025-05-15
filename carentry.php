<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $VehicleType = $_POST['VehicleType'];
    $SlotNumber = $_POST['SlotNumber'];
    

    $sql = "INSERT INTO parking_entry (VehicleType, SlotNumber) VALUES ('$VehicleType', '$SlotNumber')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('Slot Updated');
                window.location.href = 'index.php';
              </script>";
    } else {
        echo "<script>
                alert('Error: " . $conn->error . "');
                window.location.href = 'index.php';
              </script>";
    }
    $conn->close();
}
?>