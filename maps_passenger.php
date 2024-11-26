<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Route Search - Toda Management System</title>
    <link rel="stylesheet" href="bootstrap5/css/bootstrap.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <style>
        #map {
            height: 600px;
            width: 100%;
        }

        /* Center the content */
        .container-fluid {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh;
            padding-top: 20px;
        }

        .map-container {
            width: 100%;
            max-width: 1000px;
            margin-top: 20px;
        }

        /* Hide the geocode control UI */
        div.routing-leaflet-geocode {
            display: none !important;
        }
    </style>
</head>
<body>
    <!-- Header with System's Name and Icon -->
    <header class="bg-primary text-white p-3 w-100">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <img src="icon.jpg" alt="System Icon" height="40"> <!-- System Icon -->
                <h1 class="h4 mb-0">Route Search: Li-Tab Toda</h1>    
            </div>
        </div>
    </header>

    <div class="container-fluid">
        <main class="col-md-10 map-container">
            <div class="container mt-5">
                <!-- Dropdown to select destination -->
                <div class="mb-3">
                    <label for="destinationSelect" class="form-label">Select a Destination</label>
                    <select class="form-select" id="destinationSelect">
                        <option value="">-- Select Destination --</option>
                        <option value="Nasugbu Public Market">Nasugbu Public Market</option>
                        <option value="Nasugbu WalterMart">Nasugbu WalterMart</option>
                        <option value="Batangas State University- Arasof">Batangas State University- Arasof</option>
                        <!-- Add more locations as needed -->
                    </select>
                </div>

                <div id="map"></div>
            </div>
        </main>
    </div>

    <script>
        // Initialize the map centered at Kapitan Isko St., Lian, Batangas (Near Lian Pub Market)
        const map = L.map('map').setView([14.039181, 120.651441], 18);

        // Set the bounds for the map view to restrict it to Lian to Nasugbu region
        const bounds = L.latLngBounds(
            L.latLng(13.9000, 120.6000), // Southwest corner (Lian area)
            L.latLng(14.1000, 120.7500)  // Northeast corner (Nasugbu area)
        );
        map.setMaxBounds(bounds);  // Restrict the map to these bounds

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Predefined locations (e.g. Nasugbu Public Market)
        const predefinedLocations = {
            'Nasugbu Public Market': [14.070509, 120.630878], 
            'Nasugbu WalterMart': [14.056199, 120.639646], 
            'Batangas State University- Arasof': [14.067165, 120.626065] 
        };

        // Starting point (e.g., Lian Pub Market)
        const startLocation = L.latLng(14.039181, 120.651441);

        // Routing Control
        const routingControl = L.Routing.control({
            waypoints: [
                startLocation, // Starting point
                null // Destination will be added dynamically
            ],
            routeWhileDragging: true,
            geocoder: L.Control.Geocoder.nominatim(),
            show: false
        }).addTo(map);

        // A variable to store current marker so it can be removed later
        let currentMarker = null;

        // Function to update the route based on the selected destination
        function updateRoute(destination) {
            if (!destination) {
                return; // If no destination is selected, do nothing
            }

            // Get the coordinates of the selected destination
            const destinationCoords = predefinedLocations[destination];

            if (!destinationCoords) {
                alert('Selected destination is not available.');
                return;
            }

            // Clear the current marker if it exists
            if (currentMarker) {
                map.removeLayer(currentMarker);
            }

            // Set the destination in the route
            routingControl.setWaypoints([
                startLocation, // Starting point
                L.latLng(destinationCoords[0], destinationCoords[1]) // Set destination
            ]);

            // Add a new marker at the destination
            currentMarker = L.marker([destinationCoords[0], destinationCoords[1]])
                .addTo(map)
                .bindPopup(`Destination: ${destination}`)
                .openPopup();

            // Center the map to the destination
            map.setView(L.latLng(destinationCoords[0], destinationCoords[1]), 14);
        }

        // Event listener for destination selection
        document.getElementById('destinationSelect').addEventListener('change', function() {
            const selectedDestination = this.value;
            updateRoute(selectedDestination);
        });
    </script>

    <script src="bootstrap5/js/bootstrap.bundle.min.js"></script>
</body>
</html>
