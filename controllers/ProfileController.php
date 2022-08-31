<?php
namespace Controllers;
require_once __DIR__ . '/../views/profile/profile.php';

class ProfileController{
    public function actionViewProfile(){
        $user_id = get_current_user_id();
        return profile($user_id);
    }
}