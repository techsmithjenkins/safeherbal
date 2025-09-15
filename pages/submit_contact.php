<?php
     // submit_contact.php - Process contact form submissions
     require_once '../config/db_connect.php'; // Updated path

     if ($_SERVER['REQUEST_METHOD'] === 'POST') {
         $full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING);
         $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
         $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
         $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

         if ($full_name && $email && $message) {
             $stmt = $conn->prepare("INSERT INTO contact_messages (full_name, email, phone, message, submitted_at) VALUES (?, ?, ?, ?, NOW())");
             $stmt->bind_param("ssss", $full_name, $email, $phone, $message);
             if ($stmt->execute()) {
                 echo json_encode(['success' => true, 'message' => 'Thank you for your message. We\'ll get back to you soon!']);
             } else {
                 echo json_encode(['success' => false, 'message' => 'Error saving message: ' . $conn->error]);
             }
             $stmt->close();
         } else {
             echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
         }
     }
     ?>