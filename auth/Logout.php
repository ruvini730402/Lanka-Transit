<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Logout Successful</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <style>
    :root {
      --primary-color: #800000;
      --secondary-color: #333;
      --success-green: #28a745;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      width: 100%;
      height: 100%;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #fff;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
      overflow-x: hidden;
    }

    .logout-box {
      width: 100%;
      max-width: 420px;
      background-color: #fff;
      border: 1px solid #ddd;
      padding: 32px 28px;
      border-radius: 12px;
      text-align: center;
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
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
      font-size: 42px;
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
      font-size: 1.7rem;
      color: var(--secondary-color);
      margin-bottom: 10px;
    }

    p {
      font-size: 1rem;
      color: #444;
      margin-bottom: 25px;
      line-height: 1.5;
    }

    a {
      display: inline-block;
      text-decoration: none;
      background-color: var(--primary-color);
      color: white;
      padding: 10px 22px;
      border-radius: 6px;
      font-weight: 500;
      font-size: 0.95rem;
      transition: background-color 0.3s ease;
    }

    a:hover {
      background-color: #a80000;
    }

    .link-small {
      display: inline-block;
      background: none;
      color: #444;
      padding: 0;
      font-size: 0.9rem;
      font-weight: normal;
      text-decoration: underline;
      margin-top: 12px;
    }

    .link-small:hover {
      color: #004999;
    }

    @media (max-width: 480px) {
      .logout-box {
        padding: 25px 18px;
      }

      .success-icon {
        width: 65px;
        height: 65px;
        font-size: 32px;
      }

      h1 {
        font-size: 1.5rem;
      }

      p {
        font-size: 0.95rem;
      }

      a {
        font-size: 0.9rem;
        padding: 9px 18px;
      }
    }

    @media (max-width: 360px) {
      .logout-box {
        padding: 20px 15px;
      }

      h1 {
        font-size: 1.35rem;
      }

      .success-icon {
        width: 60px;
        height: 60px;
        font-size: 28px;
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
    <a href="../index.php" class="link-small">Return to Home Page</a>
  </div>

</body>
</html>
