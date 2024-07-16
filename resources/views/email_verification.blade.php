<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{asset('favicon.png')}}">
    <title>Micro Tech</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            background-color: #fafafa;
            font-family: -apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol';
            line-height: 1.6;
            padding: 20px;
            direction: rtl;
        }
        .container{
            margin: 0 auto;
            overflow: hidden;
            background-color: #fafafa;
            border-radius: 10px;
            padding: 20px;
            color: #262626;
            line-height: 1.8;
            word-break:break-word;
            text-align: center;
        }

        .logo{
            width: 100%;
            border-bottom: 2px solid #E0E2E5;
            padding: 20px 0;
        }

        .message-content{
            padding: 50px 0;
            text-align: right;
            border-bottom: 2px solid #E0E2E5;
            font-size: 16px;
        }
        p{
            font-size: 16px;
            padding-bottom: 8px;
        }
        .verification-code{
            font-weight: bold;
            margin: 20px 0;
        }
        .privecy{
            padding: 20px 0;
            width: 100%;
            color: #b0adc5;
            font-size: 12px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="logo">
        <img src="https://adminpanel.microtechdev.com/favicon.png" alt="logo">
    </div>
    <div class="message-content">
        <p style="direction: rtl">مرحباً {{$user['first_name']}}</p>
        <p style="direction: rtl"> استخدم الكود التالي لتسجيل الدخول في <span style=" font-family: 'SF Pro Text'; ">Consultation</span></p>
        <div class="verification-code">{{$user['otp']}}</div>
        <p style="direction: rtl">لا يمكن الوصول إلى حسابك بدون رمز التحقق هذا، حتى لو لم ترسل هذا الطلب.</p>

        <p style="direction: rtl">الرجاء استخدام هذا الرمز لتسجيل الدخول إلى حسابك.</p>
    </div>
    <p class="privecy">© 2024 Consultation. All rights reserved.</p>
</div>
</body>
</html>
