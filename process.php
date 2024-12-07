
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    $conn = new mysqli("localhost", "username", "password", "database_name");
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Handle file upload
    $target_dir = "uploads/";
    $file_extension = strtolower(pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Check if image file is valid
    $valid_types = array("jpg", "jpeg", "png", "gif");
    if (!in_array($file_extension, $valid_types)) {
        die("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
    }
    
    if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO persons (firstname, lastname, email, dob, phone, address, profile_picture) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("sssssss", 
            $_POST['firstname'],
            $_POST['lastname'],
            $_POST['email'],
            $_POST['dob'],
            $_POST['phone'],
            $_POST['address'],
            $new_filename
        );
        
        if ($stmt->execute()) {
            echo "Record saved successfully";
        } else {
            echo "Error: " . $stmt->error;
        }
        
        $stmt->close();
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
    
    $conn->close();
}
?>
