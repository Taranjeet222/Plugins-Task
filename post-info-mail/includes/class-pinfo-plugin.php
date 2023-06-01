<?php

class Pinfo_Plugin{
    public function __construct(){
        add_action('wp_enqueue_scripts', array($this, 'enqueue_stylesheet'));
        add_action('init', array($this,'schedule_mail'));
        add_action('mail_post_summary',array($this,'send_mail'));
        add_filter('cron_schedules',array($this,'add_schedule'));
    }

    public function add_schedule($arr){
        $arr['minute'] = array(
            'interval' => 1*MINUTE_IN_SECONDS,
            'display' => __('ones minutes','post-info-mail')
        );
        return $arr;
    }
    public function schedule_mail(){
        if(!wp_next_scheduled('mail_post_summary'))
        {
            $response = wp_schedule_event(time(),'minute','mail_post_summary',array(),true);
            error_log(print_r($response,1));
        }
    }

    public function send_mail(){
        $today = date('Y-m-d');         
        $year = date('Y');          
        $month = date('m');
        $day = date('d');
        $args = array(  
            'date_query' => array(    
                'day' => $day,
                'month' => $month,
                'year' => $year
            ),
            'posts_per_page' => -1,
        );
        $query = new WP_Query($args);
        if($query->have_posts())
        {
            $email_subject = 'Daily Posts - ' . $today;
            $email_body = "";
            while($query->have_posts())
            {
                $query->the_post();
                $title = get_the_title();
                $url = get_permalink();
                $meta_title = get_post_meta(get_the_ID(), 'meta_title', true);
                $meta_description = get_post_meta(get_the_ID(), 'meta_description', true);
                $meta_keywords = get_post_meta(get_the_ID(), 'meta_keywords', true);
                $google_pagespeed_score = get_post_meta(get_the_ID(), 'google_pagespeed_score', true);
                $email_body .= '<div style="background-color: #f5f5f5; padding: 10px; margin-bottom: 10px; width: 70%; margin:auto; text-align:center;">';
                $email_body .= '<h3 style="font-weight: bold; color: #333;">Post Title: ' . $title . '</h3>';
                $email_body .= '<p style="margin-top: 5px; margin-bottom: 10px;"><strong>Post URL:</strong> <a href="' . $url . '" style="color: #007bff;">' . $url . '</a></p>';
                $email_body .= '<p style="font-weight: bold; color: #333;">Meta Title: ' . $meta_title . '</p>';
                $email_body .= '<p>Meta Description: ' . $meta_description . '</p>';
                $email_body .= '<p>Meta Keywords: ' . $meta_keywords . '</p>';
                $email_body .= '<p>Google PageSpeed Score: ' . $google_pagespeed_score . '</p>';
                $email_body .= '</div>';
                $email_body .= '<br>'; 


            }
            $admin_email = get_option('admin_email');
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
            $mail_sent = wp_mail($admin_email, $email_subject, $email_body, $headers);
            if ($mail_sent) {
                echo "Mail has been sent successfully!";
            } else {
                echo "Message could not be sent.";
            }
        }
    }

    public function enqueue_stylesheet() {
        wp_enqueue_style(
            'pinfo-style',
            plugins_url('pinfo-style.css', __FILE__)
        );
    }
}