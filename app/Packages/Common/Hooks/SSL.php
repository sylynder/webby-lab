<?php

function force_ssl() {

    app()->config->config['base_url'] = str_replace('http://', 'https://', app()->config->config['base_url']);

    if ($_SERVER['SERVER_PORT'] != 443) redirect(app()->uri->uri_string());
    
}
