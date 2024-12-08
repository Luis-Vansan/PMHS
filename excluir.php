<?php

require 'conexao.php'; // Inclui a conexão com o banco

// Verificar se o parâmetro `id` foi passado na URL
if (isset($_GET['id'])) {
    $id_post = (int)$_GET['id'];

    // Preparar a consulta para excluir o post
    $sql = "DELETE FROM posts WHERE id = ?";

    // Preparar a declaração
    $stmt = $con->prepare($sql); // Use $con em vez de $conn
    if ($stmt) {
        // Bind do parâmetro
        $stmt->bind_param("i", $id_post);

        // Executar a declaração
        if ($stmt->execute()) {
            echo  "<script>console.log('Post excluído com Sucesso!);</script>";
            header('location: feedadm.php');
        } else {
            echo "<script>console.log('Erro ao excluir o post: )'" . $stmt->error;
        }

        // Fechar a declaração
        $stmt->close();
    } else {
        echo "Erro na preparação da consulta: " . $con->error; // Use $con em vez de $conn
    }
} else {
    echo "ID do post não especificado.";
}

// Fechar a conexão
$con->close(); // Use $con em vez de $conn
?>
