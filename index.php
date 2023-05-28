<!DOCTYPE html>
<html>
<head>
    <title>Teste Prático</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Olá, mundo!</h1>
    </header>
    <br>
    <?php
    $servername = "mysql";
    $username = "root";
    $password = "root";
    $database = "sci";

    // Cria a conexão
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);

    // Verifica se houve erro na conexão
    if (!$conn) {
       die("Erro na conexão com o banco de dados.");
    }

    // Consulta no banco de dados
    $sql = "SELECT * FROM clientes";
    $result = $conn->query($sql);

    echo '<div class="highlight">Resultados da consulta:</div>';

    if ($result && $result->rowCount() > 0) {
       // Exibe os dados do banco de dados
       while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
           echo "ID: " . $row["id"] . " - Nome: " . $row["nome"] . " - Email: " . $row["email"] . "<br>";
       }
    } else {
       echo "Nenhum cliente encontrado.";
    }

    // Fecha a conexão
    $conn = null;
    ?>
</body>
</html>