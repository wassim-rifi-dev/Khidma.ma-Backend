<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Nouvelle demande de service</title>
</head>
<body style="margin:0; padding:0; font-family: Arial, sans-serif; background:#f4f6f9;">
<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center">
            <table width="600" style="background:#ffffff; margin-top:40px; border-radius:10px; overflow:hidden;">
                <tr>
                    <td style="background:#0d6efd; color:#ffffff; padding:20px; text-align:center;">
                        <h1>Nouvelle demande recue</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding:30px;">
                        <h2 style="margin-top:0;">Bonjour {{ $requestOrder->service?->professional?->user?->name ?? 'Professionnel' }},</h2>

                        <p style="color:#555; line-height:1.6;">
                            Un client vient d'envoyer une nouvelle demande pour votre service
                            <strong>{{ $requestOrder->service?->title ?? 'N/A' }}</strong>.
                        </p>

                        <div style="background:#f8f9fa; padding:18px; border-radius:8px; margin:20px 0; color:#333;">
                            <p><strong>Client :</strong> {{ $requestOrder->client?->name ?? 'N/A' }}</p>
                            <p><strong>Email :</strong> {{ $requestOrder->client?->email ?? 'N/A' }}</p>
                            <p><strong>Adresse :</strong> {{ $requestOrder->address ?? 'N/A' }}</p>
                            <p><strong>Date souhaitee :</strong> {{ optional($requestOrder->preferred_date)->format('Y-m-d') ?? 'N/A' }}</p>
                            <p><strong>Heure souhaitee :</strong> {{ $requestOrder->preferred_time ?? 'N/A' }}</p>
                            <p><strong>Budget propose :</strong> {{ $requestOrder->price !== null ? number_format((float) $requestOrder->price, 2, '.', '') . ' MAD' : 'N/A' }}</p>
                            <p><strong>Message :</strong> {{ $requestOrder->message ?? 'N/A' }}</p>
                        </div>

                        <p style="color:#555; line-height:1.6; margin-bottom:0;">
                            Connectez-vous a votre espace professionnel pour consulter et traiter cette demande.
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="background:#f1f1f1; padding:15px; text-align:center; font-size:12px; color:#888;">
                        &copy; {{ date('Y') }} {{ config('app.name') }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
