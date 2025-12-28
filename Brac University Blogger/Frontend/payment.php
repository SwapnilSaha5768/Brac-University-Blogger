<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Gateway</title>
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
<div class="auth-body">
    <div class="auth-container">
        <div class="auth-header">
            <h1>Payment Gateway</h1>
        </div>
        <form action="/process_payment" method="post">
            <div class="form-group">
                <label for="card_number" class="form-label">Card Number:</label>
                <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="expiry_date" class="form-label">Expiry Date:</label>
                <input type="text" id="expiry_date" name="expiry_date" placeholder="MM/YY" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="cvv" class="form-label">CVV:</label>
                <input type="text" id="cvv" name="cvv" placeholder="123" class="form-control" required>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn-primary">Submit Payment</button>
            </div>
            <div class="text-center mt-4">
                <a href="index.php" style="color:var(--primary-color);">Back to Home</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
