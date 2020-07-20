<?php

/**
 * Created by PhpStorm.
 * User: mr.incognito
 * Date: 10.11.2018
 * Time: 21:36
 */
class Main_page extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        App::get_ci()->load->model('User_model');
        App::get_ci()->load->model('Login_model');
        App::get_ci()->load->model('Post_model');

        if (is_prod()) {
            die('In production it will be hard to debug! Run as development environment!');
        }
    }

    public function index()
    {
        $user = User_model::get_user();


        App::get_ci()->load->view('main_page', ['user' => User_model::preparation($user, 'default')]);
    }

    public function get_all_posts()
    {
        $posts = Post_model::preparation(Post_model::get_all(), 'main_page');
        return $this->response_success(['posts' => $posts]);
    }

    public function get_post($post_id)
    { // or can be $this->input->post('news_id') , but better for GET REQUEST USE THIS

        $post_id = intval($post_id);

        if (empty($post_id)) {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }

        try {
            $post = new Post_model($post_id);
        } catch (EmeraldModelNoDataException $ex) {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NO_DATA);
        }


        $posts = Post_model::preparation($post, 'full_info');
        return $this->response_success(['post' => $posts]);
    }


    public function comment_post($post_id, $message)
    {
        if (!User_model::is_logged()) {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NEED_AUTH);
        }

        $post_id = intval($post_id);

        if (empty($post_id) || empty($message)) {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }

        try {
            $post = new Post_model($post_id);
        } catch (EmeraldModelNoDataException $ex) {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NO_DATA);
        }

        $comment = new Comment_model();
        $comment::create([
            'user_id' => App::get_ci()->session->get_userdata()['id'],
            'assign_id' => $post_id,
            'text' => urldecode($message),
        ]);
        $posts = Post_model::preparation($post, 'full_info');
        return $this->response_success(['post' => $posts]);
    }


    /**
     * не вижу смысла для параметра userId, даже для тестов (по крайней мере в начале метода)
     **/
    public function login()
    {
        $login = App::get_ci()->input->post('login');
        $password = App::get_ci()->input->post('password');

        $userId = Login_model::login($login, $password);

        if (!empty($userId)) {
            Login_model::start_session($userId);

            return $this->response_success(['user' => $userId]);
        } else {
            return $this->response_success(['message' => 'Incorrect Login or Password']);
        }
    }


    public function logout()
    {
        Login_model::logout();
        redirect(site_url('/'));
    }

    public function add_money()
    {
        // todo: add money to user logic
        return $this->response_success(['amount' => rand(1, 55)]);
    }

    public function buy_boosterpack()
    {
        // todo: add money to user logic
        return $this->response_success(['amount' => rand(1, 55)]);
    }


    public function like($type, $entityId)
    {
        if (!User_model::is_logged()) {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NEED_AUTH);
        }
        $userId = App::get_ci()->session->userdata('id');
        $user = new User_model($userId);
        $userLikes = $user->get_likes();
        if (0 == $userLikes) {
            return $this->response_success(['type' => 'errorLike']);
        }
        $likes = 0;
        if ('post' == $type) {
            try {
                $post = new Post_model($entityId);
                $likes = $post->get_likes() + 1;
                $post->set_likes($likes);
            } catch (EmeraldModelNoDataException $ex) {
                return $this->response_error(CI_Core::RESPONSE_GENERIC_NO_DATA);
            }
        } else if ('comment' == $type) {
            try {
                $comment = new Comment_model($entityId);
                $likes = $comment->get_likes() + 1;
                $comment->set_likes($likes);
            } catch (EmeraldModelNoDataException $ex) {
                return $this->response_error(CI_Core::RESPONSE_GENERIC_NO_DATA);
            }
        }
        $user->set_likes($userLikes - 1);
        return $this->response_success(['likes' => $likes, 'type' => $type, 'entityId' => $entityId]);
    }

}
