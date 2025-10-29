<?php
include('dbConnect.php');

$sql = "
  SELECT 
    r.repaymentId,
    r.loanId,
    CONCAT(m.firstName, ' ', m.lastName) AS memberName,
    l.loanAmount AS originalLoanAmount,
    r.amountPaid,
    r.balanceRemaining,
    r.paymentDate
  FROM LoanRepayment r
  JOIN Loan l ON r.loanId = l.loanId
  JOIN Member m ON l.memberId = m.memberId
  ORDER BY r.repaymentId ASC
";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>

<head>
    <title>View Repayments - SACCO</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
        }

        .container {
            width: 95%;
            margin: 40px auto;
            background: #fff;
            padding: 25px;
            border-radius: 6px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #003366;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #003366;
            color: #fff;
        }

        tr:nth-child(even) {
            background: #f9f9f9;
        }

        a {
            text-decoration: none;
            color: #003366;
            display: block;
            text-align: center;
            margin-top: 20px;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>All Loan Repayments</h2>
        <table>
            <tr>
                <th>Repayment ID</th>
                <th>Loan ID</th>
                <th>Member Name</th>
                <th>Original Loan Amount (UGX)</th>
                <th>Amount Paid (UGX)</th>
                <th>Remaining Balance (UGX)</th>
                <th>Payment Date</th>
            </tr>

            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['repaymentId']; ?></td>
                        <td><?php echo $row['loanId']; ?></td>
                        <td><?php echo $row['memberName']; ?></td>
                        <td><?php echo number_format($row['originalLoanAmount'], 2); ?></td>
                        <td><?php echo number_format($row['amountPaid'], 2); ?></td>
                        <td><?php echo number_format($row['balanceRemaining'], 2); ?></td>
                        <td><?php echo $row['paymentDate']; ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align:center;">No repayments found.</td>
                </tr>
            <?php endif; ?>
        </table>

        <a href="#" onclick="top.location.href='index.php';"> Back to Home</a>
    </div>

</body>

</html>