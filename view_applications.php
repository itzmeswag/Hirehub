<?php
session_start();

/* Only company can access */
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || trim($_SESSION['user_role']) !== 'company') {
    echo "
    <script>
        alert('Access denied. Only companies can view applications.');
        window.location.href = 'index.php';
    </script>
    ";
    exit();
}

$conn = new mysqli("localhost", "root", "mypassword", "hirehub");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$job_id = isset($_GET['job_id']) ? (int)$_GET['job_id'] : 0;

if ($job_id <= 0) {
    die("Invalid job ID.");
}

/* Get job info and confirm it belongs to this company */
$stmt = $conn->prepare("SELECT * FROM jobs WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $job_id, $_SESSION['user_id']);
$stmt->execute();
$jobResult = $stmt->get_result();

if ($jobResult->num_rows !== 1) {
    die("Job not found or access denied.");
}

$job = $jobResult->fetch_assoc();
$stmt->close();

/* Fetch applications for this job */
$stmt = $conn->prepare("
    SELECT id, applicant_name, applicant_email, applicant_phone, resume, cover_letter, status, applied_at
    FROM applications
    WHERE job_id = ?
    ORDER BY applied_at DESC
");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$applications = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Applications - JobPortal</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #0b0f19;
      color: #f8fafc;
    }

    .applications-wrapper {
      min-height: 100vh;
      padding: 50px 20px;
    }

    .applications-card {
      max-width: 1200px;
      margin: auto;
      background: #111827;
      border: 1px solid #1f2937;
      border-radius: 16px;
      padding: 30px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.35);
    }

    .top-links {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      flex-wrap: wrap;
      gap: 12px;
    }

    .top-link {
      text-decoration: none;
      color: #60a5fa;
      font-weight: 600;
    }

    .top-link:hover {
      color: #93c5fd;
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

    .applications-card h1 {
      color: #60a5fa;
      margin-bottom: 10px;
    }

    .job-info {
      background: #1e293b;
      border: 1px solid #334155;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 25px;
    }

    .job-info h3 {
      color: #60a5fa;
      margin-bottom: 10px;
    }

    .job-info p {
      margin-bottom: 8px;
      color: #e2e8f0;
    }

    .table-dark-custom {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      overflow: hidden;
      border-radius: 12px;
    }

    .table-dark-custom th,
    .table-dark-custom td {
      border: 1px solid #334155;
      padding: 14px;
      vertical-align: top;
      text-align: left;
    }

    .table-dark-custom th {
      background: #1e293b;
      color: #60a5fa;
      font-weight: 700;
    }

    .table-dark-custom td {
      background: #111827;
      color: #e2e8f0;
    }

    .resume-link {
      color: #60a5fa;
      text-decoration: none;
      font-weight: 600;
    }

    .resume-link:hover {
      color: #93c5fd;
    }

    .status-badge {
      display: inline-block;
      padding: 6px 12px;
      border-radius: 999px;
      font-size: 14px;
      font-weight: 600;
    }

    .status-pending {
      background: #f59e0b;
      color: #111827;
    }

    .status-accepted {
      background: #22c55e;
      color: white;
    }

    .status-rejected {
      background: #ef4444;
      color: white;
    }

    .action-btn {
      display: inline-block;
      margin: 4px 0;
      padding: 8px 12px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      color: white;
      text-align: center;
    }

    .accept-btn {
      background: #16a34a;
    }

    .accept-btn:hover {
      background: #15803d;
      color: white;
    }

    .reject-btn {
      background: #dc2626;
    }

    .reject-btn:hover {
      background: #b91c1c;
      color: white;
    }

    .empty-message {
      text-align: center;
      padding: 30px;
      color: #cbd5e1;
      font-size: 18px;
    }

    .cover-letter-box {
      max-width: 220px;
      white-space: normal;
      word-wrap: break-word;
    }

    @media (max-width: 992px) {
      .applications-card {
        padding: 20px;
      }

      .table-responsive-custom {
        overflow-x: auto;
      }

      .table-dark-custom {
        min-width: 1000px;
      }
    }
  </style>
</head>
<body>

<div class="applications-wrapper">
  <div class="applications-card">

    <div class="top-links">
      <div>
        <a href="company_dashboard.php" class="top-link">← Back to Dashboard</a>
      </div>
      <div>
        <span style="color:#e2e8f0; font-weight:600; margin-right:12px;">
          Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
        </span>
        <a href="logout.php" class="logout-btn">Logout</a>
      </div>
    </div>

    <h1>Applications for Job</h1>
    <p class="mb-4 text-light">Review all applicants for this job posting.</p>

    <div class="job-info">
      <h3><?php echo htmlspecialchars($job['title']); ?></h3>
      <p><strong>Company:</strong> <?php echo htmlspecialchars($job['company']); ?></p>
      <p><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
      <p><strong>Salary:</strong> <?php echo htmlspecialchars($job['salary']); ?></p>
      <p><strong>Type:</strong> <?php echo htmlspecialchars($job['job_type'] ?? 'Not specified'); ?></p>
      <p><strong>Category:</strong> <?php echo htmlspecialchars($job['category'] ?? 'General'); ?></p>
    </div>

    <?php if ($applications->num_rows > 0): ?>
      <div class="table-responsive-custom">
        <table class="table-dark-custom">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Resume</th>
              <th>Cover Letter</th>
              <th>Status</th>
              <th>Applied At</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $applications->fetch_assoc()): ?>
              <tr>
                <td><?php echo htmlspecialchars($row['applicant_name']); ?></td>
                <td><?php echo htmlspecialchars($row['applicant_email']); ?></td>
                <td><?php echo htmlspecialchars($row['applicant_phone']); ?></td>
                <td>
                  <a class="resume-link" href="<?php echo htmlspecialchars($row['resume']); ?>" target="_blank">
                    View PDF
                  </a>
                </td>
                <td class="cover-letter-box">
                  <?php echo nl2br(htmlspecialchars($row['cover_letter'] ?: 'No cover letter')); ?>
                </td>
                <td>
                  <?php
                    $statusClass = 'status-pending';
                    if ($row['status'] === 'Accepted') $statusClass = 'status-accepted';
                    if ($row['status'] === 'Rejected') $statusClass = 'status-rejected';
                  ?>
                  <span class="status-badge <?php echo $statusClass; ?>">
                    <?php echo htmlspecialchars($row['status']); ?>
                  </span>
                </td>
                <td><?php echo htmlspecialchars($row['applied_at']); ?></td>
                <td>
                  <a class="action-btn accept-btn" href="update_status.php?id=<?php echo $row['id']; ?>&status=Accepted">Accept</a><br>
                  <a class="action-btn reject-btn" href="update_status.php?id=<?php echo $row['id']; ?>&status=Rejected">Reject</a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="empty-message">
        No applications received for this job yet.
      </div>
    <?php endif; ?>

  </div>
</div>

</body>
</html>
