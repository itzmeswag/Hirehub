<?php
session_start();

/* Allow only companies */
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'company') {
    header("Location: index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "mypassword", "hirehub");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/* Total posted jobs */
$totalJobs = 0;
$stmt1 = $conn->prepare("SELECT COUNT(*) AS total FROM jobs WHERE user_id = ?");
$stmt1->bind_param("i", $_SESSION['user_id']);
$stmt1->execute();
$res1 = $stmt1->get_result()->fetch_assoc();
if ($res1) {
    $totalJobs = $res1['total'];
}
$stmt1->close();

/* Total applicants across all jobs posted by this company */
$totalApplicants = 0;
$stmt2 = $conn->prepare("
    SELECT COUNT(applications.id) AS total
    FROM applications
    JOIN jobs ON applications.job_id = jobs.id
    WHERE jobs.user_id = ?
");
$stmt2->bind_param("i", $_SESSION['user_id']);
$stmt2->execute();
$res2 = $stmt2->get_result()->fetch_assoc();
if ($res2) {
    $totalApplicants = $res2['total'];
}
$stmt2->close();

/* Fetch jobs with applicant count */
$stmt3 = $conn->prepare("
    SELECT jobs.*,
           COUNT(applications.id) AS applicant_count
    FROM jobs
    LEFT JOIN applications ON jobs.id = applications.job_id
    WHERE jobs.user_id = ?
    GROUP BY jobs.id
    ORDER BY jobs.created_at DESC
");
$stmt3->bind_param("i", $_SESSION['user_id']);
$stmt3->execute();
$jobsResult = $stmt3->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Company Dashboard - HireHub</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #0b0f19;
      color: #f8fafc;
      min-height: 100vh;
    }

    .dashboard-wrapper {
      padding: 40px 20px;
    }

    .dashboard-card {
      max-width: 1200px;
      margin: auto;
      background: #111827;
      border: 1px solid #1f2937;
      border-radius: 18px;
      padding: 30px;
      box-shadow: 0 10px 28px rgba(0, 0, 0, 0.35);
    }

    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 12px;
      margin-bottom: 25px;
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

    .heading h1 {
      color: #60a5fa;
      margin-bottom: 8px;
    }

    .heading p {
      color: #cbd5e1;
      margin-bottom: 0;
    }

    .stats-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 18px;
      margin: 30px 0;
    }

    .stat-box {
      background: #1e293b;
      border: 1px solid #334155;
      border-radius: 14px;
      padding: 22px;
      text-align: center;
    }

    .stat-box h2 {
      color: #60a5fa;
      font-size: 32px;
      margin-bottom: 8px;
    }

    .stat-box p {
      color: #e2e8f0;
      margin: 0;
      font-weight: 500;
    }

    .action-buttons {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
      margin-bottom: 28px;
    }

    .btn-main {
      background: linear-gradient(45deg, #2563eb, #60a5fa);
      border: none;
      color: white;
      font-weight: 600;
      padding: 10px 18px;
      border-radius: 8px;
      text-decoration: none;
    }

    .btn-main:hover {
      opacity: 0.95;
      color: white;
    }

    .btn-secondary-custom {
      background: #1e293b;
      border: 1px solid #334155;
      color: #f8fafc;
      font-weight: 600;
      padding: 10px 18px;
      border-radius: 8px;
      text-decoration: none;
    }

    .btn-secondary-custom:hover {
      color: white;
      background: #334155;
    }

    .section-title {
      color: #60a5fa;
      margin-bottom: 18px;
      font-size: 24px;
      font-weight: 700;
    }

    .table-wrap {
      background: #0f172a;
      border: 1px solid #1f2937;
      border-radius: 14px;
      overflow-x: auto;
      overflow-y: hidden;
      -webkit-overflow-scrolling: touch;
      width: 100%;
    }

    .table {
      margin-bottom: 0;
      min-width: 900px;
    }

    .table thead th {
      background: #1e293b !important;
      color: #f8fafc !important;
      border-color: #334155 !important;
      white-space: nowrap;
    }

    .table tbody td {
      color: #e2e8f0 !important;
      border-color: #334155 !important;
      vertical-align: middle;
      white-space: nowrap;
    }

    .badge-applicants {
      background: #2563eb;
      color: white;
      padding: 6px 10px;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 600;
      display: inline-block;
      white-space: nowrap;
    }

    .small-btn {
      font-size: 14px;
      padding: 8px 14px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      display: inline-block;
      white-space: nowrap;
    }

    .view-btn {
      background: #0ea5e9;
      color: white;
    }

    .view-btn:hover {
      background: #0284c7;
      color: white;
    }

    .empty-state {
      text-align: center;
      color: #cbd5e1;
      padding: 30px 15px;
    }

    @media (max-width: 768px) {
      .dashboard-card {
        padding: 20px;
      }

      .heading h1 {
        font-size: 28px;
      }

      .table {
        min-width: 850px;
      }
    }
  </style>
</head>
<body>

<div class="dashboard-wrapper">
  <div class="dashboard-card">

    <div class="top-bar">
      <a href="index.php" class="top-link">← Back to Home</a>
      <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="heading">
      <h1>Company Dashboard</h1>
      <p>Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Company'); ?>. Manage your jobs and applicants here.</p>
    </div>

    <div class="stats-row">
      <div class="stat-box">
        <h2><?php echo $totalJobs; ?></h2>
        <p>Total Jobs Posted</p>
      </div>

      <div class="stat-box">
        <h2><?php echo $totalApplicants; ?></h2>
        <p>Total Applicants</p>
      </div>
    </div>

    <div class="action-buttons">
      <a href="post_job.php" class="btn-main">Post New Job</a>
    </div>

    <h2 class="section-title">My Posted Jobs</h2>

    <div class="table-wrap">
      <table class="table table-dark table-bordered align-middle">
        <thead>
          <tr>
            <th>Job Title</th>
            <th>Location</th>
            <th>Salary</th>
            <th>Type</th>
            <th>Category</th>
            <th>Applicants</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        <?php if ($jobsResult && $jobsResult->num_rows > 0): ?>
          <?php while ($row = $jobsResult->fetch_assoc()): ?>
            <tr>
              <td><?php echo htmlspecialchars($row['title']); ?></td>
              <td><?php echo htmlspecialchars($row['location']); ?></td>
              <td><?php echo htmlspecialchars($row['salary']); ?></td>
              <td><?php echo htmlspecialchars($row['job_type'] ?? 'Full-time'); ?></td>
              <td><?php echo htmlspecialchars($row['category'] ?? 'General'); ?></td>
              <td>
                <span class="badge-applicants">
                  <?php echo $row['applicant_count']; ?> Applicants
                </span>
              </td>
              <td>
                <a href="view_applications.php?job_id=<?php echo $row['id']; ?>" class="small-btn view-btn">
                  View Applicants
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" class="empty-state">No jobs posted yet.</td>
          </tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>

</body>
</html>
