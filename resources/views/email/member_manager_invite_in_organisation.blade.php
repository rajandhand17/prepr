<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation Email</title>
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

        .image-with-text {
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            width: 100%;
            max-width: 600px;
            height: 300px; /* Adjust height as needed */
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            color: black;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            padding: 20px;
            box-sizing: border-box;
        }
    </style>
</head>

<body>
@php $fallbackImage = 'https://dev.learnlab.ai/static/media/organization.b81447a21b1ee865d621.png'; @endphp
    <div class="content-container">
        <img class="header-logo" src="https://preprlabs.org/uploads/settings/site_logo.png" alt="Preprlabs Logo">
        <div class="title">You have been invited to join an {{$emailData['module_name']}}</div>
        <img class="image-container" src="{{ $emailData['org_image'] }}" alt="Image" 
           onerror="this.style.display='none'; document.getElementById('fallback').style.display='block';"><br>
       <div id="fallback" class="image-with-text" style="display: none; background-image: url('{{ $fallbackImage }}');">
           {{ $emailData['comp_title'] }}
        </div><br>
        <a href="{{ $emailData['slug'] }} " class="cta-button"  style="color:white">Join Now</a>
        <div class="message">
            Dear {{ $emailData['invitee_name'] }},
            <br><br>
            {{$emailData['inviter_name']}} has invited you to join an organization on the Prepr Network as {{$emailData['role']}} : {{$emailData['comp_title']}} Feel free to write to us at support@prepr.org for any assistance. We will be happy to help. 
            <br><br>
            Regards,
            <br>
            Prepr team
        </div>
        <div class="footer">
            ©2024 Preprlabs. All rights reserved.
            <div class="contact">support@prepr.org</div>
            This email message was auto-generated. If you need assistance please contact us.
        </div>
    </div>
</body>
</html>
