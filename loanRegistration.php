<?php
include('dbConnect.php');
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $memberId = $_POST['member_id'];
    $loanAmount = $_POST['loan_amount'];
    $interestRate = $_POST['interest_rate'];
    $dueDate = $_POST['due_date'];

    if (!empty($memberId) && !empty($loanAmount) && !empty($interestRate) && !empty($dueDate)) {

     $hasActiveLoan = mysqli_query($conn, "
                SELECT l.loanId, l.loanAmount, 
                       COALESCE(SUM(lr.amountPaid), 0) as totalPaid,
                       (l.loanAmount - COALESCE(SUM(lr.amountPaid), 0)) as remainingBalance
                FROM Loan l 
                LEFT JOIN LoanRepayment lr ON l.loanId = lr.loanId 
                WHERE l.memberId = '$memberId' 
                GROUP BY l.loanId, l.loanAmount
                HAVING remainingBalance > 0
            ");
        if (mysqli_num_rows($hasActiveLoan)>0 ) {
            $message = " Member already has a loan. ";
        } else {
            $totalLoan = $loanAmount + ($loanAmount * ($interestRate / 100));

            $statement = "INSERT INTO Loan (memberId, loanAmount, interestRate, loanDate, dueDate)
                    VALUES ('$memberId', '$totalLoan', '$interestRate', NOW(), '$dueDate')";

            if (mysqli_query($conn, $statement)) {
                $message = "Loan of UGX registered successfully!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Loan Registration - SACCO</title>
    <style>
        body {
            font-family: Arial;
            background: #f2f2f2;
        }

        .container {
            width: 450px;
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
        <h2>Loan Registration</h2>
        <form method="POST">
            <label>Member ID:</label>
            <input type="number" name="member_id" required>

            <label>Loan Amount:</label>
            <input type="number" name="loan_amount" min="20000" step="1000"
                placeholder="Minimum: 20,000" required>

            <label>Interest Rate:</label>
            <input type="number" name="interest_rate" required>

            <label>Due Date:</label>
            <input type="date" name="due_date" required>

            <input type="submit" value="Register Loan">
        </form>

        <div class="message"><?php echo $message; ?></div>
        <a href="#" onclick="top.location.href='index.php';">← Back to Home</a>
    </div>
</body>

</html>