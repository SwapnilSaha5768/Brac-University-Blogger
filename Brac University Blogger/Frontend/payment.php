<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Gateway</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 16px;
            box-sizing: border-box;
        }

        button {
            background-color: #4caf50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<form action="/process_payment" method="post">
    <label for="card_number">Card Number:</label>
    <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" required>

    <label for="expiry_date">Expiry Date:</label>
    <input type="text" id="expiry_date" name="expiry_date" placeholder="MM/YY" required>

    <label for="cvv">CVV:</label>
    <input type="text" id="cvv" name="cvv" placeholder="123" required>

    <button type="submit">Submit Payment</button>
</form>

</body>
</html>
