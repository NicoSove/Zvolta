<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>About Us - ZVOLTA</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f7f9fc;
      color: #2c3e50;
    }

    header {
      background-color: #1a1a2e;
      padding: 20px;
      color: white;
      text-align: center;
    }

    header h1 {
      margin: 0;
      font-size: 2.5em;
    }

    .container {
      padding: 40px 20px;
      max-width: 1000px;
      margin: auto;
    }

    section {
      margin-bottom: 40px;
    }

    section h2 {
      color: #1a1a2e;
      border-left: 5px solid #0f3460;
      padding-left: 10px;
      margin-bottom: 10px;
    }

    section p {
      line-height: 1.6;
    }

    .team {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }

    .team-member {
      background: white;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      border-radius: 10px;
      padding: 20px;
      flex: 1 1 200px;
      text-align: center;
    }

    .team-member h4 {
      margin: 10px 0 5px;
    }

    footer {
      background-color: #0f3460;
      color: white;
      text-align: center;
      padding: 20px;
    }

    @media (max-width: 600px) {
      .team {
        flex-direction: column;
      }
    }
  </style>
</head>
<body>

<header>
  <h1>ZVOLTA</h1>
  <p>Protecting your digital world</p>
</header>

<div class="container">
  <section>
    <h2>Our Mission</h2>
    <p>
      At ZVOLTA, our mission is to empower businesses and individuals with cutting-edge cybersecurity solutions.
      We strive to stay ahead of evolving threats and ensure digital safety across all platforms.
    </p>
  </section>

  <section>
    <h2>What We Do</h2>
    <p>
      From threat detection to network protection, our services include penetration testing, vulnerability assessments,
      data encryption solutions, and 24/7 security monitoring.
    </p>
  </section>

  <section>
    <h2>Meet Our Team</h2>
    <div class="team">
      <div class="team-member">
        <h4>Alice Johnson</h4>
        <p>CEO & Founder</p>
      </div>
      <div class="team-member">
        <h4>Mike Chen</h4>
        <p>Chief Security Officer</p>
      </div>
      <div class="team-member">
        <h4>Sara Lee</h4>
        <p>Lead Ethical Hacker</p>
      </div>
    </div>
  </section>

  <section>
    <h2>Contact Us</h2>
    <p>
      Want to work with us? Reach out at <strong>contact@zvolta.com</strong> or call <strong>+1 800-123-4567</strong>.
    </p>
  </section>
</div>

<footer>
  &copy; 2025 ZVOLTA. All rights reserved.
</footer>

</body>
</html>
