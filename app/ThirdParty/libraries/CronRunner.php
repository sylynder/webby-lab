<?php

class CronRunner {
  private $CI;

  public function __construct()
  {
    $this->CI =& get_instance();
    date_default_timezone_set('Asia/Dhaka');

  }

  private function calculateNextRun($obj)
  {
    return (time() + $obj->interval_sec);
  }

  public function run()
  {

    $date = new DateTime();
    $datetime = $date->format('Y-m-d H:i:s');

    $query = $this->CI->db->where('status', 1)->where("'".$datetime."'>= next_run_at OR next_run_at IS NULL", '', false)->from('dbt_cron')->get();


    if ($query->num_rows() > 0) {
      $env = getenv('CI_ENV');
      foreach ($query->result() as $row) {

      $tim = $this->calculateNextRun($row);
      $nextdatetime = date('Y-m-d H:i:s', $tim);


      $cmd = $row->command;
      $this->CI->db->set('next_run_at', "'".$nextdatetime."'", false)->where('id', $row->id)->update('dbt_cron');

      // create a new cURL resource
      $ch = curl_init();
      // set URL and other appropriate options
      curl_setopt($ch, CURLOPT_URL, $cmd);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      // grab URL and pass it to the browser
      curl_exec($ch);
      // close cURL resource, and free up system resources
      curl_close($ch);


      $this->CI->db->set('last_run_at', "'".$datetime."'", false)->where('id', $row->id)->update('dbt_cron');

      }

    }

  }

}