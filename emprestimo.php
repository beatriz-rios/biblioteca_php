<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empréstimo</title>
</head>

<body>
    <h1>Cadastro de Empréstimo</h1>
    <form method="post">
<label>Nome do Usario</label>
      <select name="usuarios" id="usuarios" required>
                <option value="">Selecione um Usuário</option>
                <?php
                include 'conexao.php';
                $sqlUsuarios = "SELECT idusuario, nome FROM usuario ORDER BY nome";
                $resU = $conn->query($sqlUsuarios);
                if ($resU) {
                    while ($u = $resU->fetch_assoc()) {
                        $id = $u['idusuario'];
                        $nome = htmlspecialchars($u['nome']);
                        echo "<option value='$id'>$nome</option>";
                    }
                }
                ?>
                </select>
<br><br>
<label>Nome da Obra</label>
        <select name="obras" id="obras" required>
                <option value="">Selecione uma Obra</option>
                <?php
                include 'conexao.php';
                $sqlUsuarios = "SELECT idobra, nome_obra FROM obra ORDER BY nome_obra";
                $resU = $conn->query($sqlUsuarios);
                if ($resU) {
                    while ($u = $resU->fetch_assoc()) {
                        $id = $u['idobra'];
                        $nome = htmlspecialchars($u['nome_obra']);
                        echo "<option value='$id'>$nome</option>";
                    }
                }
                ?>
        </select>
        <br><br>

        <label for="data_emprestimo">Data Empréstimo:</label><br>
        <input type="date" name="data_emprestimo" id="data_emprestimo" required>
        <br><br>

        <label for="tempo_emprestimo">Prazo (dias):</label>
        <input type="number" name="tempo_emprestimo" id="tempo_emprestimo" required>
        <br><br>

        <label for="devolucao">Data Devolução:</label>
        <input type="date" name="devolucao" id="devolucao">
        <br><br>

        <label for="emprestado_devolvido">Status:</label>
        <select name="emprestado_devolvido" id="emprestado_devolvido" required>
            <option value="emprestado">Emprestado</option>
            <option value="devolvido">Devolvido</option>
        </select><br><br>

        <input type="submit" value="Cadastrar">
    </form>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {



        $id_usuario = $_POST['id_usuario'];
        $obra_idobra = $_POST['obra_idobra'];
        $data_emprestimo = $_POST['data_emprestimo'];
        $tempo_emprestimo = $_POST['tempo_emprestimo'];
        $devolucao = $_POST['devolucao'];
        $emprestado_devolvido = $_POST['emprestado_devolvido'];





        $servername = "localhost";
        $database = "biblioteca";
        $username = "root";
        $password = "";

        $conn = mysqli_connect($servername, $username, $password, $database);

        if (!$conn) {
            echo "<div class='mensagem erro'>Falha na conexão: " . mysqli_connect_error() . "</div>";
            die();
        }



        $sql = "INSERT INTO emprestimo (
            id_usuario,
            obra_idobra,
            data_emprestimo,
            tempo_emprestimo,
            devolucao,
            emprestado_devolvido
        ) VALUES(
            '$id_usuario',
            '$obra_idobra',
            '$data_emprestimo',
            '$tempo_emprestimo',
            '$devolucao',
            '$emprestado_devolvido'
        )";



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