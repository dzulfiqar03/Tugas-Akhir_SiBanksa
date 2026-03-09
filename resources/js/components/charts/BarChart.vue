<script setup>
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

const chartData = {
  labels: ['18 Oct', '21 Oct', '25 Oct', '28 Oct', '2 Nov', '5 Nov', '9 Nov'],
  datasets: [
    {
      label: 'Setoran',
      backgroundColor: '#064e4b', // Hijau Tua sesuai gambar
      data: [40, 70, 45, 90, 65, 55, 80],
      borderRadius: 6,
    },
    {
      label: 'Penukaran',
      backgroundColor: '#10b981', // Hijau Terang (Emerald)
      data: [-20, -35, -25, -50, -30, -40, -25],
      borderRadius: 6,
    }
  ]
}

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false } // Sembunyikan legend agar mirip gambar
  },
  scales: {
    y: {
      grid: { display: false },
      ticks: { display: false }
    },
    x: {
      grid: { display: false },
      ticks: { font: { size: 10 } }
    }
  }
}
</script>

<template>
  <div class="h-full w-full">
    <Bar :data="chartData" :options="chartOptions" />
  </div>
</template>
