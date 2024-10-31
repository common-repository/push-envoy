<?php

class PUSH_ENVOY_ADMIN {

    public function save_push_envoy_custom_meta($data, $post_id) {
        $send_pattern = array();

        if (!isset($data['push_resend'])) {
            $data['push_resend'] = '';
        }
        if (!isset($data['send_push_type'])) {
            $data['send_push_type'] = '';
        }

        if ($data['push_resend'] == 'resend') { /// trigger when post is updated
            $send_pattern['push_send_time'] = 'now';
            $send_pattern['pattern'] = 'now';
            $send_pattern['resend_flag'] = 1;
        } else {


            $send_pattern['pattern'] = sanitize_text_field($data['send_push_type']);

            if ($send_pattern['pattern'] == 'later') {
                $send_pattern['push_send_time'] = sanitize_text_field($data['push_sendtime']);
            } elseif ($send_pattern['pattern'] == 'now') {
                $send_pattern['push_send_time'] = 'now';
            } elseif ($send_pattern['pattern'] == 'nosend') {
                $send_pattern['push_send_time'] = 'nosend';
            } else {
                $send_pattern['push_send_time'] = 'now';
                $send_pattern['pattern'] = 'now';
            }
            $send_pattern['resend_flag'] = 0;
        }

        //// Save Segment//
        $post_segment = array();
        if ($send_pattern['pattern'] != 'nosend' && count($data['push_send_segments']) == 0) {
            //set all as the default segment //
            $post_segment[] = 'all';
        }

        foreach ($data['push_send_segments'] as $segment) {
            $post_segment[] = sanitize_text_field($segment);
        }

        update_post_meta($post_id, 'push_send_pattern', $send_pattern);
        update_post_meta($post_id, 'push_post_segment', $post_segment);
    }

    public function prepare_platforms($post_platform) {

        $prepare_platforms = '[';
        $count = 0;

        foreach ($post_platform as $platform) {
            if ($count > 0) {
                $prepare_platforms .= ',"' . sanitize_text_field($platform) . '"';
            } else {
                $prepare_platforms .= '"' . sanitize_text_field($platform) . '"';
            }
            $count = $count + 1;
        }
        $prepare_platforms .= ']';

        return $prepare_platforms;
    }

