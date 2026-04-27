<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $mailSubject }}</title>
</head>
<body style="margin:0; padding:0; font-family: Arial, sans-serif; background:#f4f6f9;">
<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center">
            <table width="600" style="background:#ffffff; margin-top:40px; border-radius:10px; overflow:hidden;">
                <tr>
                    <td style="background:#0f766e; color:#ffffff; padding:20px; text-align:center;">
                        <h1 style="margin:0;">{{ $heading }}</h1>
                    </td>
                </tr>
                <tr>
                    <td style="padding:30px; color:#334155;">
                        <h2 style="margin-top:0;">{{ $greeting }}</h2>
                        <p style="line-height:1.6; color:#475569;">{{ $messageLine }}</p>

                        @if (!empty($details))
                            <div style="background:#f8fafc; border:1px solid #e2e8f0; padding:18px; border-radius:8px; margin:20px 0;">
                                @foreach ($details as $label => $value)
                                    <p style="margin:0 0 10px 0;">
                                        <strong>{{ $label }}:</strong> {{ $value }}
                                    </p>
                                @endforeach
                            </div>
                        @endif

                        @if ($actionLabel && $actionUrl)
                            <a href="{{ $actionUrl }}"
                               style="display:inline-block; padding:12px 24px; background:#0f766e; color:#ffffff; text-decoration:none; border-radius:6px;">
                                {{ $actionLabel }}
                            </a>
                        @endif

                        @if ($footerLine)
                            <p style="margin-top:24px; line-height:1.6; color:#64748b;">{{ $footerLine }}</p>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="background:#f1f5f9; padding:15px; text-align:center; font-size:12px; color:#64748b;">
                        &copy; {{ date('Y') }} {{ config('app.name') }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
