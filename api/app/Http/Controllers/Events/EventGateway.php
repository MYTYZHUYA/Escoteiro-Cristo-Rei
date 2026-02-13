<?php

use App\Http\Middleware\BaseGateway;

class EventGateway extends BaseGateway {
    public function createEvent(string $title, string $description, string $banner_url, string $start_date, string $finish_date): array {
        $sql = "INSERT INTO Events (title, description, banner_url, start_date, finish_date)
            VALUES (:title, :description, :banner_url, :start_date, :finish_date)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":title", $title, PDO::PARAM_STR);
        $stmt->bindValue(":description", $description, PDO::PARAM_STR);
        $stmt->bindValue(":banner_url", $banner_url, PDO::PARAM_STR);
        $stmt->bindValue(":start_date", date('Y-m-d H:i:s', $start_date), PDO::PARAM_STR);
        $stmt->bindValue(":finish_date", date('Y-m-d H:i:s', $finish_date), PDO::PARAM_STR);

        $stmt->execute();
        return [
            "id" => $this->conn->lastInsertId()
        ];
    }

    public function updateEvent(int $id_event, string $title, string $description, string $banner_url, int $start_date, int $finish_date) {
        $sql = "UPDATE Events
                SET title = :title, description = :description, banner_url = :banner_url, start_date = :start_date, finish_date = :finish_date
                WHERE id = :id_event";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":title", $title, PDO::PARAM_STR);
        $stmt->bindValue(":description", $description, PDO::PARAM_STR);
        $stmt->bindValue(":banner_url", $banner_url, PDO::PARAM_STR);
        $stmt->bindValue(":start_date", date('Y-m-d H:i:s', $start_date), PDO::PARAM_STR);
        $stmt->bindValue(":finish_date", date('Y-m-d H:i:s', $finish_date), PDO::PARAM_STR);
        $stmt->bindValue(":id_event", $id_event, PDO::PARAM_INT);

        $stmt->execute();
    }

    public function deleteEvent(int $id_event) {
        $sql = "DELETE FROM Events
                WHERE id = :id_event";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id_event", $id_event, PDO::PARAM_INT);

        $stmt->execute();
    }

    public function getEventData(int $id_event) {
        $sql = "SELECT * FROM Events
                WHERE id = :id_event";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id_event", $id_event, PDO::PARAM_INT);

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return is_bool($result) ? [] : $result;
    }

    // Event Targets

    public function getEventTargets(int $id_event) {
        $sql = "SELECT id, id_group, id_troup FROM Event_Target
                WHERE id_event = :id_event";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id_event", $id_event, PDO::PARAM_INT);

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return is_bool($result) ? [] : $result;
    }

    public function eventExistsId(int $id_event) {
        $sql = "SELECT COUNT(id) FROM Events
                WHERE id = :id_event";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id_event", $id_event, PDO::PARAM_STR);
        
        $stmt->execute();
        return !empty($stmt->fetch()["COUNT(id)"]);
    }

    // public function getTargetedUsers(int $id_event) {
    //     $sql = "SELECT id_group, id_troup FROM Event_Target
    //             WHERE id_event = :id_event";

    //     $stmt = $this->conn->prepare($sql);
    //     $stmt->bindValue(":id_event", $id_event, PDO::PARAM_INT);

    //     $stmt->execute();
    //     $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //     return is_bool($result) ? [] : $result;
    // }

    // Verificar no controller se a pessoa só passou patrol, se sim, tem que usar a função de verificar hierarquia lá
    public function assignEventTarget(int $id_event, int $id_group, int $id_troup) {
        $sql = "INSERT INTO Event_Target (id_event, id_group, id_troup, id_patrol)
                VALUES (:id_event, :id_group, :id_troup, :id_patrol)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id_event", $id_event, PDO::PARAM_INT);
        $stmt->bindValue(":id_group", $id_group, PDO::PARAM_INT);
        $stmt->bindValue(":id_troup", $id_troup, PDO::PARAM_INT);

        $stmt->execute();
        return [
            "id" => $this->conn->lastInsertId()
        ];
    }

    public function updateEventTarget(int $target_id, int $id_event, int $id_group, int $id_troup) {
        $sql = "UPDATE Event_Target
                SET id_event = :id_event, id_group = :id_group, id_troup = :id_troup
                WHERE id = :target_id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":id_event", $id_event, PDO::PARAM_INT);
        $stmt->bindValue(":id_group", $id_group, PDO::PARAM_INT);
        $stmt->bindValue(":id_troup", $id_troup, PDO::PARAM_INT);
        $stmt->bindValue(":target_id", $target_id, PDO::PARAM_INT);

        $stmt->execute();
    }

    public function userCanCreateEvent(int $user_id) {
        $sql = "SELECT COUNT(id) FROM Chefia
                WHERE id_user = :user_id";
        
        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_STR);
        
        $stmt->execute();
        return !empty($stmt->fetch()["COUNT(id)"]);
    }
}