    public function check_token($token) {
        //// check push envoy server if token is correct ///
        $url = 'https://pushenvoy.com/app/hooks/apis/';
        $response = wp_remote_post($url, array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(),
            'body' => array(
                'Access_Token' => $token,
                'action' => 'CHECK_TOKEN',
        )));


        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            $error_msg = "Something went wrong:" . $error_message;
            $result = array();
            $result['status'] = 2;
            $result['message'] = $error_msg;
            return $result;
        } else {
            $result = array();
            $res = json_decode($response['body']);

            if ($res->respond == 1) {
                $result['status'] = 1;
                $result['message'] = $res->message;
                $result['appid'] = $res->appid;
            } else {
                $result['status'] = 2;
                $result['message'] = $res->message;
            }

            return $result;
        }
    }

    public function send_push_message($data, $apptoken) {

        $title = $data->msg_title;
        $message = $data->msg_body;
        $open_url = $data->open_url;
        $icon_url = $data->icon_url;
        $dtitle = $data->msg_title;
        $platform = $data->platform;

        //// check push envoy server if token is correct ///
        $url = 'https://pushenvoy.com/app/api/send_message/';
        $response = wp_remote_post($url, array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(),
            'body' => array(
                'Access_Token' => $apptoken,
                'title' => $title,
                'message' => $message,
                'send_type' => 'now',
                'desktop_link' => $open_url,
                'desktop_icon' => $icon_url,
                'desktop_title' => $dtitle,
                'platform' => $platform,
        )));


        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            $error_msg = "Something went wrong:" . $error_message;
            $result = array();
            $result['status'] = 2;
            $result['message'] = $error_msg;
            return $result;
        } else {
            $result = array();
            ///print_r($response);
            $res = json_decode($response['body']);

            //print_r($res);
            if ($res->respond == 1) {
                $result['status'] = 1;
                $result['message'] = $res->message;
            } else {
                $result['status'] = 2;
                $result['message'] = $res->message;
            }

            return $result;
        }
    }

    public function auto_send_push_message($post_title, $post_body, $post_link, $icon_link, $platforms, $apptoken, $send_time, $send_type, $send_segments) {

        if (in_array("all", $send_segments)) {
            /// send to every one inrrespective of other segments //
            $segments = '';
        } else {
            $segments = $this->prepare_platforms($send_segments); /// prepare in this format ['1','2]
        }


        //// check push envoy server if token is correct ///
        $url = 'https://pushenvoy.com/app/api/send_message/';
        $response = wp_remote_post($url, array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(),
            'body' => array(
                'Access_Token' => $apptoken,
                'title' => $post_title,
                'message' => $post_body,
                'send_type' => $send_type,
                'send_time' => $send_time,
                'desktop_link' => $post_link,
                'desktop_icon' => $icon_link,
                'desktop_title' => $post_title,
                'platform' => $platforms,
                'inchannels_and' => $segments
        )));


        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            $error_msg = "Something went wrong:" . $error_message;
            $result = array();
            $result['status'] = 2;
            $result['message'] = $error_msg;
            return $result;
        } else {
            $result = array();
            ///print_r($response);
            $res = json_decode($response['body']);

            //print_r($res);
            if ($res->respond == 1) {
                $result['status'] = 1;
                $result['message'] = $res->message;
            } else {
                $result['status'] = 2;
                $result['message'] = $res->message;
            }

            return $result;
        }
    }

    public function get_segments($token) {
        //// check push envoy server if token is correct ///
        $url = 'https://pushenvoy.com/app/api/get_channels/';
        $response = wp_remote_post($url, array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(),
            'body' => array(
                'Access_Token' => $token,
                'orderby' => 'date',
                'order' => 'desc',
        )));


        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            $error_msg = "Something went wrong:" . $error_message;
            $result = array();
            $result['status'] = 2;
            $result['message'] = $error_msg;
            return $result;
        } else {
            $result = array();
            $res = json_decode($response['body']);

            if ($res->respond == 1) {
                $result['status'] = 1;
                $result['message'] = $res->message;
                $result['data'] = $res->result;
            } else {
                $result['status'] = 2;
                $result['message'] = $res->message;
                // $result['data']=$res->result;
            }

            return $result;
        }
    }

    public function create_segment($token, $segment_name) {
        //// check push envoy server if token is correct ///
        $url = 'https://pushenvoy.com/app/api/new_channel/';
        $response = wp_remote_post($url, array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(),
            'body' => array(
                'Access_Token' => $token,
                'title' => $segment_name,
        )));


        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            $error_msg = "Something went wrong:" . $error_message;
            $result = array();
            $result['status'] = 2;
            $result['message'] = $error_msg;
            return $result;
        } else {
            $result = array();
            $res = json_decode($response['body']);

            if ($res->respond > 0) {
                $result['status'] = 1;
                $result['message'] = $res->message;
            } else {
                $result['status'] = 2;
                $result['message'] = $res->message;
            }

            return $result;
        }
    }

    public function get_account_info($token) {

        //// check push envoy server if token is correct ///
        $url = 'https://pushenvoy.com/app/hooks/apis/';
        $response = wp_remote_post($url, array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(),
            'body' => array(
                'Access_Token' => $token,
                'action' => 'ACCOUNT_INFO',
        )));


        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            $error_msg = "Something went wrong:" . $error_message;
            $result = array();
            $result['status'] = 2;
            $result['message'] = $error_msg;
            return $result;
        } else {
            $result = array();

            $res = json_decode($response['body']);

            if ($res->respond == 1) {
                $result['status'] = 1;
                $result['message'] = $res->message;
                $result['data'] = $res->data;
            } else {
                $result['status'] = 2;
                $result['message'] = $res->message;
            }

            return $result;
        }
    }

    public function get_account_message($token) {
        //// check push envoy server if token is correct ///
        $url = 'https://pushenvoy.com/app/hooks/apis/';
        $response = wp_remote_post($url, array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(),
            'body' => array(
                'Access_Token' => $token,
                'action' => 'USER_MESSAGES',
        )));


        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            $error_msg = "Something went wrong:" . $error_message;
            $result = array();
            $result['status'] = 2;
            $result['message'] = $error_msg;
            return $result;
        } else {
            $result = array();
            $res = json_decode($response['body']);

            if ($res->respond == 1) {
                $result['status'] = 1;
                $result['message'] = $res->message;
                $result['data'] = $res->data;
            } else {
                $result['status'] = 2;
                $result['message'] = $res->message;
            }

            return $result;
        }
    }

    public function get_app_subscribers($token) {
        //// check push envoy server if token is correct ///
        $url = 'https://pushenvoy.com/app/hooks/apis/';
        $response = wp_remote_post($url, array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(),
            'body' => array(
                'Access_Token' => $token,
                'action' => 'APP_SUBSCRIBERS',
        )));


        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            $error_msg = "Something went wrong:" . $error_message;
            $result = array();
            $result['status'] = 2;
            $result['message'] = $error_msg;
            return $result;
        } else {
            $result = array();
            $res = json_decode($response['body']);

            if ($res->respond == 1) {
                $result['status'] = 1;
                $result['message'] = $res->message;
                $result['data'] = $res->data;
            } else {
                $result['status'] = 2;
                $result['message'] = $res->message;
            }

            return $result;
        }
    }

}

?>
