<?php 

class ChiefGateway extends UserGateway {
    public function getChief(int $id_user): array {
        // TODO: Colocar as informações do chefe para serem puxadas aqui
        $sql = "SELECT C.id_user as ChefiaId
                FROM Chefia C
                INNER JOIN Users U ON U.id = C.id_user
                WHERE C.id_user = :id";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id", $id_user, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return is_bool($result) ? [] : $result;
    }

    // TODO: Modificar isso aqui para inserir as informações necessárias do chefe
    public function createChiefAccount(int $id_user): array {
        $sql = "INSERT INTO Chefia (id_user)
                VALUES (:id_user)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id_user", $id_user, PDO::PARAM_STR);
        
        $stmt->execute();
        return [
            "id" => $this->conn->lastInsertId()
        ];
    }

    // TODO: Atualizar as informações específicas do chefe aqui
    public function updateAccountData(int $id_user, string $password, string $username, string $name) : array {
        parent::updateAccountData($id_user, $password, $username, $name);
        
        $sql = "UPDATE Chefia
                SET 
                WHERE id_user = :id_user";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id_user", $id_user, PDO::PARAM_STR);
        
        $stmt->execute();

        return $this->getAccountFromId($id_user);
    }

    public function checkChiefExists(int $chief_id): bool {
        $sql = "SELECT COUNT(id) FROM Chefia
                WHERE id = :chief_id";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":chief_id", $chief_id, PDO::PARAM_STR);
        
        $stmt->execute();
        return !empty($stmt->fetch()["COUNT(id)"]);
    }
}