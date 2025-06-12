<?php

/**
 * Modelo para manejar operaciones de la tabla bares
 */
class Bar
{
    private $db;
    private $pdo;

    public function __construct()
    {
        $this->db = new Database();
        $this->pdo = $this->db->connect(); // Asegúrate de que este método retorne un objeto PDO
    }

    /**
     * Insertar un nuevo bar
     */
    public function insertarBar(
        $nombre_bar,
        $direccion,
        $telefono,
        $email,
        $descripcion,
        $estado = 1
    ) {
        $query = $this->pdo->prepare("
            INSERT INTO bares (
                nombre_bar, 
                direccion,
                telefono,
                email,
                descripcion,
                estado,
                fecha_registro
            ) VALUES (
                :nombre_bar,
                :direccion,
                :telefono,
                :email,
                :descripcion,
                :estado,
                NOW()
            )
        ");

        return $query->execute([
            "nombre_bar" => $nombre_bar,
            "direccion" => $direccion,
            "telefono" => $telefono,
            "email" => $email,
            "descripcion" => $descripcion,
            "estado" => $estado
        ]);
    }

    /**
     * Obtener todos los bares activos
     */
    public function obtenerBares($estado = 1)
    {
        $query = $this->pdo->prepare("
            SELECT * FROM bares 
            WHERE estado = :estado 
            ORDER BY nombre_bar ASC
        ");

        $query->execute(["estado" => $estado]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar bares por nombre (para autocompletado)
     */
    public function buscarBaresPorNombre($termino)
    {
        $query = $this->pdo->prepare("
            SELECT id_bar, nombre_bar, direccion, telefono, email 
            FROM bares 
            WHERE nombre_bar LIKE :termino 
            AND estado = 1 
            ORDER BY nombre_bar ASC 
            LIMIT 10
        ");

        $query->execute(["termino" => '%' . $termino . '%']);
        return $query->fetchAll(PDO::FETCH_ASSOC);;
    }

    /**
     * Obtener un bar por ID
     */
    public function obtenerBarPorId($id)
    {
        $query = $this->pdo->prepare("
            SELECT * FROM bares 
            WHERE id_bar = :id
        ");

        $query->execute(["id" => $id]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Actualizar un bar
     */
    public function actualizarBar(
        $id_bar,
        $nombre_bar,
        $direccion,
        $telefono,
        $email,
        $descripcion
    ) {
        $query = $this->pdo->prepare("
            UPDATE bares SET
                nombre_bar = :nombre_bar,
                direccion = :direccion,
                telefono = :telefono,
                email = :email,
                descripcion = :descripcion,
                fecha_actualizacion = NOW()
            WHERE id_bar = :id_bar
        ");

        return $query->execute([
            "id_bar" => $id_bar,
            "nombre_bar" => $nombre_bar,
            "direccion" => $direccion,
            "telefono" => $telefono,
            "email" => $email,
            "descripcion" => $descripcion
        ]);
    }

    /**
     * Eliminar un bar (cambiar estado)
     */
    public function eliminarBar($id_bar)
    {
        $query = $this->pdo->prepare("
            UPDATE bares SET
                estado = 0,
                fecha_actualizacion = NOW()
            WHERE id_bar = :id_bar
        ");

        return $query->execute(["id_bar" => $id_bar]);
    }

    /**
     * Verificar si existe un bar con el mismo nombre
     */
    public function existeBar($nombre_bar, $id_excluir = null)
    {
        $sql = "SELECT COUNT(*) FROM bares WHERE nombre_bar = :nombre_bar AND estado = 1";
        $params = ["nombre_bar" => $nombre_bar];

        if ($id_excluir) {
            $sql .= " AND id_bar != :id_excluir";
            $params["id_excluir"] = $id_excluir;
        }

        $query = $this->pdo->prepare($sql);
        $query->execute($params);

        return $query->fetchColumn() > 0;
    }
    public function obtenerClientes()
    {
        $query = $this->pdo->prepare("
        SELECT id_cliente, razon_social, telefono, direccion, zona, fecha_registro 
        FROM clientes 
        WHERE estado = 1 
        ORDER BY fecha_registro DESC
    ");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertarCliente($id_bar, $razon_social, $telefono, $direccion, $zona)
    {
        $query = $this->pdo->prepare("
            INSERT INTO clientes (
                id_bar, razon_social, telefono, direccion, zona, estado, fecha_registro
            ) VALUES (
                :id_bar, :razon_social, :telefono, :direccion, :zona, 1, NOW()
            )
        ");

        return $query->execute([
            "id_bar" => $id_bar,
            "razon_social" => $razon_social,
            "telefono" => $telefono,
            "direccion" => $direccion,
            "zona" => $zona
        ]);
    }
    public function obtenerUltimoIdInsertado()
    {
        return $this->pdo->lastInsertId();
    }
}
