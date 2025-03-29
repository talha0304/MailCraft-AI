<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Email Verification</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <!-- Outer Container -->
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4;">
        <tr>
            <td align="center">
                <!-- Inner Container -->
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; margin: 20px auto; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                    <!-- Header Section -->
                    <tr>
                        <td align="center" style="padding: 20px; background-color: #007BFF; border-radius: 8px 8px 0 0;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px;">Email Verification</h1>
                        </td>
                    </tr>
                    <!-- Body Section -->
                    <tr>
                        <td style="padding: 20px;">
                            <p style="font-size: 16px; color: #333333; line-height: 1.6;">Hello,</p>
                            <p style="font-size: 16px; color: #333333; line-height: 1.6;">Your One-Time Password (OTP) for verification is:</p>
                            <div style="text-align: center; margin: 20px 0;">
                                <span style="display: inline-block; padding: 10px 20px; font-size: 24px; font-weight: bold; color: #007BFF; background-color: #e9f5ff; border-radius: 4px;">
                                    {{ $otp }}
                                </span>
                            </div>
                            <p style="font-size: 16px; color: #333333; line-height: 1.6;">Please use this OTP to complete your verification process.</p>
                            <p style="font-size: 16px; color: #333333; line-height: 1.6;">If you did not request this OTP, please ignore this email.</p>
                        </td>
                    </tr>
                    <!-- Footer Section -->
                    <tr>
                        <td align="center" style="padding: 20px; background-color: #f4f4f4; border-radius: 0 0 8px 8px;">
                            <p style="font-size: 14px; color: #777777; margin: 0;">&copy; {{ date('Y') }} Your Company. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>