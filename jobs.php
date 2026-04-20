<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
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
      <p>Login as a Job Seeker to apply for jobs</p>
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


$conn = new mysqli("sql305.infinityfree.com", "if0_41614068", "Swagata1077", "if0_41614068_test");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM jobs ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Jobs - JobPortal</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .jobs-wrapper {
      min-height: 100vh;
      background: #0b0f19;
      color: white;
      padding: 50px 20px;
    }

    .jobs-heading {
      text-align: center;
      margin-bottom: 40px;
    }

    .jobs-heading h1 {
      color: #60a5fa;
      margin-bottom: 10px;
    }

    .jobs-heading p {
      color: #cbd5e1;
    }

    .jobs-grid {
      display: flex;
      flex-wrap: wrap;
      gap: 25px;
      justify-content: center;
    }

    .job-card-custom {
      width: 320px;
      background: #111827;
      border: 1px solid #1f2937;
      border-radius: 14px;
      padding: 22px;
      box-shadow: 0 8px 22px rgba(0,0,0,0.35);
    }

    .job-card-custom h3 {
      color: #60a5fa;
      margin-bottom: 12px;
      font-size: 22px;
    }

    .job-card-custom p {
      margin-bottom: 8px;
      color: #e2e8f0;
    }

    .apply-btn-custom {
      display: inline-block;
      margin-top: 12px;
      text-decoration: none;
      background: linear-gradient(45deg, #2563eb, #60a5fa);
      color: white;
      padding: 10px 18px;
      border-radius: 8px;
      font-weight: 600;
    }

    .apply-btn-custom:hover {
      color: white;
      opacity: 0.9;
    }

    .back-home {
      display: inline-block;
      margin-bottom: 25px;
      color: #60a5fa;
      text-decoration: none;
    }

    .no-jobs {
      text-align: center;
      color: #cbd5e1;
      font-size: 18px;
      margin-top: 30px;
    }
  </style>
</head>
<body>

<div class="jobs-wrapper">
  <div class="container">
    <a href="index.php" class="back-home">← Back to Home</a>

    <div class="jobs-heading">
      <h1>Available Jobs</h1>
      <p>Explore the latest opportunities and apply now.</p>
    </div>

    <?php if ($result && $result->num_rows > 0): ?>
      <div class="jobs-grid">
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="job-card-custom">
            <h3><?php echo htmlspecialchars($row['title']); ?></h3>
            <p><strong>Company:</strong> <?php echo htmlspecialchars($row['company']); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
            <p><strong>Salary:</strong> <?php echo htmlspecialchars($row['salary']); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars(substr($row['description'], 0, 100)); ?>...</p>

            <a href="apply_job.php?id=<?php echo $row['id']; ?>" class="apply-btn-custom">
              Apply Now
            </a>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <div class="no-jobs">No jobs posted yet.</div>
    <?php endif; ?>
  </div>
</div>

</body>
</html>