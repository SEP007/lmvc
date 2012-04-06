<?php

class Security extends Controller {

    public static function login() {
        self::render();
    }

    public static function postLogin() {
        $user = User::authenticate(App::get()->request->username, App::get()->request->password);
        if (is_object($user)) {
            $_SESSION['currentUser'] = $user->id;
            self::redirect($_SESSION['afterLogin']);
        } else {
            $_SESSION['currentUser'] = null;
            self::redirect('/security/login');
        }
    }

    public static function logout($to='/security/login') {
        $_SESSION['currentUser'] = null;
        self::redirect('/security/login');
    }

}