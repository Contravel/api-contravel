<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Push Email</title>
    <style>
        table[name="blk_permission"],
        table[name="blk_footer"] {
            display: none;
        }
        body {
            background-color: #a6e9d7;
        }
    </style>
</head>
<body style="margin:0; padding:0; width:100%; background-color:#a6e9d7;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#a6e9d7">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0" bgcolor="#ffffff" style="max-width:600px;">
                    <tr>
                        <td align="center" bgcolor="#1fc899" style="padding:20px;">
                            {{-- Logo --}}
                            <img src="{{ $message->embed(public_path('mail/images/logo-contravel.png')) }}" width="160" alt="Contravel Logo">
                        </td>
                    </tr>
                    <tr>
                        <td align="center" bgcolor="#1fc899" style="padding:20px; color:#fff; font-family:Cambria, serif; font-size:24px;">
                            <em><strong>Hola, tienes un mensaje de: {{ $agencia }}</strong></em>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" bgcolor="#1fc899" style="padding:20px; color:#fff; font-family:Cambria, serif; font-size:21px;">
                            <em><strong>
                                Quiere saber más de nuestro {{ $tipo }}: {{ $titulo }}
                            </strong></em>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px; font-family:Tahoma, Arial, sans-serif; font-size:14px; color:#4f4f4f;">
                            <p><b>Mensaje:</b></p>
                            <p>{{ $mensaje }}</p>
                            <p><b>Contactar a:</b> {{ $nombre }}</p>
                            <p><b>Correo:</b> {{ $email }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:20px;">
                            {{-- Imagen de agente --}}
                            <img src="{{ $message->embed(public_path('mail/images/agente.png')) }}" width="160" alt="Agente Contravel">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>