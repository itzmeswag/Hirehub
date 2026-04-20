<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>HireHub</title>
  <link rel="stylesheet" href="style.css" />
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
  />
</head>
<body>

  <header>
    <nav class="custom-navbar sticky-top">
      <div class="logo">HireHub</div>

      <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="jobs.php">Apply</a></li>
        <li><a href="post_job.php">Post</a></li>
        <li><a href="about.html">About</a></li>
        <li><a href="contact.html">Contact</a></li>
      </ul>

      <div class="nav1-buttons">
      
        <?php if (isset($_SESSION['user_id']) && isset($_SESSION['user_name'])): ?>
          <span style="color:#fff; font-weight:600; margin-right:10px;">
            Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
          </span>
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'company'): ?>
  <li><a href="company_dashboard.php"  class="btn custom_signup-btn">Dashboard</a></li>
<?php elseif (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'user'): ?>
  <li><a href="user_dashboard.php"  class="btn custom_signup-btn">Dashboard</a></li>
<?php endif; ?>
          <a href="logout.php" class="btn custom_signup-btn">Logout</a>
        <?php else: ?>
          <a href="#" class="btn custom_login-btn" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a>
          <a href="#" class="btn custom_signup-btn" data-bs-toggle="modal" data-bs-target="#signupModal">Sign Up</a>
        <?php endif; ?>
      </div>
    </nav>
  </header>
  
  <div id="carouselExample" class="carousel slide" data-bs-ride="carousel" data-bs-interval="2500">
    <div class="carousel-inner">
      <div class="carousel-item active">
        <img src="1.jpg" class="d-block w-100" alt="Slide 1">
      </div>
      <div class="carousel-item">
        <img src="2.jpg" class="d-block w-100" alt="Slide 2">
      </div>
      <div class="carousel-item">
        <img src="3.jpg" class="d-block w-100" alt="Slide 3">
      </div>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>

    <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
  </div>

  

  <section class="why-choose-us">
    <h2>Why Choose Us</h2>
    <div class="features">
      <div class="feature-box">
        <h3>Easy Apply</h3>
        <p>Apply for jobs quickly with a simple and user-friendly process.</p>
      </div>
      <div class="feature-box">
        <h3>Verified Companies</h3>
        <p>We list trusted and verified companies for better opportunities.</p>
      </div>
      <div class="feature-box">
        <h3>Fast Hiring</h3>
        <p>Get hired faster with direct application and quick updates.</p>
      </div>
    </div>
  </section>

  <section class="stats">
    <div class="stat-box">
      <h3>1000+</h3>
      <p>Jobs Posted</p>
    </div>
    <div class="stat-box">
      <h3>500+</h3>
      <p>Companies</p>
    </div>
    <div class="stat-box">
      <h3>10,000+</h3>
      <p>Job Seekers</p>
    </div>
    <div class="stat-box">
      <h3>2500+</h3>
      <p>Successful Hires</p>
    </div>
  </section>

  <section class="top-companies">
    <h2>Top Companies Hiring</h2>
    <div class="company-logos">
      <div class="company-box">Google</div>
      <div class="company-box">Amazon</div>
      <div class="company-box">Microsoft</div>
      <div class="company-box">Infosys</div>
      <div class="company-box">TCS</div>
      <div class="company-box">Wipro</div>
    </div>
  </section>

  <section class="cta">
    <h2>Start Your Career Journey With Us</h2>
    <p>Create your account and apply for the best jobs today.</p>
    <?php if (!isset($_SESSION['user_id'])): ?>
      <a href="#" class="cta-btn" data-bs-toggle="modal" data-bs-target="#signupModal">Get Started</a>
    <?php else: ?>
      <a href="jobs.php" class="cta-btn">Explore Jobs</a>
    <?php endif; ?>
  </section>

  <footer class="footer">
    <div class="footer-content">
      <div>
        <h3>HireHub</h3>
        <p>Your trusted platform to find jobs and hire talent.</p>
      </div>
      <div>
        <h4>Quick Links</h4>
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="jobs.php">Apply</a></li>
          <li><a href="post_job.php">Post</a></li>
          <li><a href="contact.html">Contact</a></li>
        </ul>
      </div>
      <div>
        <h4>Contact</h4>
        <p>Email: homesengupta107@gmail.com</p>
        // Developed by Swagata Sengupta
       
      </div>
    </div>
  </footer>

  <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content bg-dark text-white border-secondary">
        <div class="modal-header border-secondary">
          <h5 class="modal-title">Login</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div id="loginMessage" class="mb-3 text-center"></div>

          <form id="loginForm" method="POST">
            <div class="mb-3">
              <label class="form-label">Email Address</label>
              <input type="email" name="email" class="form-control bg-secondary-subtle" placeholder="Enter email" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control bg-secondary-subtle" placeholder="Enter password" required>
            </div>

            <button type="submit" class="btn btn-primary w-100" id="loginBtn">Login</button>
          </form>
        </div>

        <div class="modal-footer border-secondary justify-content-center">
          <p class="mb-0">
            Don’t have an account?
            <a href="#" class="text-info" data-bs-toggle="modal" data-bs-target="#signupModal" data-bs-dismiss="modal">Sign Up</a>
          </p>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="signupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content bg-dark text-white border-secondary">
        <div class="modal-header border-secondary">
          <h5 class="modal-title">Create Account</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div id="signupMessage" class="mb-3 text-center"></div>

          <form id="signupForm" method="POST">
            <div class="mb-3">
              <label class="form-label">Full Name</label>
              <input type="text" name="name" class="form-control bg-secondary-subtle" placeholder="Enter full name" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Email Address</label>
              <input type="email" name="email" class="form-control bg-secondary-subtle" placeholder="Enter email" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control bg-secondary-subtle" placeholder="Create password" required>
            </div>

            <div class="mb-3">
            <select name="role" class="form-control bg-secondary-subtle" required>
            <option value="user">Job Seeker</option>
            <option value="company">Company</option>
            </select>
            </div>

            <button type="submit" class="btn btn-primary w-100" id="signupBtn">Sign Up</button>
          </form>
        </div>

        <div class="modal-footer border-secondary justify-content-center">
          <p class="mb-0">
            Already have an account?
            <a href="#" class="text-info" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal">Login</a>
          </p>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content bg-dark text-white border-secondary">
        <div class="modal-header border-secondary">
          <h5 class="modal-title">Success</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center" id="successModalText">
          Success message
        </div>
        <div class="modal-footer border-secondary">
          <button class="btn btn-primary" data-bs-dismiss="modal">OK</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    const loginForm = document.getElementById("loginForm");
    const signupForm = document.getElementById("signupForm");

    const loginMessage = document.getElementById("loginMessage");
    const signupMessage = document.getElementById("signupMessage");

    const loginBtn = document.getElementById("loginBtn");
    const signupBtn = document.getElementById("signupBtn");

    const successModal = new bootstrap.Modal(document.getElementById("successModal"));
    const successModalText = document.getElementById("successModalText");

    loginForm.addEventListener("submit", function(e) {
      e.preventDefault();

      loginMessage.innerHTML = "";
      loginBtn.disabled = true;
      loginBtn.textContent = "Logging in...";

      const formData = new FormData(loginForm);

      fetch("login_ajax.php", {
        method: "POST",
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        loginBtn.disabled = false;
        loginBtn.textContent = "Login";

        if (data.status === "success") {
          const loginModalInstance = bootstrap.Modal.getInstance(document.getElementById("loginModal"));
          loginModalInstance.hide();

          successModalText.textContent = data.message;
          successModal.show();

          loginForm.reset();

          setTimeout(() => {
            window.location.reload();
          }, 1200);
        } else {
          loginMessage.innerHTML = `<span class="text-danger">${data.message}</span>`;
        }
      })
      .catch(() => {
        loginBtn.disabled = false;
        loginBtn.textContent = "Login";
        loginMessage.innerHTML = `<span class="text-danger">Something went wrong.</span>`;
      });
    });

    signupForm.addEventListener("submit", function(e) {
      e.preventDefault();

      signupMessage.innerHTML = "";
      signupBtn.disabled = true;
      signupBtn.textContent = "Signing up...";

      const formData = new FormData(signupForm);

      fetch("signup_ajax.php", {
        method: "POST",
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        signupBtn.disabled = false;
        signupBtn.textContent = "Sign Up";

        if (data.status === "success") {
          const signupModalInstance = bootstrap.Modal.getInstance(document.getElementById("signupModal"));
          signupModalInstance.hide();

          successModalText.textContent = data.message;
          successModal.show();

          signupForm.reset();
        } else {
          signupMessage.innerHTML = `<span class="text-danger">${data.message}</span>`;
        }
      })
      .catch(() => {
        signupBtn.disabled = false;
        signupBtn.textContent = "Sign Up";
        signupMessage.innerHTML = `<span class="text-danger">Something went wrong.</span>`;
      });
    });
  </script>
  <script>
