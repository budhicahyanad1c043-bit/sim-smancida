<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Atur Titik Lokasi & Radius Absensi') }}
        </h2>
    </x-slot>

    <!-- Leaflet CSS & JS untuk Interactive Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if (session('success'))
            <div class="p-4 text-sm text-emerald-800 rounded-2xl bg-emerald-50 border border-emerald-200">
                <span class="font-bold">Berhasil!</span> {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Form Input Titik Koordinat -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Pengaturan Koordinat</h3>
                <p class="text-xs text-gray-500">Geser penanda di peta atau klik area sekolah untuk mendapatkan Latitude & Longitude secara presisi.</p>

                <form action="{{ route('admin.lokasi.update') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Latitude Sekolah</label>
                        <input type="text" id="latitude_sekolah" name="latitude_sekolah" value="{{ $latitude }}" required
                            class="w-full text-xs border-gray-200 rounded-xl bg-gray-50/50 py-2.5 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Longitude Sekolah</label>
                        <input type="text" id="longitude_sekolah" name="longitude_sekolah" value="{{ $longitude }}" required
                            class="w-full text-xs border-gray-200 rounded-xl bg-gray-50/50 py-2.5 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Max Radius Absen (Meter)</label>
                        <input type="number" id="radius_meter" name="radius_meter" value="{{ $radius }}" min="10" required
                            class="w-full text-xs border-gray-200 rounded-xl bg-gray-50/50 py-2.5 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold py-2.5 rounded-xl transition-all shadow-sm">
                            Simpan Lokasi Absensi
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tampilan Interactive Leaflet Map -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-4 min-h-[400px]">
                <div id="map" class="w-full h-[450px] rounded-xl"></div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let lat = parseFloat(document.getElementById('latitude_sekolah').value) || -6.200000;
            let lng = parseFloat(document.getElementById('longitude_sekolah').value) || 106.816666;
            let rad = parseFloat(document.getElementById('radius_meter').value) || 100;

            const map = L.map('map').setView([lat, lng], 17);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);

            let marker = L.marker([lat, lng], { draggable: true }).addTo(map);
            let circle = L.circle([lat, lng], { radius: rad, color: '#4f46e5', fillColor: '#6366f1', fillOpacity: 0.2 }).addTo(map);

            function updateInputs(latVal, lngVal) {
                document.getElementById('latitude_sekolah').value = latVal.toFixed(6);
                document.getElementById('longitude_sekolah').value = lngVal.toFixed(6);
                circle.setLatLng([latVal, lngVal]);
            }

            marker.on('dragend', function (e) {
                let position = marker.getLatLng();
                updateInputs(position.lat, position.lng);
            });

            map.on('click', function (e) {
                marker.setLatLng(e.latlng);
                updateInputs(e.lat, e.lng);
            });

            document.getElementById('radius_meter').addEventListener('input', function (e) {
                let newRad = parseFloat(e.target.value) || 0;
                circle.setRadius(newRad);
            });
        });
    </script>
</x-app-layout>