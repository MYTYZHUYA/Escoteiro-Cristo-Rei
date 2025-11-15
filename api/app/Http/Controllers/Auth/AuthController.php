<?php

use App\Http\Controllers\Controller;
use App\Helpers\JwtManager\JwtManager;

require_once __DIR__ . "/AuthGateway.php";
require_once __DIR__ . "/../User/UserGateway.php";

class AuthController extends Controller {
    private AuthGateway $auth_gateway;
    private UserGateway $user_gateway;
    public function __construct() {
        $this->auth_gateway = new AuthGateway();
        $this->user_gateway = new UserGateway();
    }

    public function startSession(array $body_data) {
        // user_id, reg, password
        $user_data = $this->user_gateway->getAccount($body_data["reg"]);
        if (empty($user_data)) { 
            throw new EntityNotFoundException();
        }

        if (!$this->auth_gateway->validateUserCredentials($body_data["reg"], $body_data["password"])) {
            throw new UnauthorizedException([], "Invalid register or password");
        }

        $access_token = $this->genAccessToken($user_data["id"]);

        $refresh_token = bin2hex(random_bytes(32));
        $token_hash = TokenHasher::hashToken($refresh_token, getenv("SECRET_KEY"));

        $exp = time() + ((int) getenv("RERESH_TOKEN_EXP"));
        $this->auth_gateway->storeSession($user_data["id"], $token_hash, $exp);

        echo json_encode([
            "access_token" => $access_token,
            "refresh_token" => $refresh_token
        ]);
    }

    public function checkSessionStats(array $body_data) {
        $session_data = $this->auth_gateway->getSessionData($body_data["refresh_token"]);
        // if (!$this->auth_gateway->checkSessionData($session_data)) { return; }

        $exp_unix = strtotime($session_data["expires_at"]);
        $start_unix = strtotime($session_data["created_at"]);
        
        $elapsed = time() - $exp_unix;

        echo json_encode([
            "remaining_time" => ($exp_unix - $start_unix) - $elapsed
        ]);
    }

    public function quitSession(array $body_data) {
        if (!$this->auth_gateway->validateSession($body_data["refresh_token"])) {return;}

        $this->auth_gateway->removeSession($body_data["refresh_token"]);
        echo json_encode([
            "message" => "Session closed successfully"
        ]);
    }

    public function refreshSession(array $body_data) {
        $session_data = $this->auth_gateway->getSessionData($body_data["refresh_token"]);
        
        $access_token = $this->genAccessToken($session_data["user_id"]);
        echo json_encode([
            "access_token" => $access_token
        ]);
    }

    private function genAccessToken(int $user_id): string {
        $jwt_manager = new JwtManager(getenv("SECRET_KEY"));
        $created_at = time();

        // FIXME?: Não sei se eu deveria colocar no access_token o 'user_id' ou o 'rg'
        return $jwt_manager->createToken([
            "user_id" => $user_id,
            "exp" => $created_at + ((int) getenv("ACCESS_TOKEN_EXP")),
            "iat" => $created_at
        ]);
    }

}