<?php
include('dbConnect.php');
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $member_id = $_POST['member_id'];

    if (!empty($member_id)) {// checkin if member id is not empty
        mysqli_begin_transaction($conn);

        try {
            $deleteAccount = "DELETE FROM Account WHERE MemberID = $member_id";
            if (!mysqli_query($conn, $deleteAccount)) {
                throw new Exception("Failed to delete from Account: " . mysqli_error($conn));
            }

            $deleteSavings = "DELETE FROM Savings WHERE MemberID = $member_id";
            if (!mysqli_query($conn, $deleteSavings)) {
                throw new Exception("Failed to delete from Savings: " . mysqli_error($conn));
            }

            $deleteLoanRepayments = "DELETE FROM LoanRepayment WHERE LoanId IN (SELECT LoanId FROM Loan WHERE MemberID = $member_id)";
            if (!mysqli_query($conn, $deleteLoanRepayments)) {
                throw new Exception("Failed to delete from LoanRepayment: " . mysqli_error($conn));
            }

            $deleteLoans = "DELETE FROM Loan WHERE MemberID = $member_id";
            if (!mysqli_query($conn, $deleteLoans)) {
                throw new Exception("Failed to delete from Loan: " . mysqli_error($conn));
            }

            $deleteMember = "DELETE FROM Member WHERE MemberID = $member_id";
            if (!mysqli_query($conn, $deleteMember)) {
                throw new Exception("Failed to delete from Member: " . mysqli_error($conn));
            }

            mysqli_commit($conn);
            $message = "Member and all related records deleted successfully!";
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $message = "eletion failed: " . $e->getMessage();
        }
    } else {
        $message = "Please enter a valid Member ID.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Delete Member & Account - SACCO</title>
<style>
body {
  font-family: Arial, sans-serif;
  background: #f2f2f2;
  margin: 0;
}
.container {
  width: 400px;
  margin: 80px auto;
  background: #fff;
  padding: 30px;
  border-radius: 5px;
  box-shadow: 0 0 8px rgba(0,0,0,0.1);
}
h2 {
  text-align: center;
  color: #660000;
}
form {
  display: flex;
  flex-direction: column;
}
label {
  font-weight: bold;
  color: #333;
  margin-top: 10px;
}
input {
  padding: 8px;
  margin-top: 5px;
  border: 1px solid #ccc;
  border-radius: 4px;
}
input[type=submit] {
  background: #660000;
  color: #fff;
  border: none;
  margin-top: 20px;
  cursor: pointer;
}
input[type=submit]:hover {
  background: #800000;
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
  <h2>Delete Member & Account</h2>

  <form method="POST" action="">
    <label>Enter Member ID:</label>
    <input type="number" name="member_id" required placeholder="e.g. 1">
    <input type="submit" value="Delete Member">
  </form>

  <?php if($message != ""): ?>
  <div class="message"><?php echo $message; ?></div>
  <?php endif; ?>

  <a href="#" onclick="top.location.href='index.php';">Back to Home</a>
</div>

</body>
</html>
