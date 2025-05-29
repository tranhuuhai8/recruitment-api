<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recruitment ITH18</title>
</head>

<body>
    <div>{{ __('auth.mail.register.mail_address') }}: {{ $data['mail_address'] }}</div>
    <a href="{{ config('app.url') }}?token=<?= $data['token_verify'] ?>">{{ __('auth.mail.register.text_verify') }}</a>
</body>

</html>
