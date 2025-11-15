<?php 

require_once __DIR__ . "/GroupGateway.php";
use App\Http\Controllers\BaseController;

class GroupController extends BaseController {
    protected GroupGateway $group_gateway;
    public function __construct() {
        $this->group_gateway = new GroupGateway();
    }

    // TODO: Add routes for gateway functions 
    // TODO: Add routes on UserController (or here idk) for joining groups
}