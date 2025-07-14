<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Feedback Form - LankaTransit</title>

  <!-- Bootstrap & Styling -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    :root {
      --primary-dark: #060725;
      --accent-color: #f1424f;
      --bg-color: #f8f8f8;
      --card-bg: #ffffff;
    }

    body {
      background-color: var(--bg-color);
      font-family: 'Segoe UI', sans-serif;
      color: var(--primary-dark);
    }

    .feedback-container {
      max-width: 750px;
      margin: 60px auto;
      background-color: var(--card-bg);
      padding: 40px 30px;
      border-radius: 16px;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.07);
    }

    .feedback-container h4 {
      font-weight: 700;
      color: var(--primary-dark);
    }

    .form-label {
      font-weight: 500;
      color: var(--primary-dark);
    }

    .form-control, .form-select {
      border-radius: 8px;
      font-size: 15px;
      padding: 10px 14px;
    }

    .form-control:focus, .form-select:focus {
      border-color: var(--accent-color);
      box-shadow: 0 0 0 0.15rem rgba(241, 66, 79, 0.25);
    }

    .btn-lanka {
      background-color: var(--accent-color);
      color: white;
      font-weight: 500;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      font-size: 16px;
      transition: all 0.3s ease;
    }

    .btn-lanka:hover {
      background-color: #d23540;
    }

    @media (max-width: 576px) {
      .feedback-container {
        padding: 25px 20px;
      }

      .btn-lanka {
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="feedback-container">
      <h4 class="mb-3">Feedback Form</h4>
      <p class="text-muted mb-4">We value your opinion! Please help us improve the LankaTransit experience by sharing your thoughts below.</p>

      <form action="submit_feedback.php" method="POST">

        <div class="mb-3">
          <label for="name" class="form-label">Your Name</label>
          <input type="text" name="name" id="name" class="form-control" placeholder="Enter your name" required />
        </div>

        <div class="mb-3">
          <label for="feedback_type" class="form-label">Feedback Type</label>
          <select name="feedback_type" id="feedback_type" class="form-select" required>
            <option value="">-- Select Type --</option>
            <option value="Service Quality">Service Quality</option>
            <option value="UI/UX Experience">UI/UX Experience</option>
            <option value="Booking Process">Booking Process</option>
            <option value="Other">Other</option>
          </select>
        </div>

        <div class="mb-3">
          <label for="rating" class="form-label">Rating</label>
          <select name="rating" id="rating" class="form-select" required>
            <option value="">-- Select Rating --</option>
            <option value="5">Excellent (5)</option>
            <option value="4">Very Good (4)</option>
            <option value="3">Good (3)</option>
            <option value="2">Fair (2)</option>
            <option value="1">Poor (1)</option>
          </select>
        </div>

        <div class="mb-3">
          <label for="message" class="form-label">Comments</label>
          <textarea name="message" id="message" rows="5" class="form-control" placeholder="Write your comments here..." required></textarea>
        </div>

        <div class="text-end">
          <button type="submit" class="btn btn-lanka">Submit Feedback</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
