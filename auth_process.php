<?php
session_start();
require_once 'config.php';

/* ================= REGISTER ================= */
if (isset($_POST['register_btn'])) {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check_email = $conn->query("SELECT id FROM users WHERE email = '$email'");

    if ($check_email->num_rows > 0) {
        $_SESSION['alerts'][] = [
            'type' => 'error',
            'message' => 'Email is already registered!'
        ];
        $_SESSION['active_form'] = 'register';
    } else {
        // Generate a token for verification
        $token = bin2hex(random_bytes(32));

        $stmt = $conn->prepare("INSERT INTO users (name, email, password, verification_token) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $password, $token);
        $stmt->execute();

        // --------------------------
        // LOCAL TEST VERIFICATION LINK
        // --------------------------
        // Instead of sending an email, display the verification link in the alerts for testing
        $verificationLink = "http://localhost/login_registration/verify.php?token=$token";

        $_SESSION['alerts'][] = [
            'type' => 'success',
            'message' => "Registration successful! <br>Test verification link: <a href='$verificationLink'>$verificationLink</a>"
        ];
        $_SESSION['active_form'] = 'login';
    }

    header('Location: index.php');
    exit();
}

/* ================= LOGIN ================= */
if (isset($_POST['login_btn'])) {

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $redirect = $_POST['redirect_after_login'] ?? 'index.php';

    $result = $conn->query("SELECT * FROM users WHERE email = '$email'");
    $user = $result->num_rows > 0 ? $result->fetch_assoc() : null;

    if ($user && password_verify($password, $user['password'])) {

        if (!$user['is_verified']) {
            $_SESSION['alerts'][] = [
                'type' => 'error',
                'message' => 'Please verify your email first'
            ];
            header('Location: index.php');
            exit();
        }

        $_SESSION['name'] = $user['name'];
        $_SESSION['user_id'] = $user['id'];

        // 🔎 Check if user has faction
        if (empty($user['faction_id'])) {

            $_SESSION['alerts'][] = [
                'type' => 'success',
                'message' => 'Login successful! Please choose your faction.'
            ];

            header("Location: faction/choose_faction.php");
            exit();
        }

        $_SESSION['alerts'][] = [
            'type' => 'success',
            'message' => 'Login successful'
        ];

header("Location: $redirect");
exit();

    } else {
        $_SESSION['alerts'][] = [
            'type' => 'error',
            'message' => 'Incorrect email or password'
        ];
        $_SESSION['active_form'] = 'login';

        header('Location: index.php');
        exit();
    }
}
?>