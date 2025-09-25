import './bootstrap';  
import Chart from "chart.js/auto";

// Sales Chart
const salesCtx = document.getElementById("salesChart");
if (salesCtx) {
    new Chart(salesCtx, {
        type: "line",
        data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "May"],
            datasets: [{
                label: "Sales ($)",
                data: [5000, 7000, 8000, 6500, 9000],
                borderColor: "rgb(37, 99, 235)",
                backgroundColor: "rgba(37, 99, 235, 0.2)",
                tension: 0.4,
                fill: true
            }]
        }
    });
}

// Products Chart
const productsCtx = document.getElementById("productsChart");
if (productsCtx) {
    new Chart(productsCtx, {
        type: "bar",
        data: {
            labels: ["Product A", "Product B", "Product C", "Product D"],
            datasets: [{
                label: "Units Sold",
                data: [120, 90, 140, 70],
                backgroundColor: ["#3B82F6", "#10B981", "#F59E0B", "#EF4444"]
            }]
        }
    });
}
