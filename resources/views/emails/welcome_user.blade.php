<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Creation Status</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f7f9fc;
        }

        .email-container {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            background-color: #2c3e50;
            color: #fff;
            text-align: center;
            padding: 15px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }

        .email-header h1 {
            margin: 0;
            font-size: 24px;
        }

        .email-body {
            padding: 20px;
        }

        .email-body p {
            margin: 10px 0;
            line-height: 1.6;
        }

        .email-body ul {
            list-style-type: none;
            padding: 0;
            margin: 10px 0;
        }

        .email-body ul li {
            margin: 8px 0;
        }

        .email-body ul li strong {
            color: #2c3e50;
        }

        .email-footer {
            text-align: center;
            font-size: 12px;
            color: #888;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }

        a {
            color: #000;
            font-weight: bold;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Welcome to Our System</h1>
        </div>
        <div class="email-body">
            <p>Hi, {{ $userName }}</p>
            <p>Your account has been created successfully. Please review the details below:</p>

            <p><strong>Account Details:</strong></p>
            <ul>
                <li><strong>Email:</strong> {{ $email }}</li>
                <li><strong>Password:</strong> {{ $password }}</li>
            </ul>

            <p>To log in, click here: <a href="{{ url('/login') }}">Go to Login</a></p>

            <p>Thank you,<br>HR Department</p>
        </div>
        <div class="email-footer">
            &copy; {{ date('Y') }} HR Department. All Rights Reserved.
        </div>
    </div>
</body>

</html>
