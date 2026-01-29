<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte Contable - {{ $company->razon_social }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .company-info {
            text-align: center;
            margin-bottom: 10px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        .company-details {
            font-size: 12px;
            color: #666;
        }
        .report-title {
            font-size: 16px;
            font-weight: bold;
            margin: 10px 0;
        }
        .report-dates {
            font-size: 14px;
            margin-bottom: 15px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }
        .summary-card {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }
        .summary-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        .summary-value {
            font-size: 16px;
            font-weight: bold;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 2px solid #333;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 12px;
        }
        td {
            font-size: 12px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            <div class="company-name">{{ $company->razon_social }}</div>
            <div class="company-details">RUC: {{ $company->ruc }} | {{ $company->direccion }}</div>
        </div>
        <div class="report-title">REPORTE CONTABLE PARA EL CONTADOR</div>
        <div class="report-dates">Periodo: {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</div>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-label">VENTAS TOTALES</div>
            <div class="summary-value">S/ {{ number_format($totalVentas, 2) }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">COMPRAS TOTALES</div>
            <div class="summary-value">S/ {{ number_format($totalCompras, 2) }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">IGV VENTAS</div>
            <div class="summary-value">S/ {{ number_format($totalIgvVentas, 2) }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">IGV COMPRAS</div>
            <div class="summary-value">S/ {{ number_format($totalIgvCompras, 2) }}</div>
        </div>
    </div>

    <div class="section-title">VENTAS REGISTRADAS</div>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Serie-Número</th>
                <th>Cliente</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ventas as $venta)
            <tr>
                <td>{{ $venta->issue_date->format('d/m/Y') }}</td>
                <td>{{ $venta->sunatType->short_name }}</td>
                <td>{{ $venta->serie }}-{{ $venta->numero }}</td>
                <td>{{ $venta->partner->name ?? 'Cliente General' }}</td>
                <td>S/ {{ number_format($venta->total, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center;">No hay ventas registradas en este periodo</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">COMPRAS REGISTRADAS</div>
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Serie-Número</th>
                <th>Proveedor</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($compras as $compra)
            <tr>
                <td>{{ $compra->issue_date->format('d/m/Y') }}</td>
                <td>{{ $compra->sunatType->short_name }}</td>
                <td>{{ $compra->serie }}-{{ $compra->numero }}</td>
                <td>{{ $compra->partner->name ?? 'Proveedor General' }}</td>
                <td>S/ {{ number_format($compra->total, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center;">No hay compras registradas en este periodo</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
        <p>Sistema LiteConta-SUNAT</p>
    </div>
</body>
</html>