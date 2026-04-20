<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Bienvenue</title>
</head>

<body style="margin:0; padding:0; font-family: Arial, sans-serif; background-color:#f4f4f4;">

    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">

                <table width="600" style="background:#ffffff; margin-top:40px; border-radius:10px; overflow:hidden;">

                    <!-- Header -->
                    <tr>
                        <td style="background:#4CAF50; color:#fff; padding:20px; text-align:center;">
                            <h1>Bienvenue 👋</h1>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding:30px; text-align:center;">
                            <h2>Bonjour {{ $user->name }} 🎉</h2>
                            <p style="color:#555;">
                                Votre compte a été créé avec succès.<br>
                                Nous sommes ravis de vous accueillir !
                            </p>

                            <a href="{{ url('/') }}"
                                style="display:inline-block; margin-top:20px; padding:12px 25px; background:#4CAF50; color:#fff; text-decoration:none; border-radius:5px;">
                                Accéder au site
                            </a>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background:#f1f1f1; padding:15px; text-align:center; font-size:12px; color:#888;">
                            © {{ date('Y') }} {{ config('app.name') }} - Tous droits réservés
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>
