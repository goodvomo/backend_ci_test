<?php
class Login_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();

    }

    public static function login($login, $password) :?int
    {
        $user = App::get_ci()->s
            ->from('user')
            ->where('personaname', $login)
            ->select(['id', 'password'])
            ->one();

        if (!empty($user) && $password == $user['password']) {
            return $user['id'];
        } else {
            return null;
        }
    }

    public static function logout()
    {
        App::get_ci()->session->unset_userdata('id');
    }

    public static function start_session(int $user_id)
    {
        // если перенедан пользователь
        if (empty($user_id))
        {
            throw new CriticalException('No id provided!');
        }

        App::get_ci()->session->set_userdata('id', $user_id);
    }


}
