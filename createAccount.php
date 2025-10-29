<?php
include('dbConnect.php');
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $firstName = trim($_POST['firstName']);
  $lastName = trim($_POST['lastName']);
  $gender = $_POST['gender'];
  $contact = trim($_POST['contact']);
  $address = trim($_POST['address']);
  $accountNumber = trim($_POST['accountNumber']);
  $balance = $_POST['balance'];

  if (!empty($firstName) && !empty($lastName) && !empty($accountNumber) && !empty($contact)) {

    mysqli_begin_transaction($conn); // we are starting connection here

    try {
      $memberData = "INSERT INTO Member (firstName, lastName, gender, contact, address, dateJoined)
                           VALUES ('$firstName', '$lastName', '$gender', '$contact', '$address', CURRENT_DATE())"; // info about the member 

      if (!mysqli_query($conn, $memberData)) {
        throw new Exception("Error inserting Member: " . mysqli_error($conn));
      }

      $memberId = mysqli_insert_id($conn); // this increments the id basing on the previous id

      // Insert into Account table
      $accountInfo = "INSERT INTO Account (MemberID, AccountNumber, OpenDate, Balance)
                            VALUES ('$memberId', '$accountNumber', CURRENT_DATE(), '$balance')";

      if (!mysqli_query($conn, $accountInfo)) {
        throw new Exception("Error inserting Account: " . mysqli_error($conn));
      }


      mysqli_commit($conn); //if no errors met we commit all changes to the database

      $message = "Member and Account created successfully (Member ID: $memberId)";
    } catch (Exception $e) { //catching any errors
      mysqli_rollback($conn);
      $message = "Failed: " . $e->getMessage();
    }
  } else {
    $message = "Please fill all required fields.";
  }
}
function generateUniqueAccountNumber($conn)
{
  do {
    $randomNumber = rand(2000, 9000);
    $accountNumber = "ACN000" . $randomNumber."X";

    $check_sql = "SELECT AccountNumber FROM Account WHERE AccountNumber = '$accountNumber'";
    $result = $conn->query($check_sql);
  } while ($result->num_rows > 0);

  return $accountNumber;
}

$accountNumber = generateUniqueAccountNumber($conn);
?>

<!DOCTYPE html>
<html>

<head>
  <title>Create Member & Account - SACCO</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f2f2f2;
      margin: 0;
      padding: 0;
    }

    .container {
      width: 500px;
      margin: 50px auto;
      background: #fff;
      padding: 25px;
      border-radius: 5px;
      box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      color: #003366;
      margin-bottom: 20px;
    }

    form {
      display: flex;
      flex-direction: column;
    }

    label {
      margin-top: 10px;
      font-weight: bold;
      color: #333;
    }

    input,
    select {
      padding: 8px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    input[type=submit] {
      margin-top: 20px;
      padding: 10px;
      background: #003366;
      color: #fff;
      border: none;
      cursor: pointer;
      border-radius: 4px;
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
    <h2>Create Member and Account</h2>
    <form method="POST" action="">
      <label>First Name:</label>
      <input type="text" name="firstName" required>

      <label>Last Name:</label>
      <input type="text" name="lastName" required>

      <label>Gender:</label>
      <select name="gender" required>
        <option value="">-- Select</option>
        <option value="M">M</option>
        <option value="F">F</option>
      </select>

     <label>Contact:</label>
<input type="tel" name="contact" pattern="(07[0-9]{8})" maxlength="10" 
       placeholder="07XXXXXXXX" required 
       title="Please enter a valid Ugandan phone number starting with 07 followed by 8 digits">

      <label>Address:</label>
      <input type="text" name="address">

      <hr style="margin:20px 0;">

      <label>Account Number:</label>
      <input type="text" name="accountNumber" value="<?php echo $accountNumber; ?>" required readonly>
      <label>Opening Balance:</label>
      <input type="number" name="balance" min="20000" placeholder="Minimum : 20,000" step="0.1" required>

      <input type="submit" value="Create Account">
    </form>

    <?php if ($message != ""): ?>
      <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <a href="#" onclick="top.location.href='index.php';"> Back to Home</a>
  </div>

</body>

</html>