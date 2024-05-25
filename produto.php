<?php

// Função para obter os parâmetros da requisição
function getParams() {
    return json_decode(file_get_contents("php://input"), true);
}

// Função para obter a conexão com o banco de dados
function getConnection() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "estoque";

    // Criar conexão
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar conexão
    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }

    return $conn;
}

// Função para buscar produtos no banco de dados
function selectProdutos() {
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    $conn = getConnection();

    // Construir a consulta SQL com base nos parâmetros da requisição GET
    $sql = "SELECT id, nome, preco, quantidade, foto FROM produtos";
    $where = [];

    foreach ($_GET as $key => $value) {
        $where[] = "$key = '$value'";
    }

    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }

    // Executar a consulta
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $produtos = [];

        while ($row = $result->fetch_assoc()) {
            $produtos[] = $row;
        }

        echo json_encode($produtos);
    } else {
        echo json_encode([]);
    }

    $conn->close();
}

// Determinar o método da requisição
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case "GET":
        selectProdutos();
        break;
    default:
        // Retornar um erro para métodos não suportados
        http_response_code(405); // Método não permitido
        echo json_encode(["error" => "Método não permitido"]);
        break;
}

?>
