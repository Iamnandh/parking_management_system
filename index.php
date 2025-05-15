<?php
include 'connection.php';

// Fetch occupied slots from DB (remove EntryTime)
$occupiedSlots = [];
$sql = "SELECT SlotNumber, VehicleType FROM parking_entry";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $occupiedSlots[] = [
            'SlotNumber' => (int)$row['SlotNumber'],
            'VehicleType' => $row['VehicleType']
            // 'EntryTime' => $row['EntryTime'] // removed
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Car Parking System</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* ...existing code... */
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .parking-form-container {
            background: #fff;
            max-width: 400px;
            margin: 50px auto;
            padding: 30px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .parking-form-container h2 {
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }
        .parking-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        .parking-form input, .parking-form select {
            width: 100%;
            padding: 12px;
            margin-bottom: 16px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        .parking-form input:focus, .parking-form select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.1);
        }
        .parking-form button {
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s, transform 0.1s;
        }
        .parking-form button:hover {
            background: #0056b3;
            transform: translateY(-1px);
        }
        .slots-container {
            max-width: 1000px;
            margin: 40px auto;
            display: flex;
            gap: 40px;
            justify-content: center;
            align-items: flex-start;
            padding: 0 20px;
        }
        .slots-container h3 {
            margin-bottom: 15px;
            text-align: center;
            color: #333;
            font-size: 1.4em;
        }
        .slots-container ul {
            list-style-type: none;
            padding: 0;
            width: 100%;
        }
        .slots-container li {
            padding: 15px;
            margin-bottom: 12px;
            background: #fff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }
        .slots-container li:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .car-image {
            width: 24px;
            height: 24px;
            margin-right: 12px;
        }
        .slot-label {
            font-weight: bold;
            color: #444;
            min-width: 80px;
        }
        .timer {
            font-family: monospace;
            font-size: 1.1em;
            color: #666;
            margin: 0 15px;
        }
        .button-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .slot-details-btn, .stop-timer-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        .slot-details-btn {
            background: #4CAF50;
            color: white;
        }
        .slot-details-btn:hover {
            background: #45a049;
            transform: translateY(-1px);
        }
        .stop-timer-btn {
            background: #ff4444;
            color: white;
        }
        .stop-timer-btn:hover {
            background: #cc0000;
            transform: translateY(-1px);
        }
        .stop-timer-btn:disabled {
            background: #cccccc;
            cursor: not-allowed;
            transform: none;
        }
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
        }
        .modal-content {
            background: #fff;
            margin: 15% auto;
            padding: 25px;
            width: 90%;
            max-width: 500px;
            border-radius: 12px;
            position: relative;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .modal-content h3 {
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }
        .modal-content p {
            margin: 12px 0;
            font-size: 16px;
            color: #555;
        }
        .modal-content strong {
            color: #333;
        }
        .close {
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #666;
            transition: color 0.3s;
        }
        .close:hover {
            color: #333;
        }
        #availableSlotsCount, #occupiedSlotsCount {
            text-align: center;
            margin-bottom: 15px;
            font-size: 1.1em;
            color: #666;
        }
        .available-slots, .occupied-slots {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
        }
    </style>
</head>
<body>
    <div class="parking-form-container">
        <h2>Car Parking Entry</h2>
        <form class="parking-form" id="parkingForm" method="POST" action="carentry.php">
            <label for="vehicleType">Vehicle Type</label>
            <select id="vehicleType" name="VehicleType" required>
                <option value="">Select vehicle type</option>
                <option value="car">Car</option>
                <option value="truck">Truck</option>
            </select>
            <label for="slotNumber">Slot Number</label>
            <input type="number" id="slotNumber" name="SlotNumber" min="1" required placeholder="Enter slot number">
            <button type="submit">Park Vehicle</button>
        </form>
    </div>

    <!-- Modal for Details -->
    <div id="detailsModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3>Parking Details</h3>
            <div id="modalContent">
                <!-- Details will be inserted here -->
            </div>
        </div>
    </div>

    <div class="slots-container">
        <div class="available-slots">
            <h3>Available Slots</h3>
            <div id="availableSlotsCount"></div>
            <ul id="availableSlotsList">
                <!-- Available slots will be listed here -->
            </ul>
        </div>
        <div class="occupied-slots">
            <h3>Occupied Slots</h3>
            <div id="occupiedSlotsCount"></div>
            <ul id="occupiedSlotsList">
                <!-- Occupied slots will be listed here -->
            </ul>
        </div>
    </div>
    <script>
        // PHP data to JS
        const totalSlots = 10;
        const occupiedSlotsData = <?php echo json_encode($occupiedSlots); ?>;
        const RATE_PER_HOUR = 10;

        // Map slot numbers to their data
        const occupiedSlotsMap = {};
        occupiedSlotsData.forEach(slot => {
            occupiedSlotsMap[slot.SlotNumber] = slot;
        });

        function formatTime(seconds) {
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;
            return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        }

        function calculateCost(seconds) {
            const hours = Math.ceil(seconds / 3600);
            return hours * RATE_PER_HOUR;
        }

        function showDetails(slotNumber) {
            const slot = occupiedSlotsMap[slotNumber];
            let vehicleType = slot ? slot.VehicleType : '';
            let status = slot ? 'Active' : 'Available';

            // Remove EntryTime/cost logic
            let timeParked = 'N/A';
            let cost = 'N/A';

            document.getElementById('modalContent').innerHTML = slot
                ? `<p><strong>Slot Number:</strong> ${slotNumber}</p>
                   <p><strong>Vehicle Type:</strong> ${vehicleType}</p>
                   <p><strong>Status:</strong> ${status}</p>`
                : `<p>Slot ${slotNumber} is available.</p>`;
            document.getElementById('detailsModal').style.display = 'block';
        }

        // Render slots
        function renderSlots() {
            const availableSlotsList = document.getElementById('availableSlotsList');
            const occupiedSlotsList = document.getElementById('occupiedSlotsList');
            const availableSlotsCount = document.getElementById('availableSlotsCount');
            const occupiedSlotsCount = document.getElementById('occupiedSlotsCount');
            availableSlotsList.innerHTML = '';
            occupiedSlotsList.innerHTML = '';
            let availableCount = 0, occupiedCount = 0;

            for (let i = 1; i <= totalSlots; i++) {
                if (occupiedSlotsMap[i]) {
                    const li = document.createElement('li');
                    li.innerHTML = `
                        <span class="slot-label">Slot ${i}</span>
                        <button class="slot-details-btn" onclick="showDetails(${i})">View Details</button>
                    `;
                    occupiedSlotsList.appendChild(li);
                    occupiedCount++;
                } else {
                    const li = document.createElement('li');
                    li.innerHTML = `
                        Slot ${i}
                        <button class="slot-details-btn" onclick="showDetails(${i})">View Details</button>
                    `;
                    availableSlotsList.appendChild(li);
                    availableCount++;
                }
            }
            availableSlotsCount.textContent = `Count: ${availableCount}`;
            occupiedSlotsCount.textContent = `Count: ${occupiedCount}`;
        }

        // Modal close functionality
        document.querySelector('.close').onclick = function() {
            document.getElementById('detailsModal').style.display = 'none';
        }
        window.onclick = function(event) {
            const modal = document.getElementById('detailsModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        renderSlots();
    </script>
</body>
</html>