<table align="center" cellpadding="0" cellspacing="0" style="width:100%">
	<tbody>
		<tr>
			<td>
			<table align="center" border="0" cellpadding="5" cellspacing="5" class="deviceWidth" style="background:#f6f6f6; box-shadow:0px 0px 4px 1px #f5f5f5; font-family:Arial,Helvetica,sans-serif; padding:15px; width:650px">
				<tbody>
					<tr>
						<td style="text-align:center"><img alt="" src="https://preprlabs.org/uploads/settings/site_logo.png" /></td>
					</tr>
					<tr>
						<td>Dear {{ $emailData['invitee_name'] }},</td>
					</tr>
					<tr>
						<td style="text-align:center"><img alt="" height="75px" src="{cover_image}" /></td>
					</tr>
					<tr>
						<td>{{  $emailData['body'] }}</td>
					</tr>
					<tr>
                        <br/>
						<td style="text-align:center"><a href="{{ $emailData['slug'] }}" style="color: #fff; background: #44C1E0; text-decoration: none; font-size: 18px; padding: 10px 50px; border-radius: 4px; ">Click here</a></td>
					</tr>
					<tr>
                        <td>Feel free to write us at <a href="mailto:support@prepr.com" style="color:#44C1E0; text-decoration: none;">support@prepr.org</a>&nbsp;for any assistance. We will be happy to help.<br />
						&nbsp;</td>
					</tr>
					<tr>
						<td>Regards,<br />
						Prepr team</td>
					</tr>
				</tbody>
			</table>
			</td>
		</tr>
	</tbody>
</table>
