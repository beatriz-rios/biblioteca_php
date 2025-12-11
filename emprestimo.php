<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empréstimo</title>
    <link rel="stylesheet" href="./_css/style.css">
    <link rel="stylesheet" href="./_css/nav.css">
    
</head>

<body>
       <nav class="navbar">
        <div class="navbar-container">
            <a href="menu.html" class="navbar-logo">Biblioteca</a>
            <button class="navbar-toggle" id="navbarToggle"></button>
            <ul class="navbar-menu" id="navbarMenu">
                <li class="navbar-item">
                    <a href="usuario.php" class="navbar-link ">Usuários</a>
                </li>
                <li class="navbar-item">
                    <a href="obra.php" class="navbar-link ">Obras</a>
                </li>
                <li class="navbar-item">
                    <a href="emprestimo.php" class="navbar-link active">Empréstimos</a>
                </li>
                <li class="navbar-item">
                    <a href="devolucao.php" class="navbar-link ">Devoluções</a>
                </li>
                <li class="navbar-item">
                    <a href="vereficacao.php" class="navbar-link ">Relatórios</a>
                </li>
            </ul>
        </div>
    </nav>
    <h1>Cadastro de Empréstimo</h1>
    <form method="post">
        <label>Nome do Usario</label>
        <select name="usuarios" id="usuarios" required>
            <option value="">Selecione um Usuário</option>
            <?php
            // Incluindo a conexão para popular o SELECT de Usuários
            include 'conexao.php';
            // Assumindo que $conn está definida em 'conexao.php'
            if (isset($conn)) {
                $sqlUsuarios = "SELECT idusuario, nome FROM usuario ORDER BY nome";
                $resU = $conn->query($sqlUsuarios);
                if ($resU) {
                    while ($u = $resU->fetch_assoc()) {
                        $id = $u['idusuario'];
                        $nome = htmlspecialchars($u['nome']);
                        echo "<option value='$id'>$nome</option>";
                    }
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
            if (isset($conn)) {
                $sqlObras = "SELECT idobra, nome_obra FROM obra ORDER BY nome_obra";
                $resO = $conn->query($sqlObras);
                if ($resO) {
                    while ($o = $resO->fetch_assoc()) {
                        $id = $o['idobra'];
                        $nome = htmlspecialchars($o['nome_obra']);
                        echo "<option value='$id'>$nome</option>";
                    }
                }
            }
            ?>
        </select>
        <br><br>

        <label for="data_emprestimo">Data do Empréstimo:</label><br>
        <input type="date" id="data_emprestimo" name="data_emprestimo" required><br><br>

        <label for="tempo_emprestimo">Prazo do Empréstimo (dias):</label><br>
        <input type="number" id="tempo_emprestimo" name="tempo_emprestimo" required><br><br>

        <label for="devolucao">Data Prevista de Devolução:</label><br>
        <input type="date" id="devolucao" name="devolucao" required><br><br>

        <input type="submit" value="Cadastrar Empréstimo">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $id_usuario = $_POST['usuarios'];
        $obra_idobra = $_POST['obras'];
        $data_emprestimo = $_POST['data_emprestimo'];
        $tempo_emprestimo = $_POST['tempo_emprestimo'];
        $devolucao = $_POST['devolucao'];
        // Para um novo empréstimo, o status é 'emprestado', não lido do POST.
        $emprestado_devolvido = 'emprestado'; 


        $servername = "localhost";
        $database = "biblioteca";
        $username = "root";
        $password = "";

        $conn = mysqli_connect($servername, $username, $password, $database);

        if (!$conn) {
            echo "<div class='mensagem erro'>Falha na conexão: " . mysqli_connect_error() . "</div>";
            die();
        }

        // ----------------------------------------------------
        // --- INÍCIO DA LÓGICA DE VERIFICAÇÃO DE DISPONIBILIDADE ---
        // ----------------------------------------------------

        // 1. Protege a variável para a query de verificação
        $obra_idobra_esc = mysqli_real_escape_string($conn, $obra_idobra);

        // SQL para buscar o status do último empréstimo da obra
        $sql_check = "SELECT emprestado_devolvido
                      FROM emprestimo
                      WHERE obra_idobra = '$obra_idobra_esc'
                      ORDER BY idemprestimo DESC 
                      LIMIT 1"; // Assume 'idemprestimo' é a chave primária auto-incremento

        $res_check = mysqli_query($conn, $sql_check);
        $is_available = true; // Assume disponível por padrão (se não houver empréstimos anteriores)

        if ($res_check) {
            if (mysqli_num_rows($res_check) > 0) {
                $last_loan = mysqli_fetch_assoc($res_check);
                // 2. Se o último status não for 'devolvido', a obra não está disponível.
                if (strtolower($last_loan['emprestado_devolvido']) != 'devolvido') {
                    $is_available = false;
                }
            }
        } else {
            // Em caso de erro na consulta, exibe uma mensagem e encerra
            echo "<div class='mensagem erro'>Erro ao verificar disponibilidade: " . mysqli_error($conn) . "</div>";
            mysqli_close($conn);
            die();
        }

        if (!$is_available) {
            // 3. Obra indisponível: Exibe o prompt e a mensagem de erro
            echo "<script>alert('A obra não pode ser emprestada! O status atual não é \"devolvido\".');</script>";
            echo "<div class='mensagem erro'>Empréstimo não permitido: A obra já está emprestada ou o último empréstimo não foi devolvido.</div>";
        } else {
            // 4. Obra disponível: Procede com a inserção do empréstimo

            // Protege as variáveis para o INSERT (boa prática de segurança)
            $id_usuario_esc = mysqli_real_escape_string($conn, $id_usuario);
            $data_emprestimo_esc = mysqli_real_escape_string($conn, $data_emprestimo);
            $tempo_emprestimo_esc = mysqli_real_escape_string($conn, $tempo_emprestimo);
            $devolucao_esc = mysqli_real_escape_string($conn, $devolucao);
            $emprestado_devolvido_esc = mysqli_real_escape_string($conn, $emprestado_devolvido); // 'emprestado'

            $sql = "INSERT INTO emprestimo (
                id_usuario,
                obra_idobra,
                data_emprestimo,
                tempo_emprestimo,
                devolucao,
                emprestado_devolvido
            ) VALUES(
                '$id_usuario_esc',
                '$obra_idobra_esc',
                '$data_emprestimo_esc',
                '$tempo_emprestimo_esc',
                '$devolucao_esc',
                '$emprestado_devolvido_esc')";

            if (mysqli_query($conn, $sql)) {
                echo "<div class='mensagem sucesso'>✅ Devolução registrada com sucesso! A obra agora está disponível.'.";
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        }

        mysqli_close($conn);
    }
    ?>
</body>

</html>