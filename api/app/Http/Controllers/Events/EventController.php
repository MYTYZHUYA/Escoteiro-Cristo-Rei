<?php

use App\Helpers\JwtManager\JwtManager;
use App\Http\Controllers\Controller;

require_once __DIR__ . "/EventGateway.php";
require_once __DIR__ . "/../Group/GroupGateway.php";
require_once __DIR__ . "/../Troup/TroupGateway.php";

class EventController extends Controller {
    protected EventGateway $event_gateway;
    protected GroupGateway $group_gateway;
    protected TroupGateway $troup_gateway;
    public function __construct() {
        $this->event_gateway = new EventGateway();
    }

    public function createEvent(array $body_data, string $auth_token) {
        $this->checkFieldLengths([
            "title" => [6, 128],
            "banner_url" => [0, 256]
        ], $body_data);

        $jwt = new JwtManager(getenv("SECRET_KEY"));
        $token_data = $jwt->decodeToken($auth_token);
        if (!$this->event_gateway->userCanCreateEvent($token_data["user_id"])) {
            throw new UnauthorizedException([], "To create an event you must be a chief");
        }

        // TODO: fazer mais handling, acho que tá faltando coisa
        // TODO: atribuir os "alvos" do evento aqui já

        $event_id = $this->event_gateway->createEvent(
            $body_data["title"],
            $body_data["description"],
            $body_data["banner_url"],
            array_key_exists("start_date", $body_data["start_date"]) ? $body_data["start_date"] : time(),
            $body_data["finish_date"]
        );

        echo json_encode([
            "message" => "Created event sucessfully",
            "event_id" => $event_id
        ]);
    }

    public function updateEvent(array $body_data, array $route_params, string $auth_token) {
        $this->checkFieldLengths([
            "title" => [6, 128],
            "banner_url" => [0, 256]
        ], $body_data);

        $jwt = new JwtManager(getenv("SECRET_KEY"));
        $token_data = $jwt->decodeToken($auth_token);
        if (!$this->event_gateway->userCanCreateEvent($token_data["user_id"])) {
            throw new UnauthorizedException([], "To update an event you must be a chief");
        }

        if (!$this->event_gateway->eventExistsId($route_params["id_event"])) {
            throw new EntityNotFoundException([], "Couldn't find event of id {$route_params['id_event']}");
        }

        // TODO: fazer mais handling, acho que tá faltando coisa

        $old_data = $this->event_gateway->getEventData($route_params["id_event"]);
        $changed = false;
        foreach ($old_data as $key => $value) {
            if (!array_key_exists($key, $body_data)) {
                $body_data[$key] = $value;
                continue;
            }
            if ($body_data[$key] == "") {
                $body_data[$key] = $value; 
                continue;
            }
            $changed = true;
        }

        if (!$changed) {
            echo json_encode([
                "message" => "No data changed"
            ]);
        }

        $this->event_gateway->updateEvent(
            $route_params["id_event"],
            $body_data["title"],
            $body_data["description"],
            $body_data["banner_url"],
            array_key_exists("start_date", $body_data["start_date"]) ? $body_data["start_date"] : time(),
            $body_data["finish_date"]
        );

        echo json_encode([
            "message" => "Updated event successfully"
        ]);
    }

    public function assignEventTarget(array $body_data, array $route_params, string $auth_token) {
        $jwt = new JwtManager(getenv("SECRET_KEY"));
        $token_data = $jwt->decodeToken($auth_token);
        if (!$this->event_gateway->userCanCreateEvent($token_data["user_id"])) {
            throw new UnauthorizedException([], "To update an event you must be a chief");
        }
        
        if (!$this->event_gateway->eventExistsId($route_params["id_event"])) {
            throw new EntityNotFoundException([], "Couldn't find event of id {$route_params['id_event']}");
        }
        
        if (!array_key_exists("id_group", $body_data) && !array_key_exists("id_troup", $body_data)) {
            throw new BadRequestException([], "You must add at least one team id to target");
        }

        if (array_key_exists("id_group", $body_data)){
            $this->group_gateway = new GroupGateway();
            if (!$this->group_gateway->checkTeamExistsId($body_data["id_group"])) {
                throw new EntityNotFoundException([], "The target group doesn't exist");
            }
        } else { $body_data["id_group"] = null; }
        
        if (array_key_exists("id_troup", $body_data)) {
            $this->troup_gateway = new TroupGateway();
            if (!$this->troup_gateway->checkTeamExistsId($body_data["id_troup"])) {
                throw new EntityNotFoundException([], "The target troup doesn't exist");
            }
        } else { $body_data["id_troup"] = null; }
        
        $this->event_gateway->assignEventTarget(
            $route_params["id_event"],
            $body_data["id_group"],
            $body_data["id_troup"],
        );

        echo json_encode([
            "message" => "Added targets to event ({$route_params['id_event']})"
        ]);
    }
}