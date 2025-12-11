<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devolução de Obra</title>
    <link rel="stylesheet" href="./_css/style.css"> 
    <link rel="stylesheet" href="./_css/nav.css">
</head>

<body>
      <nav class="navbar">
        <div class="navbar-container">
            <a href="menu.html" class="navbar-logo">Biblioteca</a>
            <ul class="navbar-menu" id="navbarMenu">
                <li class="navbar-item">
                    <a href="usuario.php" class="navbar-link ">Usuários</a>
                </li>
                <li class="navbar-item">
                    <a href="obra.php" class="navbar-link ">Obras</a>
                </li>
                <li class="navbar-item">
                    <a href="emprestimo.php" class="navbar-link ">Empréstimos</a>
                </li>
                <li class="navbar-item">
                    <a href="devolucao.php" class="navbar-link active">Devoluções</a>
                </li>
                <li class="navbar-item">
                    <a href="vereficacao.php" class="navbar-link ">Relatórios</a>
                </li>
            </ul>
        </div>
    </nav>
    <h1>Registro de Devolução</h1>

    <form method="post">
        <label>Selecione a Obra para Devolução:</label>
        <select name="obra_a_devolver" id="obra_a_devolver" required>
            <option value="">Selecione uma Obra (emprestada)</option>
            <?php
            // Inclui o arquivo de conexão para popular o SELECT
            include 'conexao.php'; 
            
            // Configurações do banco (mantidas para exemplo)
            $servername = "localhost";
            $database = "biblioteca";
            $username = "root";
            $password = "";

            $conn = mysqli_connect($servername, $username, $password, $database);
            
            if (isset($conn)) {
                // Consulta: Seleciona as obras que estão atualmente emprestadas
                // Agora, busca o ID DA TRANSAÇÃO (idemprestimo) e o usa como valor
                $sqlObrasEmprestadas = "
                    SELECT 
                        t1.idemprestimo, /* NOVO: ID da Transação de Empréstimo Ativa */
                        t1.obra_idobra, 
                        o.nome_obra
                    FROM emprestimo t1
                    INNER JOIN (
                        SELECT obra_idobra, MAX(idemprestimo) AS max_id
                        FROM emprestimo
                        GROUP BY obra_idobra
                    ) t2 ON t1.obra_idobra = t2.obra_idobra AND t1.idemprestimo = t2.max_id
                    JOIN obra o ON t1.obra_idobra = o.idobra
                    WHERE t1.emprestado_devolvido = 'emprestado'
                    ORDER BY o.nome_obra
                ";

                $resO = $conn->query($sqlObrasEmprestadas);
                
                if ($resO) {
                    while ($o = $resO->fetch_assoc()) {
                        $id = $o['idemprestimo']; /* USA o ID do Empréstimo como valor */
                        $nome = htmlspecialchars($o['nome_obra']);
                        echo "<option value='$id'>$nome</option>";
                    }
                }
                
                // Fechamento da conexão após o SELECT
                // mysqli_close($conn); 
            }
            ?>
        </select>
        <br><br>

        <label for="data_devolucao_efetiva">Data Efetiva da Devolução:</label>
        <input type="date" id="data_devolucao_efetiva" name="data_devolucao_efetiva" value="<?php echo date('Y-m-d'); ?>" required>
        <br><br>

        <input type="submit" value="Registrar Devolução">
    </form>

    <?php
    // Bloco de processamento do formulário (POST)
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['obra_a_devolver'])) {
        
        // 1. Obtenção e validação dos dados
        // Agora, 'obra_a_devolver' contém o ID da Transação (idemprestimo)
        $id_emprestimo_a_atualizar = $_POST['obra_a_devolver']; 
        $data_devolucao_efetiva = $_POST['data_devolucao_efetiva'];
        $status_devolucao = 'devolvido'; // Status fixo para devolução

        // 2. Reconnecta ao DB para o processamento do POST
        $conn = mysqli_connect($servername, $username, $password, $database);

        if (!$conn) {
            echo "<div class='mensagem erro'>Falha na conexão: " . mysqli_connect_error() . "</div>";
            die();
        }

        // 3. O UPDATE irá alterar o status do registro de empréstimo original
        $id_emprestimo_esc = mysqli_real_escape_string($conn, $id_emprestimo_a_atualizar);
        $data_efetiva_esc = mysqli_real_escape_string($conn, $data_devolucao_efetiva);
        $status_esc = mysqli_real_escape_string($conn, $status_devolucao);

        $sqlUpdateDevolucao = "
            UPDATE emprestimo 
            SET 
                emprestado_devolvido = '$status_esc',
                data_devolucao_efetiva = '$data_efetiva_esc' /* NOVO CAMPO REQUERIDO */
            WHERE idemprestimo = '$id_emprestimo_esc'
        ";

        if (mysqli_query($conn, $sqlUpdateDevolucao)) {
            echo "<div class='mensagem sucesso'> Devolução registrada e Transação ID $id_emprestimo_a_atualizar atualizada com sucesso!</div>";
        } else {
            echo "<div class='mensagem erro'> Erro ao registrar devolução: " . mysqli_error($conn) . "</div>";
        }
        
        mysqli_close($conn);
    }
    ?>
</body>

</html>