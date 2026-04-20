<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'company') {
?>
<!DOCTYPE html>
<html>
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark">

<div class="modal fade show" style="display:block;">
  <div class="modal-dialog">
    <div class="modal-content bg-dark text-white text-center p-4">
      <h4>Access Denied</h4>
      <p>Login as a Company to post jobs</p>
      <button onclick="window.location.href='index.php'" class="btn btn-danger">
        Go Back
      </button>
    </div>
  </div>
</div>

</body>
</html>
<?php
exit();
}

$conn = new mysqli("localhost", "root", "mypassword", "hirehub");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"] ?? "");
    $company = trim($_POST["company"] ?? "");
    $location = trim($_POST["location"] ?? "");
    $salary = trim($_POST["salary"] ?? "");
    $job_type = trim($_POST["job_type"] ?? "Full-time");
    $category = trim($_POST["category"] ?? "General");
    $description = trim($_POST["description"] ?? "");

    if ($title === "" || $company === "" || $location === "" || $salary === "" || $description === "") {
        $error = "Please fill all required fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO jobs (user_id, title, company, location, salary, job_type, category, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "isssssss",
            $_SESSION['user_id'],
            $title,
            $company,
            $location,
            $salary,
            $job_type,
            $category,
            $description
        );

        if ($stmt->execute()) {
            $success = "Job posted successfully.";
        } else {
            $error = "Failed to post job.";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Post Job - JobPortal</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #0b0f19;
      color: #f8fafc;
    }

    .post-job-wrapper {
      min-height: 100vh;
      padding: 50px 20px;
    }

    .post-job-card {
      max-width: 760px;
      margin: auto;
      background: #111827;
      border: 1px solid #1f2937;
      border-radius: 16px;
      padding: 30px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.35);
    }

    .post-job-card h1 {
      text-align: center;
      margin-bottom: 10px;
      color: #60a5fa;
    }

    .post-job-card p {
      text-align: center;
      color: #cbd5e1;
      margin-bottom: 25px;
    }

    .post-job-card .form-label {
      color: #f8fafc;
      font-weight: 600;
    }

    .post-job-card .form-control,
    .post-job-card .form-select,
    .post-job-card textarea {
      background: #1e293b;
      color: white;
      border: 1px solid #334155;
    }

    .post-job-card .form-control:focus,
    .post-job-card .form-select:focus,
    .post-job-card textarea:focus {
      background: #1e293b;
      color: white;
      border-color: #60a5fa;
      box-shadow: none;
    }

    .post-job-btn {
      width: 100%;
      background: linear-gradient(45deg, #2563eb, #60a5fa);
      border: none;
      padding: 12px;
      font-weight: 600;
      border-radius: 8px;
      color: white;
    }

    .post-job-btn:hover {
      opacity: 0.95;
      color: white;
    }

    .top-links {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      flex-wrap: wrap;
      gap: 10px;
    }

    .top-link {
      text-decoration: none;
      color: #60a5fa;
      font-weight: 600;
    }

    .top-link:hover {
      color: #93c5fd;
    }

    .welcome-text {
      color: #e2e8f0;
      font-weight: 600;
    }

    .logout-btn {
      text-decoration: none;
      padding: 8px 16px;
      border-radius: 8px;
      background: #dc2626;
      color: white;
      font-weight: 600;
    }

    .logout-btn:hover {
      background: #b91c1c;
      color: white;
    }
  </style>
</head>
<body>

<div class="post-job-wrapper">
  <div class="post-job-card">

    <div class="top-links">
      <div>
        <a href="index.php" class="top-link">← Back to Home</a>
      </div>
      <div class="welcome-text">
        Company: <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Company'); ?>
      </div>
      <div>
        <a href="logout.php" class="logout-btn">Logout</a>
      </div>
    </div>

    <h1>Post a New Job</h1>
    <p>Only company accounts can post jobs here.</p>

    <?php if ($success !== ""): ?>
      <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if ($error !== ""): ?>
      <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="mb-3">
        <label class="form-label">Job Title</label>
        <input type="text" name="title" class="form-control" placeholder="e.g. Frontend Developer" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Company Name</label>
        <input type="text" name="company" class="form-control" placeholder="e.g. TechSoft Pvt Ltd" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Location</label>
        <input type="text" name="location" class="form-control" placeholder="e.g. Kolkata" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Salary</label>
        <input type="text" name="salary" class="form-control" placeholder="e.g. ₹35,000/month" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Job Type</label>
        <select name="job_type" class="form-select">
          <option value="Full-time">Full-time</option>
          <option value="Part-time">Part-time</option>
          <option value="Internship">Internship</option>
          <option value="Remote">Remote</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Category</label>
        <input type="text" name="category" class="form-control" placeholder="e.g. IT, Design, Marketing">
      </div>

      <div class="mb-3">
        <label class="form-label">Job Description</label>
        <textarea name="description" rows="6" class="form-control" placeholder="Write the full job description here..." required></textarea>
      </div>

      <button type="submit" class="btn post-job-btn">Post Job</button>
    </form>
  </div>
</div>

</body>
</html>
