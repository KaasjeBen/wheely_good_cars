<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #1f2937;
            margin: 0;
            padding: 24px;
        }

        h1 {
            font-size: 22px;
            margin: 0 0 6px;
        }

        .meta {
            color: #6b7280;
            margin-bottom: 18px;
        }

        .box {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 14px;
            margin-bottom: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 6px 0;
            vertical-align: top;
        }

        .label {
            width: 35%;
            color: #6b7280;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            margin: 0 6px 6px 0;
            border-radius: 999px;
            background: #f3f4f6;
        }
    </style>
</head>

<body>
    <h1>{{ $car->display_title }}</h1>
    <div class="meta">Kenteken: {{ $car->license_plate ?? 'Onbekend' }} | Aanbieder: {{ $car->user?->name ?? 'Onbekend' }}</div>

    <div class="box">
        <table>
            <tr>
                <td class="label">Merk</td>
                <td>{{ $car->make }}</td>
            </tr>
            <tr>
                <td class="label">Model</td>
                <td>{{ $car->model }}</td>
            </tr>
            <tr>
                <td class="label">Bouwjaar</td>
                <td>{{ $car->year ?? 'Onbekend' }}</td>
            </tr>
            <tr>
                <td class="label">Kilometerstand</td>
                <td>{{ number_format($car->mileage, 0, ',', '.') }} km</td>
            </tr>
            <tr>
                <td class="label">Vraagprijs</td>
                <td>€ {{ number_format($car->price, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Status</td>
                <td>{{ $car->status === 'sold' ? 'Verkocht' : 'Beschikbaar' }}</td>
            </tr>
            <tr>
                <td class="label">Views</td>
                <td>{{ $car->views }}</td>
            </tr>
        </table>
    </div>

    @if ($car->description)
    <div class="box">
        <strong>Omschrijving</strong>
        <div style="margin-top: 6px; line-height: 1.5;">{{ $car->description }}</div>
    </div>
    @endif

    <div class="box">
        <strong>Tags</strong>
        <div style="margin-top: 8px;">
            @forelse ($car->tags as $tag)
            <span class="badge">{{ $tag->name }}</span>
            @empty
            <span>Geen tags</span>
            @endforelse
        </div>
    </div>
</body>

</html>