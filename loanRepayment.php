<?php
include('dbConnect.php');
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $loanId = $_POST['loan_id'];
    $amountPaid = $_POST['amount_paid'];

    if (!empty($loanId) && !empty($amountPaid)) {

        $currentLoanAmount = mysqli_query($conn, "SELECT loanAmount FROM Loan WHERE loanId = '$loanId'");
        $loan = mysqli_fetch_assoc($currentLoanAmount);

        if ($loan) { //if loan exists
            $originalAmount = $loan['loanAmount'];


            $paid_query = mysqli_query($conn, "SELECT SUM(amountPaid) AS totalPaid FROM LoanRepayment WHERE loanId = '$loanId'");
            $paid_row = mysqli_fetch_assoc($paid_query);
            $totalPaid = $paid_row['totalPaid'] ?? 0;

            $remainingBalance = $originalAmount - $totalPaid;

            if ($amountPaid > $remainingBalance) {
                $message = "Amount paid cannot be greater than ugx " . number_format($remainingBalance, 2) . ").";
            } else {
                $newRemaining = $remainingBalance - $amountPaid;

                $sql = "INSERT INTO LoanRepayment (loanId, amountPaid, paymentDate, balanceRemaining)
                        VALUES ('$loanId', '$amountPaid', NOW(), '$newRemaining')";

                if (mysqli_query($conn, $sql)) {
                    $message = "Repayment of UGX  recorded successfully! Remaining Balance: UGX " . number_format($newRemaining, 2) . ".";
                } else {
                    $message = " Database Error: " . mysqli_error($conn);
                }
            }
        } else {
            $message = "Loan ID not found.";
        }
    } else {
        $message = "Please fill in all inputs.";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Loan Repayment - SACCO System</title>
    <style>
        body {
            font-family: Arial;
            background: #f2f2f2;
        }

        .container {
            width: 420px;
            margin: 80px auto;
            background: #fff;
            padding: 30px;
            border-radius: 6px;
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

        input {
            padding: 8px;
            margin-top: 5px;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type=submit] {
            background: #003366;
            color: #fff;
            border: none;
            margin-top: 20px;
            cursor: pointer;
        }

        input[type=submit]:hover {
            background: #004080;
        }

        .message {
            text-align: center;
            margin-top: 15px;
            color: green;
            font-weight: bold;
        }

        .error {
            color: red;
            font-weight: bold;
            text-align: center;
            margin-top: 10px;
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
        <h2>Loan Repayment</h2>
        <form method="POST" ">
            <label>Loan ID:</label>
            <input type="number" name="loan_id" required )">

            <p id="loanAmountDisplay" style="color:#003366; font-weight:bold;"></p>

            <label>Amount Paid (UGX):</label>
            <input type="number" name="amount_paid" required>

            <div id="errorMsg" class="error"></div>

            <input type="submit" value="Record Repayment">
        </form>

        <div class="message"><?php echo $message; ?></div>
        <a href="#" onclick="top.location.href='index.php';">Back to Home</a>
    </div>
</body>

</html>