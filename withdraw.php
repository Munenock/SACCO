<?php
include('dbConnect.php');
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $member_id = $_POST['member_id'];
    $amount = $_POST['amount'];

    if (!empty($member_id) && !empty($amount) && $amount > 0) {
        mysqli_begin_transaction($conn);

        try {
            $account_check = mysqli_query($conn, "SELECT Balance FROM Account WHERE MemberID = '$member_id'");
            if (mysqli_num_rows($account_check) == 0) {
                throw new Exception("Member does not have an account.");
            }

            $account = mysqli_fetch_assoc($account_check);
            $current_balance = $account['Balance'];

            if ($amount > $current_balance) {
                throw new Exception("Withdrawal amount exceeds current balance (UGX " . number_format($current_balance) . ").");
            }

            $sql1 = "INSERT INTO Savings (memberId, amount, transactionType, transactionDate)
                     VALUES ('$member_id', '$amount', 'Withdraw', NOW())";
            if (!mysqli_query($conn, $sql1)) {
                throw new Exception("Error recording withdrawal: " . mysqli_error($conn));
            }

            $sql2 = "UPDATE Account SET Balance = Balance - '$amount' WHERE MemberID = '$member_id'";
            if (!mysqli_query($conn, $sql2)) {
                throw new Exception("Error updating account balance: " . mysqli_error($conn));
            }

            mysqli_commit($conn);
            $message = "Withdrawal of UGX  recorded successfully! Remaining Balance: UGX " . number_format($current_balance - $amount);
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $message = "Transaction failed: " . $e->getMessage();
        }
    } else {
        $message = " Please fill all fields correctly.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdrawal - SACCO</title>
    <style>
        body {
            font-family: Arial;
            background: #f2f2f2;
            margin: 0;
        }

        .container {
            width: 400px;
            margin: 80px auto;
            background: #fff;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #003366;
        }

        label {
            font-weight: bold;
            color: #333;
            margin-top: 10px;
            display: block;
        }

        input[type=number] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type=submit] {
            margin-top: 20px;
            width: 100%;
            padding: 10px;
            background: #003366;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type=submit]:hover {
            background: #004080;
        }

        .message {
            text-align: center;
            margin-top: 15px;
            font-weight: bold;
            color: green;
        }

        a {
            text-decoration: none;
            color: #003366;
            display: block;
            text-align: center;
            margin-top: 15px;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Record Withdrawal</h2>
        <form method="POST">
            <label>Member ID:</label>
            <input type="number" name="member_id" required>
            <label>Amount (UGX):</label>
            <input type="number" name="amount" required>
            <input type="submit" value="Record Withdrawal">
        </form>

        <div class="message"><?php echo $message; ?></div>
        <a href="#" onclick="top.location.href='index.php';">Back to Home</a>

    </div>

</body>

</html>