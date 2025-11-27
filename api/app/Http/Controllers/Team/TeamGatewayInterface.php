<?php

interface TeamGatewayInterface {
    // public function createTeam(string $state, string $num, string $name);
    // public function updateTeam(string $team_id, string $state, string $num, string $name);
    public function deleteTeam(int $team_id);

    public function joinTeam(int $team_id, int $user_id, int $permission_level);
    public function quitTeam(int $user_id);

    public function getTeamFromId(int $team_id): array;
    
    public function getTeamMembers(int $team_id): array;
    public function getUserTeamData(int $user_id): array;
    
    public function checkTeamExists(string $name): bool;
    public function checkTeamExistsId(int $team_id): bool;
}
