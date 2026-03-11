<!DOCTYPE html>
<html>

<head>
    <title>Correo de Notificación</title>
</head>

<body>
    <h1>Hola, {{ $detalles['nombre'] }}</h1>
    <p>{{ $detalles['mensaje'] }}</p>
    <p>Puedes acceder a este enlace: <a href="{{ $detalles['url'] }}">{{ $detalles['url'] }}</a></p>
</body>

</html>
