<?php
include('dbConnect.php');

$sql = "
  SELECT 
    m.memberId, 
    m.firstName, 
    m.lastName, 
    m.gender, 
    m.Contact, 
    m.address, 
    m.dateJoined,
    a.AccountNumber,
    a.Balance,
    COALESCE(SUM(
      CASE 
        WHEN lr.minBalance IS NOT NULL THEN lr.minBalance
        ELSE l.loanAmount
      END
    ), 0) AS totalLoanBalance
  FROM Member m
  LEFT JOIN Account a 
    ON m.memberId = a.MemberID
  LEFT JOIN Loan l
    ON m.memberId = l.memberId
  LEFT JOIN (
    SELECT loanId, MIN(balanceRemaining) AS minBalance
    FROM LoanRepayment
    GROUP BY loanId
  ) lr ON l.loanId = lr.loanId
  GROUP BY 
    m.memberId, 
    m.firstName, 
    m.lastName, 
    m.gender, 
    m.Contact, 
    m.address, 
    m.dateJoined, 
    a.AccountNumber, 
    a.Balance
  ORDER BY m.memberId ASC
";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>All Members - SACCO</title>
<style>
body {
  font-family: Arial, sans-serif;
  background: #f2f2f2;
  margin: 0;
}
.container {
  width: 90%;
  margin: 40px auto;
  background: #fff;
  padding: 25px;
  border-radius: 6px;
  box-shadow: 0 0 8px rgba(0,0,0,0.1);
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
th, td {
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
  <h2>All SACCO Members and Accounts</h2>

  <table>
    <tr>
      <th>Member ID</th>
      <th>Full Name</th>
      <th>Gender</th>
      <th>Contact</th>
      <th>Address</th>
      <th>Date Joined</th>
      <th>Account Number</th>
      <th>Balance (UGX)</th>
      <th>Total Remaining Loan Balance (UGX)</th>
    </tr>

    <?php if(mysqli_num_rows($result) > 0): ?>
      <?php while($row = mysqli_fetch_assoc($result)): ?>
        <tr>
          <td><?php echo $row['memberId']; ?></td>
          <td><?php echo $row['firstName'] . ' ' . $row['lastName']; ?></td>
          <td><?php echo $row['gender']; ?></td>
          <td><?php echo $row['Contact']; ?></td>
          <td><?php echo $row['address']; ?></td>
          <td><?php echo $row['dateJoined']; ?></td>
          <td><?php echo $row['AccountNumber'] ? $row['AccountNumber'] : '—'; ?></td>
          <td><?php echo $row['Balance'] !== null ? number_format($row['Balance'], 2) : '—'; ?></td>
          <td><?php echo $row['totalLoanBalance'] > 0 ? number_format($row['totalLoanBalance'], 2) : '—'; ?></td>
        </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr><td colspan="9" style="text-align:center;">No members found.</td></tr>
    <?php endif; ?>
  </table>

<a href="#" onclick="top.location.href='index.php';">Back to Home</a>
</div>

</body>
</html>
