<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register with Map</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-lg space-y-6">
        <h2 class="text-2xl font-bold text-center mb-4">Register User</h2>

        <form id="registerForm"  method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block font-medium">Role</label>
                <select name="role" class="border p-2 rounded w-full" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="warga">Warga</option>
                    <option value="bank_sampah">Bank Sampah</option>
                </select>
            </div>

            <div>
                <label class="block font-medium">Username</label>
                <input type="text" name="username" class="border p-2 rounded w-full" required>
            </div>

            <div>
                <label class="block font-medium">Email</label>
                <input type="email" name="email" class="border p-2 rounded w-full" required>
            </div>

            <div>
                <label class="block font-medium">Password</label>
                <input type="password" name="password" class="border p-2 rounded w-full" required>
            </div>

            <div>
                <label class="block font-medium">Alamat</label>
                <input type="text" name="address" class="border p-2 rounded w-full" placeholder="Masukkan alamat lengkap..." required>
                <button type="button" id="findLocation" class="mt-2 bg-blue-500 text-white px-3 py-1 rounded">Cari Lokasi</button>
            </div>

            <input type="hidden" name="latitude">
            <input type="hidden" name="longitude">

            <div id="map" class="h-60 w-full rounded"></div>

            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded w-full">Daftar</button>
        </form>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        let map = L.map('map').setView([-6.2, 106.8], 11); // Jakarta default
        let marker;

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Tombol cari lokasi berdasarkan alamat
        document.getElementById("findLocation").addEventListener("click", async () => {
            const address = document.querySelector("[name='address']").value;
            if (!address) return alert("Isi alamat dulu!");

            const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}`);
            const data = await response.json();

            if (data.length === 0) return alert("Alamat tidak ditemukan!");

            const { lat, lon, display_name } = data[0];
            document.querySelector("[name='latitude']").value = lat;
            document.querySelector("[name='longitude']").value = lon;

            // tampilkan di peta
            if (marker) map.removeLayer(marker);
            marker = L.marker([lat, lon]).addTo(map).bindPopup(display_name).openPopup();
            map.setView([lat, lon], 15);
        });

        // Klik manual di map
        map.on('click', function(e) {
            const { lat, lng } = e.latlng;
            document.querySelector("[name='latitude']").value = lat;
            document.querySelector("[name='longitude']").value = lng;

            if (marker) map.removeLayer(marker);
            marker = L.marker([lat, lng]).addTo(map).bindPopup(`Lat: ${lat}, Lng: ${lng}`).openPopup();
        });
    </script>
</body>
</html>
