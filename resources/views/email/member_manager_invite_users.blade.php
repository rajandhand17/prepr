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
            background: #F3F7FC;
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
    <div class="content-container">
        <img class="header-logo" src="https://preprlabs.org/uploads/settings/site_logo.png" alt="Preprlabs Logo">
        <div class="title">You have been invited to join a {{$emailData['module_name']}}</div>
        @php
            $fallbackImage = $emailData['component'] == 'challenge'
                ? 'https://dev.learnlab.ai/static/media/challenge.18b606241bc34ddc5e8b.png'
                : 'https://dev.learnlab.ai/static/media/lab.357ea4694f922888318b.png';
        @endphp
        @if($emailData['comp_mediaType'] == 'image' && $emailData['comp_image'])
        <img class="image-container" src="{{ $emailData['comp_image'] }}" alt="Image"><br>
        @elseif($emailData['comp_mediaType'] == 'embedded' && $emailData['comp_image'] )
            @php
                $dom = new DOMDocument();
                libxml_use_internal_errors(true); // Ignore warnings related to malformed HTML
                $dom->loadHTML($emailData['comp_image']);
                libxml_clear_errors(); // Clear errors after parsing
                $xpath = new DOMXPath($dom);
                $src = $xpath->evaluate('string(//iframe/@src)'); // Extract the src from iframe
            @endphp
            @if(!empty($src))
                <div class="image-with-text" style="background-image: url('{{ $fallbackImage }}');">
                    <a href="{{ $src }}" target="_blank" style="color: black; margin:auto;">{{ $emailData['comp_title'] }}</a>
                </div>
                <p>Click the image above to view the embedded content.</p>
            @else
                <p>Invalid embedded code.</p>
            @endif
        @else
        <div class="image-with-text" style="background-image: url('{{ $fallbackImage }}');">
            {{ $emailData['comp_title'] }}
        </div><br>
        @endif
        <a href="{{ $emailData['slug'] }}" class="cta-button"  style="color:white" >Join {{$emailData['module_name']}}</a>
        <div class="message">
            Dear {{ $emailData['invitee_name'] }},
            <br><br>
            {{ $emailData['inviter_name'] }} invited you to join a {{$emailData['module_name']}} on the Prepr Network: {{ $emailData['comp_title'] }}.
            <br><br>
            {{ $emailData['body'] }}
            <br><br>
            If you are already registered, then please follow the login process. Feel free to write to us at support@prepr.org for any assistance. We will be happy to help.
            <br><br>
            Regards,
            <br>
            Prepr team
        </div>
        <div class="footer">
            This email message was auto-generated. If you need assistance please contact <div class="contact">support@prepr.org</div>.
            ©2024 Preprlabs. All rights reserved.
        </div>
    </div>
</body>
</html>
