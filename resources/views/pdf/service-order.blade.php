<!DOCTYPE html>
<html lang="{{ $lang ?? 'es' }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Orden de Servicio</title>
    <style>
        @page { margin: 0; }
        body { margin: 0; padding: 0; }
    </style>
</head>
<body>
    <x-service-order
        :orderNumber="$orderNumber ?? null"
        :createdAt="$createdAt ?? null"
        :serviceDate="$serviceDate ?? null"
        :serviceType="$serviceType ?? null"
        :passengerName="$passengerName ?? null"
        :pickupTime="$pickupTime ?? null"
        :provider="$provider ?? null"
        :flight="$flight ?? null"
        :hotel="$hotel ?? null"
        :adults="$adults ?? null"
        :minors="$minors ?? 0"
        :infants="$infants ?? 0"
        :carSeat="$carSeat ?? 0"
        :booster="$booster ?? 0"
        :luggage="$luggage ?? null"
        :lang="$lang ?? 'es'"
    />
</body>
</html>
