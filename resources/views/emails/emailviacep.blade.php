<!DOCTYPE html>
<html>
<head>
    <title>{{ $emailData['title'] }}</title>
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
            background-color: #4a6fdc;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            text-align: center;
            color: #666;
        }
        .cep-list {
            background-color: #fff;
            border: 1px solid #eee;
            padding: 10px;
            margin: 10px 0;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $emailData['title'] }}</h2>
    </div>
    <div class="content">
        <p>Olá,</p>

        <div class="cep-list">
            {!! nl2br(e($emailData['message'])) !!}
        </div>

        <p>Obrigado por usar nosso sistema!</p>
    </div>
    <div class="footer">
        <p>Este é um e-mail automático. Por favor, não responda a esta mensagem.</p>
        <p>© {{ date('Y') }} Sistema ViaCEP</p>
    </div>
</body>
</html>
