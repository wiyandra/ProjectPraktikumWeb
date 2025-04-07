<?php
// Start session if needed
session_start();

// Check if the success parameter exists in the URL
if (isset($_GET['success'])) {
    $message = htmlspecialchars($_GET['success']);
} else {
    $message = "Terjadi kesalahan, coba lagi.";
}

// Check if the 'nama' parameter exists in the URL (newly registered user)
if (isset($_GET['nama'])) {
    $userName = htmlspecialchars($_GET['nama']);
    $welcomeMessage = "Selamat datang, $userName!";
} else {
    $welcomeMessage = "Selamat datang!";
}

// Set countdown time in seconds (e.g., 10 seconds)
$countdownTime = 10;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang - WSA TOPUP</title>
    <meta http-equiv="refresh" content="<?php echo $countdownTime; ?>;url=login.html">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        /* Reset Default */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        /* Body Styling */
        body {
            background-color: #000;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 20px; /* Padding added to body for spacing */
        }

        /* Container Styling */
        .register-container {
            text-align: center;
            width: 100%;
            max-width: 500px;
            min-width: 300px;
            max-height: 1000px;
            min-width: 300px;
            padding: 20px; /* Padding added to container */
        }

        .register-card {
            background-color: #222;
            padding: 30px;
            border-radius: 10px;
            width: auto;
            height: auto;
        }

        /* Logo & Title */
        .logo-text img {
            width: 80px;
            margin-bottom: 10px;
        }

        .text-beside-logo h6,
        p {
            text-align: left;
            color: #fff;
            font-size: 12px;
        }

        .register-title {
            color: #fff;
            font-size: 20px;
            margin-bottom: 15px;
            text-align: left;
        }

        .register-title h3 {
            text-align: left;
            margin-bottom: 5px;
        }

        .register-title p {
            text-align: left;
            color: #ffffff;
            margin-bottom: 30px;
        }

        /* Countdown Styling */
        .countdown-container {
            text-align: center;
            background-color: #444;
            padding: 20px;
            border-radius: 10px;
            color: #fff;
            margin-top: 20px; /* Padding added for spacing */
        }

        .countdown {
            font-size: 30px;
            font-weight: bold;
            color: #fff;
        }

        .redirect-message {
            font-size: 14px;
            color: #aaa;
        }

        .countdown-message {
            font-size: 18px;
        }

        .btn-login {
            display: block;
            width: 100%;
            padding: 10px;
            color: #ffffff;
            border: 1px solid #fff;
            border-radius: 5px;
            margin-top: 10px;
            text-decoration: none;
            background: #222;
            transition: background 0.3s;
        }

        .btn-login:hover {
            background: #444;
        }

        /* Error message styling */
        .error-message {
            color: red;
            padding-top: 20px; /* Menambahkan padding atas */
            font-size: 16px;
        }
    </style>
</head>

<body>
    <div class="register-container">
        <div class="register-card">
            <h1><?php echo $welcomeMessage; ?></h1> <!-- Tampilkan pesan selamat datang dengan nama -->
            <p class="error-message"><?php echo $message; ?></p> <!-- Pesan error atau sukses -->
            <div class="countdown-container">
                <p class="countdown-message">Anda akan diarahkan ke halaman login dalam:</p>
                <p class="countdown" id="countdown"><?php echo $countdownTime; ?> detik</p>
                <p class="redirect-message">Jika tidak diarahkan secara otomatis, <a href="login.html" class="btn-login">Klik di sini</a></p>
            </div>
        </div>
    </div>

    <script>
        // Countdown logic
        var countdownTime = <?php echo $countdownTime; ?>;
        var countdownElement = document.getElementById("countdown");

        var countdownInterval = setInterval(function () {
            countdownTime--;
            countdownElement.innerHTML = countdownTime + " detik";

            if (countdownTime <= 0) {
                clearInterval(countdownInterval);
                window.location.href = "login.html"; // Redirect when countdown ends
            }
        }, 1000);
    </script>
</body>

</html>
