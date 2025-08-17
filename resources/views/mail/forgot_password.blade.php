<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recruitment ITH18</title>
</head>

<body>
    <div>{{ __('auth.mail.register.mail_address') }}: {{ $data['mail_address'] }}</div>
    <a href="{{ config('app.url_home') }}/auth/reset-password?token=<?= $data['token'] ?>&email=<?= $data['mail_address'] ?>">{{ __('auth.mail.forgot_password.text') }}</a>
</body>

</html>
