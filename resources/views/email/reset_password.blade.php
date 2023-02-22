<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Reset your password</title>
	<style>
		*{ margin:0; padding: 0; }
		body{ background: #fff; margin: 0; padding: 0; font-family: 'Arial'; }

		@media only screen and (max-width: 640px)  {
					body[yahoo] .deviceWidth {width:100%!important; padding:0; }
					body[yahoo] .center {text-align: center!important;}
					body[yahoo] .banners {width:100% !important;}
			}

	@media only screen and (max-width: 479px) {
		body[yahoo] .deviceWidth {width:100%!important; padding:0; }
	}

	</style>
</head>
<body yahoo="fix">
	<table width="100%" bgcolor="#ffffff" cellpadding="0" cellspacing="0" align="center">
		<tr>
			<td width="100%" align="center">
				<table width="650" bgcolor="#F6F6F6" cellpadding="0" cellspacing="0" align="center" class="deviceWidth">
					<tr>
						<td align="center" style="padding: 10px 0;"><img src="/" alt=""></td>
					</tr>
					<tr>
						<td style="font-family:Helvetica, Arial, sans-serif; font-size: 14px; color: #585858; padding: 20px;line-height:150%;">Dear {{ ucfirst($data['first_name']); }} {{ ucfirst($data['last_name']); }},<br>
						  <br>
						  {{__("responses.reset_password_email")}}</td>
                    </tr>
					<tr>
						<td style="border-bottom: 1px #cecece solid;">&nbsp;</td>
					</tr>

					<tr>
	                    <td bgcolor="#dbdada" style="font-family:Helvetica, Arial, sans-serif; font-size:11px; color:#7b7b7b; padding:5px 20px 15px; vertical-align:middle; text-align:center;" width="100%">
	                        You received this email to inform / update you about your
	                        product or account.<br>
	                        <a style="text-decoration:underline; color:#7b7b7b;" target="_blank" href="https://prepr.org/contact/">
	                            Click here
	                        </a>
	                            to view our contact details | Read our
	                        <a style="text-decoration:underline; color:#7b7b7b;" target="_blank" href="https://prepr.org/privacy-policy/">
	                            Privacy Policy
	                        </a>
	                    </td>
                	</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>
