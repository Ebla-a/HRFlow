<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome</title>
</head>
<body>
    <h1>Welcome to our company!</h1>
    
    <p>Your account has been successfully created with the email: <strong>{{ $email }}</strong></p>

    @if($tempPassword)
        <p>Your temporary password is: <strong>{{ $tempPassword }}</strong></p>
        <p>Please log in and change your password as soon as possible.</p>
    @endif

    <p>Thank you for joining us!</p>
</body>
</html>