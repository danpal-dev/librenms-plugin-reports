<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        @page { margin: 15mm 12mm; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; border-bottom: 2px solid #00529b; padding-bottom: 10px; margin-bottom: 16px; }
        .header h1 { margin: 0; font-size: 20px; color: #00529b; }
        .meta { margin-bottom: 16px; font-size: 11px; color: #555; }
        .meta p { margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 11px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background-color: #e8f0fe; color: #1a3a6b; font-weight: bold; }
        tr:nth-child(even) { background-color: #f7f9fc; }
        .footer { text-align: center; font-size: 10px; color: #999; border-top: 1px solid #ddd; padding-top: 8px; margin-top: 16px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
    </div>

    @if(!empty($meta))
        <div class="meta">
            @foreach($meta as $k => $v)
                <p><strong>{{ $k }}:</strong> {{ $v }}</p>
            @endforeach
        </div>
    @endif

    <table>
        <thead>
            <tr>
                @foreach($headers as $h)
                    <th>{{ $h }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $row)
                <tr>
                    @foreach($row as $val)
                        <td>{{ $val }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generado por Moni - Reportes &nbsp;|&nbsp; {{ date('d/m/Y H:i:s') }}
    </div>
</body>
</html>
