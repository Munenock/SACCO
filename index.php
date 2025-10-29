<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SACCO Management System</title>
<style>
body {
  margin: 0;
  font-family: Arial, sans-serif;
  /* background: #f2f2f2; */
}

.sidebar {
  width: 230px;
  height: 100vh;
  /* background-color: #0033666d; */
  color: white;
  position: fixed;
  top: 0;
  left: 0;
  overflow-y: auto;
  padding-top: 20px;
}

.sidebar h2 {
  text-align: center;
  color: #003366;
  margin-bottom: 20px;
}

.sidebar ul {
  list-style: none;
  padding-left: 0;
}

.sidebar ul li {
  display: grid;
  /* grid-gap: 10px; */
  padding: 4px 15px;
  color: #003366;

}

.sidebar ul li a {
  
  color: #003366;
  padding: 10px;
  border-radius: 4px;
  text-decoration: none;
  background-color: #e6f0ff;
}

/* .sidebar ul li a:hover {
  text-decoration: underline;
} */

.submenu {
  margin-left: 20px;
}

.main-content {
  margin-left: 230px;
  padding: 20px;
  min-height: 100vh;
}

iframe {
  width: 100%;
  height: 90vh;
  border: none;
  background: #fff;
}
</style>
</head>
<body>

<div class="sidebar">
  <h2>MENU</h2>
  <ul>
    <li>Savings
      <ul class="submenu">
        <li><a href="deposit.php" target="contentFrame">Depositing</a></li>
        <li><a href="withdraw.php" target="contentFrame">Withdrawal</a></li>
      </ul>
    </li>
    <li>Loans
      <ul class="submenu">
        <li><a href="loanRegistration.php" target="contentFrame">Loan Registration</a></li>
        <li><a href="loanRepayment.php" target="contentFrame">Loan Repayment</a></li>
        <li><a href="viewLoans.php" target="contentFrame">View Loans</a></li>
        <li><a href="viewRepayments.php" target="contentFrame">Loan Repayments</a></li>
      </ul>
    </li>
    <li>Account
      <ul class="submenu">
        <li><a href="createAccount.php" target="contentFrame">Create Account</a></li>
        <li><a href="editaccount.php" target="contentFrame">Edit Account</a></li>
        <li><a href="deleteMember.php" target="contentFrame">Delete Member</a></li>
        <li><a href="viewMembers.php" target="contentFrame">View All Members</a></li>
      </ul>
    </li>
  </ul>
</div>

<div class="main-content">

<iframe src="home.html"  name="contentFrame" width="100%" height="100%" frameborder="0"></iframe>
</div>

</body>
</html>
