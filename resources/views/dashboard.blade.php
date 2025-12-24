@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-6">📊 MikroTik Dashboard</h1>

{{-- INTERFACE TABLE --}}
<div class="bg-white shadow rounded-lg p-4 mb-8">
    <h2 class="text-xl font-semibold mb-4">Interfaces</h2>

    <table class="w-full text-sm border">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-2 border">Name</th>
                <th class="p-2 border">Type</th>
                <th class="p-2 border">RX (Bytes)</th>
                <th class="p-2 border">TX (Bytes)</th>
                <th class="p-2 border">Status</th>
            </tr>
        </thead>
        <tbody id="interfaces-table">
            <tr>
                <td colspan="5" class="text-center p-4">Loading...</td>
            </tr>
        </tbody>
    </table>
</div>


{{-- IP ADDRESS TABLE --}}
<div class="bg-white shadow rounded-lg p-4 mb-8">
    <h2 class="text-xl font-semibold mb-4">IP Address</h2>

    <table class="w-full text-sm border">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-2 border">Address</th>
                <th class="p-2 border">Network</th>
                <th class="p-2 border">Interface</th>
                <th class="p-2 border">Dynamic</th>
                <th class="p-2 border">Status</th>
            </tr>
        </thead>
        <tbody id="ipaddress-table">
            <tr>
                <td colspan="5" class="text-center p-4">Loading...</td>
            </tr>
        </tbody>
    </table>
</div>



{{-- IP NEIGHBOURS TABLE --}}
<div class="bg-white shadow rounded-lg p-4 mb-8">
    <h2 class="text-xl font-semibold mb-4">IP Neighbours</h2>

    <table class="w-full text-sm border">
        <thead class="bg-gray-200">
            <tr>
                <th class="p-2 border">Identity</th>
                <th class="p-2 border">IP Address</th>
                <th class="p-2 border">Interface</th>
                <th class="p-2 border">Platform</th>
                <th class="p-2 border">Version</th>
                <th class="p-2 border">Board</th>
            </tr>
        </thead>
        <tbody id="neighbour-table">
            <tr>
                <td colspan="6" class="text-center p-4">Loading...</td>
            </tr>
        </tbody>
    </table>
</div>






{{-- TRAFFIC CHART --}}
<div class="bg-white shadow rounded-lg p-4">
    <h2 class="text-xl font-semibold mb-4">Realtime Traffic (ether1)</h2>
    <canvas id="trafficChart" height="100"></canvas>
</div>

<script>
const API_BASE = '/api/mikrotik';

/* =======================
   LOAD INTERFACES
======================= */
fetch(API_BASE + '/interfaces')
    .then(res => res.json())
    .then(res => {
        const tbody = document.getElementById('interfaces-table');
        tbody.innerHTML = '';

        res.data.forEach(iface => {
            tbody.innerHTML += `
                <tr class="border-b">
                    <td class="p-2">${iface.name}</td>
                    <td class="p-2">${iface.type}</td>
                    <td class="p-2">${Number(iface['rx-byte']).toLocaleString()}</td>
                    <td class="p-2">${Number(iface['tx-byte']).toLocaleString()}</td>
                    <td class="p-2">
                        <span class="${iface.running === 'true' ? 'text-green-600' : 'text-red-600'}">
                            ${iface.running === 'true' ? 'UP' : 'DOWN'}
                        </span>
                    </td>
                </tr>
            `;
        });
    });

/* =======================
   REALTIME TRAFFIC
======================= */
const ctx = document.getElementById('trafficChart').getContext('2d');

const trafficChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [],
        datasets: [
            {
                label: 'RX bps',
                data: [],
                borderWidth: 2
            },
            {
                label: 'TX bps',
                data: [],
                borderWidth: 2
            }
        ]
    },
    options: {
        responsive: true,
        animation: false,
        scales: {
            y: { beginAtZero: true }
        }
    }
});

setInterval(() => {
    fetch(API_BASE + '/traffic/ether1')
        .then(res => res.json())
        .then(res => {
            const d = res.data[0];
            const now = new Date().toLocaleTimeString();

            trafficChart.data.labels.push(now);
            trafficChart.data.datasets[0].data.push(d['rx-bits-per-second'] || 0);
            trafficChart.data.datasets[1].data.push(d['tx-bits-per-second'] || 0);

            if (trafficChart.data.labels.length > 20) {
                trafficChart.data.labels.shift();
                trafficChart.data.datasets.forEach(ds => ds.data.shift());
            }

            trafficChart.update();
        });
}, 2000);
</script>


<script>
/* =======================
   LOAD IP ADDRESSES
======================= */
fetch(API_BASE + '/ip-addresses')
    .then(res => res.json())
    .then(res => {
        const tbody = document.getElementById('ipaddress-table');
        tbody.innerHTML = '';

        res.data.forEach(ip => {
            tbody.innerHTML += `
                <tr class="border-b">
                    <td class="p-2">${ip.address}</td>
                    <td class="p-2">${ip.network}</td>
                    <td class="p-2">${ip.interface}</td>
                    <td class="p-2">
                        <span class="${ip.dynamic === 'true' ? 'text-yellow-600' : 'text-blue-600'}">
                            ${ip.dynamic === 'true' ? 'YES' : 'NO'}
                        </span>
                    </td>
                    <td class="p-2">
                        <span class="${ip.disabled === 'true' ? 'text-red-600' : 'text-green-600'}">
                            ${ip.disabled === 'true' ? 'DISABLED' : 'ACTIVE'}
                        </span>
                    </td>
                </tr>
            `;
        });
    });
</script>




<script>
/* =======================
   LOAD IP NEIGHBOURS
======================= */
function loadNeighbours() {
    fetch(API_BASE + '/neighbours')
        .then(res => res.json())
        .then(res => {
            const tbody = document.getElementById('neighbour-table');
            tbody.innerHTML = '';

            if (res.data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center p-4 text-gray-500">
                            No neighbours detected
                        </td>
                    </tr>`;
                return;
            }

            res.data.forEach(n => {
                tbody.innerHTML += `
                    <tr class="border-b">
                        <td class="p-2 font-semibold">${n.identity || '-'}</td>
                        <td class="p-2">${n.address || '-'}</td>
                        <td class="p-2">${n.interface || '-'}</td>
                        <td class="p-2">${n.platform || '-'}</td>
                        <td class="p-2">${n.version || '-'}</td>
                        <td class="p-2">${n.board || '-'}</td>
                    </tr>
                `;
            });
        })
        .catch(() => {
            document.getElementById('neighbour-table').innerHTML = `
                <tr>
                    <td colspan="6" class="text-center p-4 text-red-600">
                        Failed to load neighbours
                    </td>
                </tr>`;
        });
}

/* LOAD FIRST TIME */
loadNeighbours();

/* AUTO REFRESH EVERY 10 SECONDS */
setInterval(loadNeighbours, 10000);
</script>



@endsection
