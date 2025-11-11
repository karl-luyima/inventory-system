/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue"
  ],
  theme: {
    extend: {
      colors: {
        'kpi-blue': '#3b82f6',
        'kpi-green': '#22c55e',
        'kpi-yellow': '#facc15',
        'kpi-red': '#ef4444',
        'kpi-purple': '#a855f7',
      }
    }
  },
  safelist: [
    'bg-gradient-to-r from-kpi-blue to-kpi-blue/80',
    'bg-gradient-to-r from-kpi-green to-kpi-green/80',
    'bg-gradient-to-r from-kpi-yellow to-kpi-yellow/80',
    'bg-gradient-to-r from-kpi-red to-kpi-red/80',
    'bg-gradient-to-r from-kpi-purple to-kpi-purple/80',
  ],
  plugins: [],
}
