<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Service créé</title>
</head>
<body style="margin:0; padding:0; font-family: Arial, sans-serif; background:#f4f6f9;">

<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td align="center">

<table width="600" style="background:#ffffff; margin-top:40px; border-radius:10px; overflow:hidden;">

    <!-- Header -->
    <tr>
        <td style="background:#28a745; color:#fff; padding:20px; text-align:center;">
            <h1>Service ajouté 🎉</h1>
        </td>
    </tr>

    <!-- Content -->
    <tr>
        <td style="padding:30px; text-align:center;">

            <h2>{{ $service->title }}</h2>

            <p style="color:#555;">
                Votre service a été créé avec succès ✔️<br>
                Il est maintenant visible pour les clients.
            </p>

            <div style="background:#f8f9fa; padding:15px; border-radius:8px; margin:20px 0;">
                <p><strong>Catégorie :</strong> {{ $service->category->name ?? 'N/A' }}</p>
                <p><strong>Ville :</strong> {{ $service->city }}</p>
            </div>

            <a href="{{ url('/services/'.$service->id) }}"
               style="display:inline-block; padding:12px 25px; background:#28a745; color:#fff; text-decoration:none; border-radius:5px;">
               Voir le service
            </a>

        </td>
    </tr>

    <!-- Footer -->
    <tr>
        <td style="background:#f1f1f1; padding:15px; text-align:center; font-size:12px; color:#888;">
            © {{ date('Y') }} {{ config('app.name') }}
        </td>
    </tr>

</table>

</td>
</tr>
</table>

</body>
</html>
