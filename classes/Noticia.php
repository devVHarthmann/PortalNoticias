<?php
class Noticia
{
    private $conn;
    private $table_name = "noticias";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function registrar($titulo, $noticia, $data, $autor, $imagem)
    {
        $query = "INSERT INTO " . $this->table_name . "(titulo, noticia, data, autor, imagem) VALUES (?,?,?,?,?)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$titulo, $noticia, $data, $autor, $imagem]);
        return $stmt;
    }

    public function ler()
    {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    public function lerPorId($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function atualizar($id, $titulo, $noticia, $data, $autor, $imagem)
    {
        $query = "UPDATE " . $this->table_name . " SET titulo = ?, noticia = ?, data = ?, autor = ?, imagem = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$titulo, $noticia, $data, $autor, $imagem, $id]);
        return $stmt;
    }


    public function deletar($id)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt;
    }
}
