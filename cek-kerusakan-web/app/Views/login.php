<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pelindo BJTI</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #164ec0;
            --secondary-blue: #0d3a9c;
            --accent-yellow: #f4a51c;
            --accent-red: #f25a5a;
            --light-gray: #f5f7fa;
            --white: #ffffff;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }

        .background-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        .shape-1 {
            position: absolute;
            width: 600px;
            height: 500px;
            background: rgba(255, 255, 255, 0.05);
            top: -150px;
            left: -100px;
            transform: rotate(-15deg);
            border-radius: 30px;
        }

        .shape-2 {
            position: absolute;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.03);
            bottom: -100px;
            right: -100px;
            transform: rotate(15deg);
            border-radius: 30px;
        }

        .login-container {
            position: relative;
            z-index: 1;
            background: var(--white);
            border-radius: 20px;
            box-shadow: var(--shadow);
            width: 90%;
            max-width: 420px;
            padding: 40px;
            text-align: center;
            animation: fadeInUp 0.6s ease-out;
        }

        .logo {
            margin-bottom: 30px;
        }

        .logo img {
            max-width: 220px;
            height: auto;
        }

        .welcome-text {
            margin-bottom: 25px;
            text-align: center;
        }

        .welcome-text h1 {
            color: var(--primary-blue);
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .welcome-text p {
            color: #666;
            font-size: 14px;
        }

        form {
            margin-top: 20px;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
        }

        input {
            width: 100%;
            padding: 14px 20px 14px 45px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
            background-color: var(--light-gray);
        }

        input:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(22, 78, 192, 0.1);
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #777;
            font-size: 18px;
        }

        button {
            width: 100%;
            padding: 14px;
            background: var(--primary-blue);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        button:hover {
            background: var(--secondary-blue);
            transform: translateY(-2px);
        }

        .forgot {
            display: block;
            margin-top: 20px;
            color: #777;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
        }

        .forgot:hover {
            color: var(--primary-blue);
        }

        .error-message {
            color: var(--accent-red);
            font-size: 14px;
            margin-bottom: 15px;
            padding: 10px;
            background-color: rgba(242, 90, 90, 0.1);
            border-radius: 5px;
        }

        .footer-text {
            margin-top: 30px;
            color: #999;
            font-size: 12px;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }

            .logo img {
                max-width: 180px;
            }
        }
    </style>
</head>

<body>
    <div class="background-shapes">
        <div class="shape-1"></div>
        <div class="shape-2"></div>
    </div>

    <div class="login-container">
        <div class="logo">
            <img src="<?= base_url(relativePath: '/assets/logonew.png') ?>" alt="PELINDO BJTI Logo">
        </div>

        <div class="welcome-text">
            <h1>Welcome Back</h1>
            <p>Please login to access your account</p>
        </div>

        <?php if (session()->getFlashdata('error')) : ?>
            <p class="error-message"><?= session()->getFlashdata('error') ?></p>
        <?php endif; ?>

        <form action="<?= base_url('/login') ?>" method="post">
            <div class="input-group">
                <i class="input-icon">👤</i>
                <input type="text" name="username" placeholder="Username" required>
            </div>

            <div class="input-group">
                <i class="input-icon">🔒</i>
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit">LOGIN</button>
        </form>

        <a href="#" class="forgot">Forgot password?</a>

        <div class="footer-text">
            &copy; <?= date('Y') ?> Pelindo BJTI. All rights reserved.
        </div>
    </div>
</body>

<?php if (session()->getFlashdata('error')): ?>
    <script>
        setTimeout(() => {
            alert("<?= session()->getFlashdata('error'); ?>");
        }, 300);
    </script>
<?php endif; ?>

</html>