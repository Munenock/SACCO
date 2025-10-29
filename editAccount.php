<?php
include('dbConnect.php');
$message = "";
$member = null;
$account = null;

if (isset($_POST['search'])) {
    $memberId = $_POST['member_id'];

    $member_query = mysqli_query($conn, "SELECT * FROM Member WHERE memberId = '$memberId'");
    if ($member_query && mysqli_num_rows($member_query) > 0) {
        $member = mysqli_fetch_assoc($member_query);

        $account_query = mysqli_query($conn, "SELECT * FROM Account WHERE MemberID = '$memberId'");
        if ($account_query && mysqli_num_rows($account_query) > 0) {
            $account = mysqli_fetch_assoc($account_query);
        }
    } else {
        $message = "No one found .";
    }
}

if (isset($_POST['update'])) {
    $memberId = $_POST['member_id'];
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $gender = $_POST['gender'];
    $contact = trim($_POST['contact']);
    $address = trim($_POST['address']);
    $accountNumber = trim($_POST['accountNumber']);
    $balance = $_POST['balance'];

    if (!empty($memberId)) {
        mysqli_begin_transaction($conn);
        try {
            $sql_member = "UPDATE Member 
                           SET firstName='$firstName', lastName='$lastName', gender='$gender',
                               Contact='$contact', address='$address'
                           WHERE memberId='$memberId'";
            if (!mysqli_query($conn, $sql_member)) {
                throw new Exception("Member update failed: " . mysqli_error($conn));
            }

            $sql_account = "UPDATE Account 
                            SET AccountNumber='$accountNumber', Balance='$balance'
                            WHERE MemberID='$memberId'";
            if (!mysqli_query($conn, $sql_account)) {
                throw new Exception("Account update failed: " . mysqli_error($conn));
            }

            mysqli_commit($conn);
            $message = "update done successfully!";
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $message = "Update failed: " . $e->getMessage();
        }
    } else {
        $message = "enter a member id first.";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Member & Account - SACCO</title>
    <style>
        body {
            font-family: Arial;
            background: #f2f2f2;
            margin: 0;
        }

        .container {
            width: 600px;
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

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-top: 10px;
            font-weight: bold;
        }

        input,
        select {
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type=submit] {
            margin-top: 15px;
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
        }

        a {
            text-decoration: none;
            color: #003366;
            text-align: center;
            display: block;
            margin-top: 15px;
        }

        a:hover {
            text-decoration: underline;
        }

        fieldset {
            margin-top: 20px;
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 4px;
            display: grid;
        }

        legend {
            font-weight: bold;
            color: #003366;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Edit Member & Account</h2>

        <!-- Search -->
        <form method="POST">
            <label>Enter Member ID to Edit:</label>
            <input type="number" name="member_id" required>
            <input type="submit" name="search" value="Load Details">
        </form>

        <!-- Edit Form -->
        <?php if ($member): ?>
            <form method="POST">
                <input type="hidden" name="member_id" value="<?php echo $member['memberId']; ?>">

                <fieldset>
                    <legend>Member Information</legend>
                    <label>First Name:</label>
                    <input type="text" name="firstName" value="<?php echo $member['firstName']; ?>" required>

                    <label>Last Name:</label>
                    <input type="text" name="lastName" value="<?php echo $member['lastName']; ?>" required>

                    <label>Gender:</label>
                    <select name="gender" required>
                        <option value="">-- Select Gender --</option>
                        <option value="M" <?php if ($member['gender'] == 'M') echo 'selected'; ?>>Male</option>
                        <option value="F" <?php if ($member['gender'] == 'F') echo 'selected'; ?>>Female</option>
                    </select>

                    <label>Contact:</label>
                    <input type="text" name="contact" maxlength="10" value="<?php echo $member['Contact']; ?>" required>

                    <label>Address:</label>
                    <input type="text" name="address" value="<?php echo $member['address']; ?>">
                </fieldset>

                <fieldset>
                    <legend>Account Information</legend>
                    <?php if ($account): ?>
                        <label>Account Number:</label>
                        <input type="text" name="accountNumber" value="<?php echo $account['AccountNumber']; ?>" required>

                        <label>Balance :</label>
                        <input type="number" name="balance" step="0.01" value="<?php echo $account['Balance']; ?>" required>
                    <?php else: ?>
                        <p style="color:red;">No account found for this member.</p>
                    <?php endif; ?>
                </fieldset>

                <input type="submit" name="update" value="Update Details">
            </form>
        <?php endif; ?>

        <!-- Message -->
        <?php if ($message != ""): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <a href="#" onclick="top.location.href='index.php';"> Back to Home</a>
    </div>

</body>

</html>