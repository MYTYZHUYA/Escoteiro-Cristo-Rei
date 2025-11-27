<?php

interface TeamInterface {
    public function createTeam(array $body_data, string $auth_token);
    public function updateTeam(array $body_data, string $auth_token);
    public function deleteTeam(string $auth_token);

    public function joinTeam(array $route_params, string $auth_token);
    public function quitTeam(string $auth_token);

    public function transferOwnership(array $body_data, string $auth_token);
    public function updateUserpermission(array $body_data, string $auth_token);

    public function getTeamInfo(array $route_params);
    public function getTeamMembers(array $route_params);
    public function getUserTeamData(array $route_params);
}