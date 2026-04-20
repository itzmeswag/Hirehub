<?php
session_start();

/* Allow only logged-in job seekers */
if (!isset($_SESSION['user_id'])) {
    echo "
    <script>
        alert('Please login first to apply for jobs.');
        window.location.href = 'index.php';
    </script>
    ";
    exit();
}

if (!isset($_SESSION['user_role']) || trim($_SESSION['user_role']) !== 'user') {
    echo "
    <script>
        alert('Only job seekers can apply for jobs.');
        window.location.href = 'index.php';
    </script>
    ";
    exit();
}

$conn = new mysqli("localhost", "root", "mypassword", "hirehub");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success = "";
$error = "";
$job = null;

$job_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($job_id <= 0) {
    die("Invalid job ID.");
}

/* Fetch selected job */
$stmt = $conn->prepare("SELECT * FROM jobs WHERE id = ?");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $job = $result->fetch_assoc();
} else {
    die("Job not found.");
}
$stmt->close();

/* Handle form submission */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $applicant_name = trim($_SESSION["user_name"] ?? "");
    $applicant_email = trim($_SESSION["user_email"] ?? "");
    $applicant_phone = trim($_POST["applicant_phone"] ?? "");
    $cover_letter = trim($_POST["cover_letter"] ?? "");

    if ($applicant_name === "" || $applicant_email === "" || $applicant_phone === "") {
        $error = "Please fill all required fields.";
    } else {
        /* Prevent duplicate application */
        $check = $conn->prepare("SELECT id FROM applications WHERE user_id = ? AND job_id = ?");
        $check->bind_param("ii", $_SESSION['user_id'], $job_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "You have already applied for this job.";
        } else {
            if (!isset($_FILES["resume"]) || $_FILES["resume"]["error"] !== 0) {
                $error = "Please upload your resume.";
            } else {
                $fileName = $_FILES["resume"]["name"];
                $fileTmp = $_FILES["resume"]["tmp_name"];
                $fileSize = $_FILES["resume"]["size"];
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                if ($fileExt !== "pdf") {
                    $error = "Only PDF files are allowed.";
                } elseif ($fileSize > 2 * 1024 * 1024) {
                    $error = "File size must be less than 2MB.";
                } else {
                    /* Ensure uploads folder exists */
                    $uploadDir = __DIR__ . "/uploads/";

                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $safeFileName = preg_replace("/[^a-zA-Z0-9._-]/", "_", $fileName);
                    $newFileName = time() . "_" . $safeFileName;

                    $fullUploadPath = $uploadDir . $newFileName;
                    $dbPath = "uploads/" . $newFileName;

                    if (move_uploaded_file($fileTmp, $fullUploadPath)) {
                        $stmt = $conn->prepare("
                            INSERT INTO applications
                            (job_id, user_id, applicant_name, applicant_email, applicant_phone, resume, cover_letter)
                            VALUES (?, ?, ?, ?, ?, ?, ?)
                        ");
                        $stmt->bind_param(
                            "iisssss",
                            $job_id,
                            $_SESSION['user_id'],
                            $applicant_name,
                            $applicant_email,
                            $applicant_phone,
                            $dbPath,
                            $cover_letter
                        );

                        if ($stmt->execute()) {
                            $success = "Application submitted successfully.";
                        } else {
                            $error = "Failed to save application.";
                        }

                        $stmt->close();
                    } else {
                        $error = "Failed to upload PDF.";
                    }
                }
            }
        }

        $check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Apply Job - JobPortal</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #0b0f19;
      color: white;
    }

    .apply-wrapper {
      min-height: 100vh;
      padding: 50px 20px;
    }

    .apply-card {
      max-width: 850px;
      margin: auto;
      background: #111827;
      border: 1px solid #1f2937;
      border-radius: 16px;
      padding: 30px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.35);
    }

    .apply-card h1 {
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

    .form-label {
      color: #f8fafc;
      font-weight: 600;
    }

    .form-control {
      background: #1e293b;
      color: white;
      border: 1px solid #334155;
    }

    .form-control:focus {
      background: #1e293b;
      color: white;
      border-color: #60a5fa;
      box-shadow: none;
    }

    .apply-btn-main {
      width: 100%;
      background: linear-gradient(45deg, #2563eb, #60a5fa);
      border: none;
      padding: 12px;
      border-radius: 8px;
      font-weight: 600;
      color: white;
    }

    .apply-btn-main:hover {
      opacity: 0.95;
      color: white;
    }

    .back-link {
      display: inline-block;
      margin-bottom: 20px;
      text-decoration: none;
      color: #60a5fa;
      font-weight: 600;
    }

    .back-link:hover {
      color: #93c5fd;
    }
  </style>
</head>
<body>

<div class="apply-wrapper">
  <div class="apply-card">
    <a href="jobs.php" class="back-link">← Back to Jobs</a>

    <h1>Apply for Job</h1>
    <p class="mb-4 text-light">Fill the form below to apply for this position.</p>

    <div class="job-info">
      <h3><?php echo htmlspecialchars($job['title']); ?></h3>
      <p><strong>Company:</strong> <?php echo htmlspecialchars($job['company']); ?></p>
      <p><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
      <p><strong>Salary:</strong> <?php echo htmlspecialchars($job['salary']); ?></p>
      <p><strong>Job Type:</strong> <?php echo htmlspecialchars($job['job_type'] ?? 'Not specified'); ?></p>
      <p><strong>Category:</strong> <?php echo htmlspecialchars($job['category'] ?? 'General'); ?></p>
      <p><strong>Description:</strong> <?php echo htmlspecialchars($job['description']); ?></p>
    </div>

    <?php if ($success !== ""): ?>
      <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if ($error !== ""): ?>
      <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input type="text" class="form-control" value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>" >
      </div>

      <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input type="email" class="form-control" value="<?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>" >
      </div>

      <div class="mb-3">
        <label class="form-label">Phone Number</label>
        <input type="text" name="applicant_phone" class="form-control" placeholder="Enter your phone number" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Upload Resume (PDF only)</label>
        <input type="file" name="resume" class="form-control" accept=".pdf" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Cover Letter</label>
        <textarea name="cover_letter" rows="5" class="form-control" placeholder="Write a short cover letter (optional)"></textarea>
      </div>

      <button type="submit" class="btn apply-btn-main">Submit Application</button>
    </form>
  </div>
</div>

</body>
</html>
