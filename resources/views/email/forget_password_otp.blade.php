
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset your password</title>
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
		background: #F3F7FC; /* Set the background to white for the box */
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
		color: #101223;
	}
	.image-container {
		width: 100%;
		max-width: 347px;
		height: auto;
		margin: 0 auto 20px;
	}
	.whitebox {
		background: white;
		border-radius: 10px;
		padding: 20px;
		margin: 20px 0;
		font-size: 40px;
		font-weight: 600;
		color: #101223;
	}
	.message {
		width: 100%;
		max-width: 550px;
		color: #7A7A7A;
		font-weight: 400;
		font-size: 16px;
		text-align: center;
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
        <div class="title">Verification Code</div>
        <div class="whitebox">
		<div class="message">Here is your verification code:</div>
            {{$data['otp']}}
        </div>
        <div class="message">
		We’ve received a request to reset your password.  If you have not requested the reset, please ignore this message.
            <br><br>
            Regards,
            <br>
            Prepr team
        </div>
        <div class="footer">
            This email message was auto-generated. If you need assistance please contact  <span class="contact">support@prepr.org</span>.
            <br>©2023 Preprlabs. All rights reserved.
        </div>
    </div>
</body>
</html>
