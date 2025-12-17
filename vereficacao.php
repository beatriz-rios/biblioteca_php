<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório Completo de Empréstimos</title>
    <link rel="stylesheet" href="./_css/style.css">
    <link rel="stylesheet" href="./_css/nav.css">
    <style>
        /* Estilos adicionais para o relatório */
        /* Uso de variáveis do style.css para consistência */
        .atrasado {
            background-color: #380000; /* Fundo vermelho escuro para atrasos */
            color: #ffcccc; /* Texto vermelho claro */
            font-weight: bold;
        }
        .devolvido {
            background-color: var(--preto-terciario); /* Fundo cinza escuro para devolvido */
            color: var(--cinza-claro); /* Texto mais suave */
        }
        .ok {
            background-color: #053b05; /* Fundo verde escuro para no prazo */
            color: #ccffcc;
        }
        table {
            width: 90%;
            border-collapse: collapse;
            margin: 20px auto;
            background-color: var(--preto-secundario);
            box-shadow: var(--sombra);
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            border: 1px solid var(--preto-terciario);
            padding: 12px;
            text-align: left;
        }
        th {
            background: var(--gradiente-dourado);
            color: var(--preto-principal);
            font-weight: 700;
            text-transform: uppercase;
        }
    </style>
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
                    <a href="devolucao.php" class="navbar-link ">Devoluções</a>
                </li>
                <li class="navbar-item">
                    <a href="vereficacao.php" class="navbar-link active">Relatórios</a>
                </li>
            </ul>
        </div>
    </nav>


    <h1>Relatório Completo de Empréstimos (Ativos e Devolvidos)</h1>

    <?php
    // Inclui o arquivo de conexão
    include 'conexao.php';
    // Define a data atual para comparação
    $data_hoje = date('Y-m-d'); 
    
    // Configurações do banco
    $servername = "localhost";
    $database = "biblioteca";
    $username = "root";
    $password = "";

    $conn = mysqli_connect($servername, $username, $password, $database);

    if (!$conn) {
        echo "<div class='mensagem erro'>Falha na conexão: " . mysqli_connect_error() . "</div>";
        die();
    }

    //  SQL para buscar TODAS as transações (ativas e devolvidas) 
    
    $sqlRelatorio = "
        SELECT 
            e.idemprestimo,
            u.nome AS nome_usuario,
            o.nome_obra,
            e.data_emprestimo,
            e.devolucao AS data_prevista_devolucao,
            e.tempo_emprestimo,
            e.emprestado_devolvido AS status_atual
        FROM emprestimo e
        
        -- Junta com as tabelas de apoio
        JOIN obra o ON e.obra_idobra = o.idobra
        JOIN usuario u ON e.id_usuario = u.idusuario
        
        -- ORDENA: 
        -- 1. Por obras ainda ativas primeiro (emprestado).
        -- 2. Por data de empréstimo do MAIS ANTIGO PARA O MAIS RECENTE (ASC).
        ORDER BY 
            CASE WHEN e.emprestado_devolvido = 'emprestado' THEN 0 ELSE 1 END,
            e.data_emprestimo ASC /* ALTERADO: Mostra as transações mais antigas primeiro */
    ";

    $resRelatorio = mysqli_query($conn, $sqlRelatorio);

    if (!$resRelatorio) {
        echo "<div class='mensagem erro'>Erro ao executar a consulta do relatório: " . mysqli_error($conn) . "</div>";
    } elseif (mysqli_num_rows($resRelatorio) == 0) {
        echo "<div class='mensagem sucesso'>O banco de dados não contém registros de empréstimo.</div>";
    } else {
        // Inicia a tabela para exibir os resultados
        echo "<table>";
        echo "<tr>";
        echo "<th>ID</th>";
        echo "<th>Obra</th>";
        echo "<th>Usuário</th>";
        echo "<th>Empréstimo (Data)</th>";
        echo "<th>Prazo (Dias)</th>";
        echo "<th>Devolução Prevista</th>";
        echo "<th>Status de Devolução</th>";
        echo "</tr>";

        // Loop para exibir cada empréstimo/devolução
        while ($row = mysqli_fetch_assoc($resRelatorio)) {
            $data_prevista = $row['data_prevista_devolucao'];
            $status_db = $row['status_atual']; // 'emprestado' ou 'devolvido'
            
            // Lógica de ATUALIZAÇÃO DO STATUS
            
            if ($status_db == 'devolvido') {
                // Se o status no DB for 'devolvido', exibe como devolvido
                $status_display = "DEVOLVIDO (Finalizado) ";
                $class = "devolvido";
            } else {
                // Se o status no DB for 'emprestado', verifica se está atrasado ou no prazo
                if ($data_prevista < $data_hoje) {
                    // Está emprestado E a data prevista já passou
                    $status_display = "ATRASADO ";
                    $class = "atrasado";
                } else {
                    // Está emprestado E ainda está dentro do prazo
                    $status_display = "No Prazo (Ativo) ";
                    $class = "ok";
                }
            }

            // Exibe a linha da tabela com a classe de cor apropriada
            echo "<tr class='$class'>";
            echo "<td>" . $row['idemprestimo'] . "</td>";
            echo "<td>" . htmlspecialchars($row['nome_obra']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nome_usuario']) . "</td>";
            echo "<td>" . date('d/m/Y', strtotime($row['data_emprestimo'])) . "</td>";
            echo "<td>" . $row['tempo_emprestimo'] . "</td>";
            echo "<td>" . date('d/m/Y', strtotime($data_prevista)) . "</td>";
            echo "<td>" . $status_display . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    mysqli_close($conn);
    ?>
    </body>

</html>