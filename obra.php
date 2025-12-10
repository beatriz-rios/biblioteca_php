<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Obra</title>
</head>

<body>
    <form method="post">
        <h1>Cadastre a obra literária</h1>
        <fieldset>
            <label>Nome da Obra literária</label>
            <input type="text" name="obra">
            <br>
            <label>Nome do autor</label>
            <select name="autores" id="autore">
                <option value="">Selcione um autor</option>
                <option value="1">Andrezej Sapkowski</option>
                <option value="2">J. K. Rowling</option>
                <option value="3">C. S. Lewis</option>
                <option value="4">H. P. Lovecraft</option>
                <option value="5">Stephen King</option>
                <option value="6">Paulo Coelho</option>
            </select>
            <br>
            <label>Nome da editora</label>
            <select name="editor" id="editore">
                <option value="">Selecione uma editora</option>
                <option value="1">Companhia das Letras</option>
                <option value="2">Editora Abril</option>
                <option value="3">Editora Globo</option>
                <option value="4">Editora Record</option>
                <option value="5">Penguin Random House</option>
                <option value="6">HarperCollins</option>
            </select>
            <br>
            <label>Categora</label>
            <select name="categoria" id="categori">
                <option value="">Selecione uma categoria...</option>
                <option value="ficcao">Ficção</option>
                <option value="nao-ficcao">Não-Ficção</option>
                <option value="fantasia">Fantasia</option>
                <option value="ficcao-cientifica">Ficção Científica</option>
                <option value="romance">Romance</option>
                <option value="suspense">Suspense/Terror</option>
                <option value="biografia">Biografia/Autobiografia</option>
                <option value="historia">História</option>
                <option value="autoajuda">Autoajuda/Desenvolvimento Pessoal</option>
                <option value="infantil">Infantil/Juvenil</option>
            </select>
            <br>
            <label>Ano de Lançamento</label>
            <input type="number" id="ano" name="ano" min="1700" max="2025" step="1" placeholder="Ex: 2025" />
        </fieldset>
        <input type="submit" value="Cadastrar">
    </form>


    <?php

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $obra = $_POST['obra'];
        $autor = $_POST['autores'];
        $editora = $_POST['editor'];
        $categoria = $_POST['categoria'];
        $ano = $_POST['ano'];




        $servername = "localhost";
        $database = "biblioteca";
        $username = "root";
        $password = "";

        $conn = mysqli_connect($servername, $username, $password, $database);

        if (!$conn) {
            echo "<div class='mensagem erro'>Falha na conexão: " . mysqli_connect_error() . "</div>";
            die();
        }

       
        $sql = "INSERT INTO obra (
        nome_obra, 
        autor_idautor, 
        editora_ideditora, 
        categoria, 
        anoLancamento) 
        VALUE (
        '$obra', 
        '$autor',
        '$editora', 
        '$categoria', 
        '$ano')";

     if(mysqli_query($conn, $sql)){
    echo"<br>Comando executado com sucesso";
}else{
    echo"Error: " . $sql . "<br>" . mysqli_error($conn);
}


 mysqli_close($conn);
  }
    ?>

</body>

</html>