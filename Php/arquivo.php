<?php

$pdo = require_once 'config.php';


if (isset($_GET['tipo'])) {
    $tipo = $_GET['tipo'];
}

if ($tipo == 'ler') {
    lerDados($pdo);
} else if ($tipo == 'cadastrar') {
    cadastrar($pdo);
} else if ($tipo == 'deletar') {
    deletar($pdo);
} else if ($tipo == 'alterar') {
    alterar($pdo);
} else if ($tipo == 'ordenar_home') {
    ordenar_home($pdo);
} else if ($tipo == 'ordenar_categoria') {
    ordenar_categoria($pdo);
}


function cadastrar($pdo)
{
    // inserir
    $json_convertido = json_decode(file_get_contents('php://input'), true);

    $nome = $json_convertido['nome'];
    $quantidade = $json_convertido['quantidade'];
    $unidade = $json_convertido['unidade'];
    $preco_de_compra  = $json_convertido['precoInicial'];
    $preco_de_venda = $json_convertido['precoFinal'];


    $sql = 'INSERT INTO produto(nome,quantidade,unidade,preco_de_compra,preco_de_venda)  VALUES(:nome,:quantidade,:unidade,:preco_de_compra,:preco_de_venda)';

    $statement = $pdo->prepare($sql);

    $statement->execute([
        ':nome' => $nome,
        ':quantidade' => $quantidade,
        ':unidade' => $unidade,
        ':preco_de_compra' => $preco_de_compra,
        ':preco_de_venda' => $preco_de_venda,
    ]);
}


function alterar($pdo)
{
    $json_convertido = json_decode(file_get_contents('php://input'), true);
    $id = $json_convertido['id'];
    $nome = $json_convertido['nome'];
    $quantidade = $json_convertido['quantidade'];
    $unidade = $json_convertido['unidade'];
    $preco_de_compra  = $json_convertido['precoInicial'];
    $preco_de_venda = $json_convertido['precoFinal'];

    $publisher = [
        'publisher_id' => $id,
        'nome' => $nome,
        'quantidade' => $quantidade,
        'unidade' => $unidade,
        'preco_de_compra' => $preco_de_compra,
        'preco_de_venda' => $preco_de_venda,
    ];

    $sql = 'UPDATE produto
        SET nome = :nome,
        quantidade = :quantidade,
        unidade = :unidade,
        preco_de_compra = :preco_de_compra,
        preco_de_venda = :preco_de_venda
        WHERE id = :publisher_id';


    $statement = $pdo->prepare($sql);

    $statement->bindParam(':publisher_id', $publisher['publisher_id'], PDO::PARAM_INT);
    $statement->bindParam(':nome', $publisher['nome']);
    $statement->bindParam(':quantidade', $publisher['quantidade']);
    $statement->bindParam(':unidade', $publisher['unidade']);
    $statement->bindParam(':preco_de_compra', $publisher['preco_de_compra']);
    $statement->bindParam(':preco_de_venda', $publisher['preco_de_venda']);

    $statement->execute();
}

function deletar($pdo)
{

    $id = $_GET['idRegistro'];

    $sql = 'DELETE FROM produto
        WHERE id = :id';

    $statement = $pdo->prepare($sql);
    $statement->bindParam(':id', $id, PDO::PARAM_INT);

    $statement->execute();
}

function ordenar_home($pdo)
{
    $statement = $pdo->query("SELECT * FROM produto ORDER BY id ASC");

    $statement->execute();

    $resultados = $statement->fetchAll();

    $json = json_encode($resultados);

    echo $json;
}


function ordenar_categoria($pdo)
{

    if (isset($_GET['categoria']) ? $_GET['categoria'] : "null") {
        $categoria = $_GET['categoria'];
    }

    if (isset($_GET['seta']) ? $_GET['seta'] : "null") {
        $seta = $_GET['seta'];
    } 

    $categoria_atualizada = categoria_selecionada($categoria);

    if ($seta == 'ASC') {
        $statement = $pdo->prepare("SELECT * FROM produto ORDER BY $categoria_atualizada ASC");
    }else {
        $statement = $pdo->prepare("SELECT * FROM produto ORDER BY $categoria_atualizada DESC");
    }


    $statement->execute();

    $resultados = $statement->fetchAll();

    $json = json_encode($resultados);

    header('Content-Type: application/json');
    echo $json;
}

function categoria_selecionada($categoria)
{
    if (trim($categoria) == "Cód") {
        return $categoria = "id";
    } elseif (trim($categoria) == "Produto") {
        return  $categoria = "nome";
    } elseif (trim($categoria) == "Quantidade") {
        return  $categoria = "quantidade";
    } elseif (trim($categoria) == "Unidade") {
        return  $categoria = "unidade";
    } elseif (trim($categoria) == "Preço de Compra") {
        return  $categoria = "preco_de_compra";
    } elseif (trim($categoria) == "Preço de Venda") {
        return  $categoria = "preco_de_venda";
    }
}

function lerDados($pdo)
{
    $sql = 'SELECT * FROM produto';

    $statement = $pdo->query($sql);

    $publishers = $statement->fetchAll(PDO::FETCH_ASSOC);

    $json = json_encode($publishers);

    header('Content-Type: application/json');
    echo $json;
}
