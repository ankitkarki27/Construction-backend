<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Contact Form Submission</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            border-bottom: 3px solid #0056b3;
        }
        .content {
            background-color: #ffffff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }
        .field-label {
            font-weight: bold;
            color: #0056b3;
        }
        .message-box {
            background-color: #f9f9f9;
            padding: 15px;
            border-left: 3px solid #0056b3;
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>New Contact Form Submission</h2>
    </div>
    
    <div class="content">
        <p><span class="field-label">Name:</span> {{ $mailData['name'] }}</p>
        <p><span class="field-label">Email:</span> <a href="mailto:{{ $mailData['email'] }}">{{ $mailData['email'] }}</a></p>
        <p><span class="field-label">Phone:</span> {{ $mailData['phone'] }}</p>
        <p><span class="field-label">Subject:</span> {{ $mailData['subject'] }}</p>
        
        <p class="field-label">Message:</p>
        <div class="message-box">
            {{ $mailData['message'] }}
        </div>
    </div>
    
    <div class="footer">
        <p>This is an automated notification from your website contact form.</p>
        <p>© {{ date('Y') }} Rainbow Construction</p>
    </div>
</body>
</html>