document.addEventListener("DOMContentLoaded", function () {
  const loginForm = document.getElementById("loginForm");
  const signupForm = document.getElementById("signupForm");

  const loginMessage = document.getElementById("loginMessage");
  const signupMessage = document.getElementById("signupMessage");

  const loginBtn = document.getElementById("loginBtn");
  const signupBtn = document.getElementById("signupBtn");

  const successModalEl = document.getElementById("successModal");
  const successModal = new bootstrap.Modal(successModalEl);
  const successModalText = document.getElementById("successModalText");

  loginForm.addEventListener("submit", function (e) {
    e.preventDefault();

    loginMessage.innerHTML = "";
    loginBtn.disabled = true;
    loginBtn.textContent = "Logging in...";

    const formData = new FormData(loginForm);

    fetch("login_ajax.php", {
      method: "POST",
      body: formData
    })
    .then(async response => {
      const text = await response.text();
      try {
        return JSON.parse(text);
      } catch (err) {
        throw new Error("Invalid JSON from login_ajax.php: " + text);
      }
    })
    .then(data => {
      loginBtn.disabled = false;
      loginBtn.textContent = "Login";

      if (data.status === "success") {
        bootstrap.Modal.getInstance(document.getElementById("loginModal")).hide();
        successModalText.textContent = data.message;
        successModal.show();
        loginForm.reset();

        setTimeout(() => {
          window.location.reload();
        }, 1200);
      } else {
        loginMessage.innerHTML = `<span class="text-danger">${data.message}</span>`;
      }
    })
    .catch(error => {
      loginBtn.disabled = false;
      loginBtn.textContent = "Login";
      loginMessage.innerHTML = `<span class="text-danger">${error.message}</span>`;
      console.error(error);
    });
  });

  signupForm.addEventListener("submit", function (e) {
    e.preventDefault();

    signupMessage.innerHTML = "";
    signupBtn.disabled = true;
    signupBtn.textContent = "Signing up...";

    const formData = new FormData(signupForm);

    fetch("signup_ajax.php", {
      method: "POST",
      body: formData
    })
    .then(async response => {
      const text = await response.text();
      try {
        return JSON.parse(text);
      } catch (err) {
        throw new Error("Invalid JSON from signup_ajax.php: " + text);
      }
    })
    .then(data => {
      signupBtn.disabled = false;
      signupBtn.textContent = "Sign Up";

      if (data.status === "success") {
        bootstrap.Modal.getInstance(document.getElementById("signupModal")).hide();
        successModalText.textContent = data.message;
        successModal.show();
        signupForm.reset();
      } else {
        signupMessage.innerHTML = `<span class="text-danger">${data.message}</span>`;
      }
    })
    .catch(error => {
      signupBtn.disabled = false;
      signupBtn.textContent = "Sign Up";
      signupMessage.innerHTML = `<span class="text-danger">${error.message}</span>`;
      console.error(error);
    });
  });
});
</script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php if (isset($_GET['login_required'])): ?>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const loginModal = new bootstrap.Modal(document.getElementById("loginModal"));
    loginModal.show();
  });
</script>
<?php endif; ?>
</body>
</html>