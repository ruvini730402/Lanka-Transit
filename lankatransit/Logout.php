<?php
session_start();

// Unset all session variables
session_unset();

// Destroy the session
session_destroy();

// Clear session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Logout Successful</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    :root {
      --primary-color: #800000;
      --secondary-color: #060725;
      --success-green: #28a745;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background-color: #f4f4f4;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 20px;
    }

    .logout-box {
      background-color: white;
      border: 1px solid #ddd;
      padding: 30px 25px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      text-align: center;
      width: 100%;
      max-width: 400px;
    }

    .success-icon {
      width: 80px;
      height: 80px;
      background-color: var(--success-green);
      color: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 40px;
      margin: 0 auto 20px;
      animation: pop 0.3s ease-out;
    }

    @keyframes pop {
      0% {
        transform: scale(0);
        opacity: 0;
      }
      100% {
        transform: scale(1);
        opacity: 1;
      }
    }

    h1 {
      color: var(--secondary-color);
      font-size: 1.6rem;
      margin-bottom: 10px;
    }

    p {
      color: #333;
      font-size: 1rem;
      margin-bottom: 25px;
    }

    a {
      display: inline-block;
      text-decoration: none;
      background-color: var(--primary-color);
      color: white;
      padding: 10px 20px;
      border-radius: 5px;
      font-weight: 500;
      transition: background-color 0.3s ease;
      font-size: 0.95rem;
    }

    a:hover {
      background-color: #d93b46;
    }

    @media (max-width: 480px) {
      .logout-box {
        padding: 25px 20px;
      }

      .success-icon {
        width: 65px;
        height: 65px;
        font-size: 32px;
      }

      h1 {
        font-size: 1.4rem;
      }

      p {
        font-size: 0.95rem;
      }

      a {
        font-size: 0.9rem;
        padding: 9px 18px;
      }
    }
  </style>
</head>
<body>
  <div class="logout-box">
    <div class="success-icon">✔</div>
    <h1>Logged Out</h1>
    <p>You have successfully logged out of your account.</p>
    <a href="login.php">Return to Login</a><br><br>
    <a href="index.php">Return to Home Page</a>
  </div>
    
</body>
</html>
