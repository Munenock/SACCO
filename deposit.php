<?php
include('dbConnect.php');
$message = "  ";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $member_id = $_POST['member_id'];
    $amount = $_POST['amount'];

    if (!empty($member_id) && !empty($amount) && $amount > 0) {
        // Start transaction
        mysqli_begin_transaction($conn);

        try {
            // Here we are checking if member has an account
            $account_check = mysqli_query($conn, "SELECT * FROM Account WHERE MemberID = '$member_id'");
            if (mysqli_num_rows($account_check) == 0) {
                throw new Exception("Member does not have an account. ");
            }

            // Recording deposit
            $sql1 = "INSERT INTO Savings (memberId, amount, transactionType, transactionDate)
                     VALUES ('$member_id', '$amount', 'Deposit', NOW())";
            if (!mysqli_query($conn, $sql1)) {
                throw new Exception("Error recording deposit: " . mysqli_error($conn));
            }

            
            $sql2 = "UPDATE Account SET Balance = Balance + '$amount' WHERE MemberID = '$member_id'";
            if (!mysqli_query($conn, $sql2)) {
                throw new Exception("Error updating account balance: " . mysqli_error($conn));
            }

            mysqli_commit($conn);
            $message = "Deposit of UGX " . number_format($amount) . " recorded successfully! Account balance updated.";
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $message = "Transaction failed: " . $e->getMessage();
        }
    } else {
        $message = "Please enter all required fields with a valid amount.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposit - SACCO Management System</title>
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
            margin-top: 10px;
            font-weight: bold;
            color: #333;
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

        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
            color: #003366;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Record Deposit</h2>

        <form method="POST" action="">
            <label for="member_id">Member ID:</label>
            <input type="number" name="member_id" id="member_id" placeholder="Enter Member ID" required>

            <label for="amount">Amount (UGX):</label>
            <input type="number" name="amount" id="amount" placeholder="Enter deposit amount" required>

            <input type="submit" value="Record Deposit">
        </form>

        <?php if ($message != ""): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <a href="#" onclick="top.location.href='index.php';"> Back to Home</a>
    </div>

</body>

</html>