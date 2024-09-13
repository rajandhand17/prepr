<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>password reset</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-size: 16px;
            margin: 0;
            padding: 0;
            background-color: #F3F7FC;
            font-family: 'Poppins', sans-serif;
            color: #101223;
        }
        .content-container {
            width: 100%;
            max-width: 600px;
            background: #F3F7FC;;
            margin: 40px auto;
            padding: 40px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .header-logo {
            width: 150px;
            height: auto;
            margin: 0 auto 20px;
        }
        .title {
            font-weight: 600;
            font-size: 24px;
            margin-bottom: 30px;
        }
        .image-container {
            width: 100%;
            max-width: 347px;
            height: auto;
            margin: 0 auto 20px;
        }
        .cta-button {
            display: inline-block;
            background-color: #2D9CDB;
            color: white;
            padding: 12px 24px;
            border-radius: 25px;
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 40px;
        }
        .message {
            width: 100%;
            max-width: 550px;
            color: #7A7A7A;
            font-weight: 400;
            font-size: 16px;
            text-align: left;
            margin: 0 auto 50px;
            line-height: 1.5;
        }
        .footer {
            width: 100%;
            text-align: center;
            font-family: 'Poppins', sans-serif;
            font-size: 12px;
            color: #7A7A7A;
            margin-top: 40px;
        }
        .footer .contact {
            color: #4992CE;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="content-container">
        <img class="header-logo" src="https://preprlabs.org/uploads/settings/site_logo.png" alt="Preprlabs Logo">
        <div class="title">Password Reset Successfully</div>
        <img class="image-container" src="{{ config('site-settings.aws_url') }}front/img/resetpassword.png" alt="Image"><br>
        <div class="message">
			Dear {{ ucfirst($data['first_name']); }} {{ ucfirst($data['last_name']); }}
            <br><br>
			You are receiving this email because your PreprLabs password is changed. If you have not requested this reset, please email us at support@prepr.org for assistance.
			<br><br>
            Regards,
            <br>
            Prepr team
        </div>
        <div class="footer">
            ©2023 Preprlabs. All rights reserved.
            <div class="contact">support@prepr.org</div>
            This email message was auto-generated. If you need assistance please contact us.
        </div>
    </div>
</body>
</html>

