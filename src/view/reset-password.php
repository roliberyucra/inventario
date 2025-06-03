<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cambiar contraseña</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      font-family: 'Arial', sans-serif;
      background: linear-gradient(135deg, #6e45e2, #88d3ce);
      color: #fff;
    }

    .login-container {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      padding: 40px 30px;
      border-radius: 15px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
      text-align: center;
      width: 300px;
    }

    .login-container h1 {
      font-size: 2rem;
      margin-bottom: 20px;
      color: #fff;
    }

    .login-container input {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      border: none;
      border-radius: 5px;
      outline: none;
      font-size: 1rem;
    }

    .login-container input[type="email"],
    .login-container input[type="password"] {
      background: rgba(255, 255, 255, 0.8);
      color: #333;
    }

    .login-container input::placeholder {
      color: #888;
    }

    .login-container button {
      width: 100%;
      padding: 10px;
      margin-top: 20px;
      background: #6e45e2;
      border: none;
      border-radius: 5px;
      color: #fff;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .login-container button:hover {
      background: #88d3ce;
    }

    .login-container a {
      display: block;
      margin-top: 15px;
      color: #fff;
      text-decoration: none;
      font-size: 0.9rem;
    }

    .login-container a:hover {
      text-decoration: underline;
    }
  </style>
  <!-- Sweet Alerts css -->
  <link href="<?php echo BASE_URL ?>src/view/pp/plugins/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
  <script>
    const base_url = '<?php echo BASE_URL; ?>';
    const base_url_server = '<?php echo BASE_URL_SERVER; ?>';
  </script>
</head>

<body>
    <input type="hidden" id="data" value="<?php echo $_GET['data'] ?>">
    <input type="hidden" id="data2" value="<?php echo $_GET['data2'] ?>">
  <div class="login-container">
    <h1>Cambiar contraseña</h1>
    <img src="https://sispa.iestphuanta.edu.pe/img/logo.png" alt="" width="100%">
    <form id="frm_reset_password">
      <input type="text" name="password" id="password" placeholder="Ingrese su nueva contraseña" required>
      <input type="password" name="newpassword" id="newpassword" placeholder="Confirme su contraseña" required>
      <button type="submit">Actualizar contraseña</button>
    </form>
  </div>
</body>
<script src="<?php echo BASE_URL; ?>src/view/js/principal.js"></script>
<!-- Sweet Alerts Js-->
<script src="<?php echo BASE_URL ?>src/view/pp/plugins/sweetalert2/sweetalert2.min.js"></script>

</html>