<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'PDF Report')</title>
    <style>
        /* Global font and body settings */
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 13px;
            line-height: 1.6;
            color: #333;
            margin: 30px;
        }

        /* Main title */
        h1 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 15px;
            color: #1a202c;
        }

        /* Section headings */
        h2 {
            font-size: 18px;
            margin-top: 30px;
            margin-bottom: 12px;
            color: #2b6cb0;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 5px;
        }

        /* Subheadings */
        h3 {
            font-size: 15px;
            margin-bottom: 8px;
            color: #2d3748;
        }

        /* Paragraphs */
        p {
            margin: 6px 0;
        }

        /* Lists */
        ul {
            list-style: none;
            padding: 0;
            margin: 0 0 12px 0;
        }

        li {
            margin-bottom: 5px;
        }

        /* Horizontal lines */
        hr {
            border: 0;
            border-top: 1px solid #e2e8f0;
            margin: 20px 0;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12.5px;
        }

        th, td {
            border: 1px solid #cbd5e0;
            padding: 8px 10px;
            text-align: left;
        }

        th {
            background-color: #edf2f7;
            font-weight: 600;
            color: #2b6cb0;
        }

        tbody tr:nth-child(even) {
            background-color: #f7fafc;
        }

        tbody tr:hover {
            background-color: #ebf8ff;
        }

        /* Totals row */
        .total-row {
            font-weight: bold;
            background-color: #bee3f8;
        }

        /* Footer info */
        .report-meta {
            font-size: 11px;
            color: #718096;
            margin-bottom: 20px;
        }

        /* Responsive for smaller tables */
        @media print {
            table, th, td {
                font-size: 11px;
            }
        }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>
