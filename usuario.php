<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuariot</title>
</head>

<body>
    <h1>Cadastro de Usuário</h1>
    <form method="post">

        <label for="nome">Nome:</label><br>
        <input type="text" id="nome" name="nome" required><br><br>

        <label for="cpf">CPF:</label><br>
        <input type="text" id="cpf" name="cpf" required placeholder="000.000.000-00"><br><br>

        <label for="telefone">Telefone:</label><br>
        <input type="text" id="telefone" name="telefone" required><br><br>

        <label for="endereco">Endereço:</label><br>
        <input type="text" id="endereco" name="endereco" required><br><br>

        <label for="cidade">Cidade:</label><br>
        <input type="text" id="cidade" name="cidade" required><br><br>

        <label for="email">E-mail:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <input type="submit" value="Cadastrar">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nome = $_POST['nome'];
        $cpf = $_POST['cpf'];
        $telefone = $_POST['telefone'];
        $endereco = $_POST['endereco'];
        $cidade = $_POST['cidade'];
        $email = $_POST['email'];




        $servername = "localhost";
        $database = "biblioteca";
        $username = "root";
        $password = "";

        $conn = mysqli_connect($servername, $username, $password, $database);

        if (!$conn) {
            echo "<div class='mensagem erro'>Falha na conexão: " . mysqli_connect_error() . "</div>";
            die();
        }


        $sql = "INSERT INTO usuario (
             nome, 
            cpf, 
            telefone, 
            endereco, 
            cidade, 
            email
        ) VALUES (
            '$nome', 
            '$cpf', 
            '$telefone', 
            '$endereco', 
            '$cidade', 
            '$email')";

        if (mysqli_query($conn, $sql)) {
            echo "<br>Comando executado com sucesso";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }


        mysqli_close($conn);
    }
    ?>
</body>

</html>