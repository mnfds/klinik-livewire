import 'alpinejs';

import './bootstrap';

import Chart from 'chart.js/auto';
window.Chart = Chart;

// resources/js/app.js
import './../../vendor/power-components/livewire-powergrid/dist/powergrid'
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";

function initThemeToggle() {
    const savedTheme = localStorage.getItem('theme') || 'emerald';
    document.documentElement.setAttribute('data-theme', savedTheme);

    const themeToggles = document.querySelectorAll('input.theme-controller');

    themeToggles.forEach(toggle => {
        // Hindari binding ulang
        if (toggle.dataset.themeBound === 'true') return;

        // Set posisi awal toggle
        toggle.checked = savedTheme === 'night';
        toggle.dataset.themeBound = 'true';

        toggle.addEventListener('change', function () {
            const newTheme = this.checked ? 'night' : 'emerald';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);

            // Sinkronkan semua toggle
            themeToggles.forEach(t => {
                if (t !== this) t.checked = this.checked;
            });
        });
    });
}

// Inisialisasi saat halaman selesai dimuat
document.addEventListener('DOMContentLoaded', initThemeToggle);

// Inisialisasi ulang setelah Livewire navigasi
document.addEventListener('livewire:navigated', initThemeToggle);

