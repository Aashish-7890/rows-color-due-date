<?php
// Database connection
$host = "localhost";
$user = "root";
$password = "";
$database = "login";

$conn = new mysqli($host, $user, $password, $database);

// Connection check
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ Expired records delete karein
if (isset($_GET['delete_expired'])) {
    // // ✅ Pehle check karo expired records hain ya nahi
    // $check_sql = "SELECT COUNT(*) as count FROM products WHERE expiry_date < CURDATE()";
    // $check_result = $conn->query($check_sql);
    // $row = $check_result->fetch_assoc();

    // if ($row['count'] > 0) {
    //     // ✅ Expired records delete karo
    //     $sql = "DELETE FROM products WHERE expiry_date < CURDATE()";
    //     if ($conn->query($sql) === TRUE) {
    //         echo "<script>alert('Expired records deleted successfully!');</script>";
    //     } else {
    //         echo "<script>alert('Error deleting records: " . $conn->error . "');</script>";
    //     }
    // } else {
    //     // ✅ Expired records nahi mile toh alert show karo
    //     echo "<script>alert('No expired data available!');</script>";
    // }
    $sql = "DELETE FROM products WHERE expiry_date < CURDATE()";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Expired records deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error deleting records: " . $conn->error . "');</script>";
    }
    // ✅ Same page reload
    echo "<script>window.location.href='" . $_SERVER['PHP_SELF'] . "';</script>";
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            padding: 10px;
            text-align: left;
        }

        .red {
            background-color: red;
            color: white;
        }

        .blue {
            background-color: blue;
            color: white;
        }

        .yellow {
            background-color: yellow;
        }

        .green {
            background-color: green;
            color: white;
        }
    </style>
    <?php
    $check_sql = "SELECT COUNT(*) as count FROM products WHERE expiry_date < CURDATE()";
    $check_result = $conn->query($check_sql);
    $row = $check_result->fetch_assoc();
    $expired_count = $row['count'] ?? 0;

    ?>
    <script>
        function deleteExpiredTasks() {

            let expiredCount = <?php echo $expired_count; ?>; // ✅ Pass PHP variable to JavaScript

            if (expiredCount > 0) {
                if (confirm("Are you sure you want to delete expired records?")) {
                    window.location.href = "?delete_expired=true";
                }
            } else {
                alert("No expired data available!");
            }
        }
    </script>
</head>

<body>

    <h2>Product List</h2>
    <button onclick="deleteExpiredTasks()">Delete Expired Products</button>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>OEM No</th>
            <th>Price</th>
            <th>Create Date</th>
            <th>Expiry Date</th>
        </tr>

        <?php
        // ✅ Non-expired records ko fetch karo
        //$sql = "SELECT * FROM products WHERE expiry_date >= CURDATE()";
        $sql = "SELECT * FROM products";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $today = date("Y-m-d");
                $expiry_date = $row["expiry_date"];
                $days_due = (strtotime($expiry_date) - strtotime($today)) / (60 * 60 * 24);

                // ✅ Expiry date ke hisaab se row ka color set karo
                if ($days_due >= 1 && $days_due <= 3) {
                    $color_class = "red";
                } elseif ($days_due > 3 && $days_due <= 31) {
                    $color_class = "blue";
                } elseif ($days_due > 31 && $days_due <= 45) {
                    $color_class = "yellow";
                } elseif ($days_due > 45 && $days_due <= 365) {
                    $color_class = "green";
                } else {
                    $color_class = "";
                }
                ?>

                <!-- ✅ PHP ke bahar HTML table row -->
                <tr class="<?php echo $color_class; ?>">
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['oem_no']; ?></td>
                    <td><?php echo $row['price']; ?></td>
                    <td><?php echo $row['create_date']; ?></td>
                    <td><?php echo $row['expiry_date']; ?></td>
                </tr>

                <?php
            }
        } else {
            ?>

            <tr>
                <td colspan="6">No active products</td>
            </tr>

            <?php
        }

        $conn->close();
        ?>
    </table>

</body>

</html>
