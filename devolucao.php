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
            include 'conexao.php'; // Inclui o arquivo que conecta com o Banco de Dados
            
            if (isset($conn)) { // Verifica se a conexão com o banco ($conn) foi estabelecida com sucesso antes de tentar a consulta.
            
                // Início da montagem da variável string que contém o comando SQL
                $sqlObrasEmprestadas = "
        SELECT 
            t1.idemprestimo,   -- Seleciona o ID da transação (necessário para o UPDATE depois)
            t1.obra_idobra,    -- Seleciona o ID do livro
            o.nome_obra        -- Seleciona o nome do livro (vindo da tabela 'obra')
            
        FROM emprestimo t1     -- Define a tabela 'emprestimo' como principal e dá o apelido de 't1'
        
        -- INÍCIO DA SUBQUERY (A parte mais inteligente)
        INNER JOIN (
            SELECT 
                obra_idobra, 
                MAX(idemprestimo) AS max_id -- Agrupa por obra e pega apenas o MAIOR ID (o empréstimo mais recente)
            FROM emprestimo
            GROUP BY obra_idobra            -- Garante que cada livro apareça apenas uma vez nesta lista temporária
        ) t2 
        -- FIM DA SUBQUERY
        
        -- O ON abaixo faz o 'filtro': ele diz que t1 só deve trazer os dados se o ID for o maior encontrado em t2
        ON t1.obra_idobra = t2.obra_idobra AND t1.idemprestimo = t2.max_id
        
        -- Faz a junção com a tabela 'obra' para conseguirmos ler o NOME do livro, não apenas o número do ID
        JOIN obra o ON t1.obra_idobra = o.idobra
        
        -- Filtra para exibir apenas o que ainda não foi devolvido pelo usuário
        WHERE t1.emprestado_devolvido = 'emprestado'
        
        -- Organiza a lista do SELECT de A a Z pelo nome do livro
        ORDER BY o.nome_obra
    ";

                $resO = $conn->query($sqlObrasEmprestadas); // Executa a consulta
            
                if ($resO) {
                    // Loop que cria as tags <option> para cada livro encontrado
                    while ($o = $resO->fetch_assoc()) {
                        $id = $o['idemprestimo']; // O valor enviado será o ID da transação
                        $nome = htmlspecialchars($o['nome_obra']); // Evita erros com nomes especiais
                        echo "<option value='$id'>$nome</option>";
                    }
                }
            }
            ?>
        </select>
        <br><br>

        <label for="data_devolucao_efetiva">Data Efetiva da Devolução:</label>
        <input type="date" id="data_devolucao_efetiva" name="data_devolucao_efetiva"
            value="<?php echo date('Y-m-d'); ?>" required>
        <br><br>

        <input type="submit" value="Registrar Devolução">
    </form>

    <?php
    // Verifica se o formulário foi enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['obra_a_devolver'])) {

        // Captura os valores enviados pelo formulário
        $id_emprestimo_a_atualizar = $_POST['obra_a_devolver'];
        $data_devolucao_efetiva = $_POST['data_devolucao_efetiva'];
        $status_devolucao = 'devolvido'; // Status fixo para marcar como encerrado
    
        // Proteção: Limpa as variáveis para evitar SQL Injection (invasão via formulário)
        $id_emprestimo_esc = mysqli_real_escape_string($conn, $id_emprestimo_a_atualizar);
        $data_efetiva_esc = mysqli_real_escape_string($conn, $data_devolucao_efetiva);
        $status_esc = mysqli_real_escape_string($conn, $status_devolucao);

        // Comando SQL para atualizar o registro
        // SET define as colunas que vão mudar. WHERE garante que só mude o livro selecionado.
        $sqlUpdateDevolucao = " UPDATE emprestimo 
            SET 
                emprestado_devolvido = '$status_esc',
                devolucao = '$data_efetiva_esc'
            WHERE idemprestimo = '$id_emprestimo_esc'
        ";

        // Executa o comando e verifica se deu certo
        if (mysqli_query($conn, $sqlUpdateDevolucao)) {
            echo "<div class='mensagem sucesso'> Devolução registrada com sucesso!</div>";
        } else {
            // Se falhar (ex: coluna inexistente), mostra o erro técnico do MySQL
            echo "<div class='mensagem erro'> Erro no banco: " . mysqli_error($conn) . "</div>";
        }
    }
    ?>
</body>

</html>