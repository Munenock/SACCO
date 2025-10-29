<?php
include('dbConnect.php');

$sql = "
  SELECT 
    l.loanId,
    l.memberId,
    CONCAT(m.firstName, ' ', m.lastName) AS memberName,
    l.loanAmount,
    l.interestRate,
    l.loanDate,
    l.dueDate,
    COALESCE(r.amountPaid, 0) AS lastPayment,
    COALESCE(r.balanceRemaining, l.loanAmount) AS remainingBalance
  FROM Loan l
  JOIN Member m ON l.memberId = m.memberId
  LEFT JOIN (
    SELECT loanId, amountPaid, balanceRemaining
    FROM LoanRepayment
    WHERE repaymentId IN (
      SELECT MAX(repaymentId) 
      FROM LoanRepayment 
      GROUP BY loanId
    )
  ) r ON l.loanId = r.loanId
  ORDER BY l.loanId ASC
";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>

<head>
    <title>View Loans - SACCO</title>
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
        <h2>All Loans</h2>
        <table>
            <tr>
                <th>Loan ID</th>
                <th>Member Name</th>
                <th>Loan Amount (UGX)</th>
                <th>Interest Rate (%)</th>
                <th>Loan Date</th>
                <th>Due Date</th>
                <th>Last Payment (UGX)</th>
                <th>Remaining Balance (UGX)</th>
            </tr>

            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['loanId']; ?></td>
                        <td><?php echo $row['memberName']; ?></td>
                        <td><?php echo number_format($row['loanAmount'], 2); ?></td>
                        <td><?php echo number_format($row['interestRate'], 2); ?></td>
                        <td><?php echo $row['loanDate']; ?></td>
                        <td><?php echo $row['dueDate']; ?></td>
                        <td><?php echo number_format($row['lastPayment'], 2); ?></td>
                        <td><?php echo number_format($row['remainingBalance'], 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align:center;">No loans to see.</td>
                </tr>
            <?php endif; ?>
        </table>

        <a href="#" onclick="top.location.href='index.php';">Back to Home</a>
    </div>

</body>

</html>