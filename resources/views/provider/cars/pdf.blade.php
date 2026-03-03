<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #111;
        }

        h1 {
            font-size: 20px;
            margin-bottom: 4px;
        }

        .meta {
            color: #555;
            margin-bottom: 12px;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            background: #f5a623;
            color: #fff;
            border-radius: 12px;
            margin-right: 6px;
            margin-bottom: 4px;
        }

        .box {
            border: 1px solid #ddd;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 4px 0;
        }

        .muted {
            color: #555;
        }
    </style>
</head>

<body>
    <h1>{{ $car->display_title }}</h1>
    <div class="meta">Kenteken: {{ $car->license_plate ?? 'nvt' }} | Aanbieder: {{ $car->user?->name }}</div>

    <div class="box">
        <table>
            <tr>
                <td class="muted">Merk</td>
                <td>{{ $car->make }}</td>
            </tr>
            <tr>
                <td class="muted">Model</td>
                <td>{{ $car->model }}</td>
            </tr>
            <tr>
                <td class="muted">Bouwjaar</td>
                <td>{{ $car->year ?? 'Onbekend' }}</td>
            </tr>
            <tr>
                <td class="muted">Kilometerstand</td>
                <td>{{ number_format($car->mileage, 0, ',', '.') }} km</td>
            </tr>
            <tr>
                <td class="muted">Vraagprijs</td>
                <td>€ {{ number_format($car->price, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="muted">Status</td>
                <td>{{ $car->status === 'sold' ? 'Verkocht' : 'Beschikbaar' }}</td>
            </tr>
            <tr>
                <td class="muted">Views</td>
                <td>{{ $car->views }}</td>
            </tr>
        </table>
    </div>

    @if ($car->description)
    <div class="box">
        <strong>Omschrijving</strong>
        <div>{{ $car->description }}</div>
    </div>
    @endif

    <div class="box">
        <strong>Tags</strong><br>
        @forelse ($car->tags as $tag)
        <span class="badge">{{ $tag->name }}</span>
        @empty
        <span class="muted">Geen tags</span>
        @endforelse
    </div>
</body>

</html>