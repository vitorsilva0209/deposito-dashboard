<?php
session_start();

if (isset($_SESSION["id"])) {
    header("Location: dashboard.php");
    exit();
}

include("config/conexao.php");

$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $usuario = trim($_POST["usuario"] ?? "");
    $senha = trim($_POST["senha"] ?? "");

    if ($usuario === "" || $senha === "") {

        $erro = "Preencha todos os campos!";

    } else {

        $stmt = $conn->prepare(
            "SELECT id, nome, senha FROM usuarios WHERE usuario = ?"
        );

        if (!$stmt) {
            $erro = "Erro ao preparar a consulta: " . $conn->error;
        } else {

            $stmt->bind_param("s", $usuario);
            $stmt->execute();

            $resultado = $stmt->get_result();

            if ($resultado->num_rows > 0) {

                $user = $resultado->fetch_assoc();

                if ($senha === $user["senha"]) {

                    $_SESSION["id"] = $user["id"];
                    $_SESSION["nome"] = $user["nome"];

                    header("Location: dashboard.php");
                    exit();

                } else {

                    $erro = "Senha incorreta!";
                }

            } else {

                $erro = "Usuário não encontrado!";
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Depósito Brasil</title>

    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', sans-serif;
    }

    body {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background: #2b2b2b;
    }

    .login-card {
        background: #ffffff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
        width: 100%;
        max-width: 380px;
    }

    .login-card h2 {
        margin-bottom: 20px;
        text-align: center;
        color: #333333;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
        color: #333333;
    }

    .form-group input {
        width: 100%;
        padding: 10px;
        border: 2px solid #d32f2f;
        border-radius: 4px;
        outline: none;
    }

    .form-group input:focus {
        border-color: #a00000;
        box-shadow: 0 0 5px rgba(211, 47, 47, 0.4);
    }

    .btn-login {
        width: 100%;
        padding: 10px;
        background: #d32f2f;
        color: #ffffff;
        border: none;
        border-radius: 4px;
        font-weight: bold;
        cursor: pointer;
    }

    .btn-login:hover {
        background: #a00000;
    }

    .alert {
        background: #f8d7da;
        color: #721c24;
        padding: 10px;
        border-radius: 4px;
        margin-bottom: 15px;
        text-align: center;
    }
</style>
</head>

<body>

    <div class="login-card">

        <h2>Entrar no Sistema</h2>

        <?php if ($erro !== ""): ?>

            <div class="alert">
                <?php echo htmlspecialchars($erro); ?>
            </div>

        <?php endif; ?>

        <form method="POST" action="login.php">

            <div class="form-group">

                <label for="usuario">
                    Usuário
                </label>

                <input
                    type="text"
                    id="usuario"
                    name="usuario"
                    required
                >

            </div>

            <div class="form-group">

                <label for="senha">
                    Senha
                </label>

                <input
                    type="password"
                    id="senha"
                    name="senha"
                    required
                >

            </div>

            <button
                type="submit"
                name="entrar"
                class="btn-login"
            >
                Entrar
            </button>

        </form>

    </div>

</body>

</html>