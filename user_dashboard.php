<?php
session_start();

/* Allow only job seekers */
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header("Location: index.php");
    exit();
}

$conn = new mysqli("localhost", "root", "mypassword", "hirehub");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/* Fetch applications */
$stmt = $conn->prepare("
    SELECT applications.*, jobs.title, jobs.company 
    FROM applications 
    JOIN jobs ON applications.job_id = jobs.id 
    WHERE applications.user_id = ?
    ORDER BY applications.applied_at DESC
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

/* Count applied jobs */
$countStmt = $conn->prepare("SELECT COUNT(*) as total FROM applications WHERE user_id=?");
$countStmt->bind_param("i", $_SESSION['user_id']);
$countStmt->execute();
$countResult = $countStmt->get_result()->fetch_assoc();
$totalApplications = $countResult['total'];

/* Count saved jobs */
$savedStmt = $conn->prepare("SELECT COUNT(*) as total FROM saved_jobs WHERE user_id=?");
$savedStmt->bind_param("i", $_SESSION['user_id']);
$savedStmt->execute();
$savedResult = $savedStmt->get_result()->fetch_assoc();
$totalSaved = $savedResult['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Dashboard - JobPortal</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
  background: #0b0f19;
  color: white;
}

/* Navbar */
.navbar-custom {
  background: #111827;
  padding: 15px 25px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.navbar-custom a {
  color: white;
  text-decoration: none;
  margin-left: 15px;
}

.navbar-custom a:hover {
  color: #60a5fa;
}

/* Cards */
.dashboard {
  padding: 40px 20px;
}

.stats {
  display: flex;
  gap: 20px;
  margin-bottom: 30px;
  flex-wrap: wrap;
}

.stat-card {
  flex: 1;
  min-width: 200px;
  background: #111827;
  padding: 20px;
  border-radius: 12px;
  border: 1px solid #1f2937;
  text-align: center;
}

.stat-card h3 {
  color: #60a5fa;
  margin-bottom: 10px;
}

/* Table */
.table-custom {
  background: #111827;
  border-radius: 10px;
  overflow: hidden;
}

.table thead {
  background: #1e293b;
}
.table-wrap {
  background: #0f172a;
  border: 1px solid #1f2937;
  border-radius: 14px;
  overflow-x: auto;   ✅ ALLOW HORIZONTAL SCROLL
  overflow-y: hidden;
  -webkit-overflow-scrolling: touch;
  width: 100%;
}
.status {
  padding: 5px 10px;
  border-radius: 6px;
  font-weight: 600;
}

.status.Pending {
  background: #facc15;
  color: black;
}

.status.Accepted {
  background: #22c55e;
  color: white;
}

.status.Rejected {
  background: #ef4444;
  color: white;
}

/* Buttons */
.btn-main {
  background: linear-gradient(45deg, #2563eb, #60a5fa);
  border: none;
  color: white;
}

.btn-main:hover {
  opacity: 0.9;
}

</style>
</head>

<body>

<!-- NAVBAR -->
<div class="navbar-custom">
  <div><strong>JobPortal</strong></div>

  <div>
    <a href="index.php">Home</a>
    <a href="jobs.php">Jobs</a>
    <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
  </div>
</div>

<div class="container dashboard">

  <h2 class="mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h2>

  <!-- STATS -->
  <div class="stats">
    <div class="stat-card">
      <h3><?php echo $totalApplications; ?></h3>
      <p>Applied Jobs</p>
    </div>


  </div>

  <!-- ACTION BUTTONS -->
  <div class="mb-4">
    <a href="jobs.php" class="btn btn-main">Browse Jobs</a>
  </div>

  <!-- APPLICATION TABLE -->
  <div class="table-responsive table-custom table-wrap">
    <table class="table table-dark table-bordered align-middle">
      <thead>
        <tr>
          <th>Job Title</th>
          <th>Company</th>
          <th>Status</th>
          <th>Applied On</th>
          <th>Resume</th>
        </tr>
      </thead>
      <tbody>

      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo htmlspecialchars($row['company']); ?></td>

            <td>
              <span class="status <?php echo $row['status']; ?>">
                <?php echo $row['status']; ?>
              </span>
            </td>

            <td><?php echo date("d M Y", strtotime($row['applied_at'])); ?></td>

            <td>
              <a href="<?php echo htmlspecialchars($row['resume']); ?>" target="_blank" class="btn btn-sm btn-info">
                View PDF
              </a>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="5" class="text-center">No applications yet</td>
        </tr>
      <?php endif; ?>

      </tbody>
    </table>
  </div>

</div>

</body>
</html>
