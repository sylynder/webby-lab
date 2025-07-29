<?php 

class Ussd 
{
	public static function getTracking($msisdn)
    {
        return use_table('ussd_tracking')
            ->where('msisdn', $msisdn)
            ->first();
    }

    public static function insertTracking($data)
    {
        return use_table('ussd_tracking')->save([
            'msisdn' => $data->msisdn,
            'session_id' => $data->sessionid,
            'mode' => $data->mode,
            'username' => $data->username,
            'time' => datetime(),
            'userdata' => $data->userdata,
            'track' => $data->track,
        ]);
    }

    public static function updateTracking($msisdn, $data)
    {
        return use_table('ussd_tracking')
            ->simpleUpdate(['msisdn' => $msisdn],
                [
                    'session_id' => $data->sessionid,
                    'mode' => $data->mode,
                    'username' => $data->username,
                    'time' => datetime(),
                    'userdata' => $data->userdata,
                    'track' => $data->track,
                ]);
    }

    public static function deleteTracking($msisdn)
    {
        return use_table('ussd_tracking')
            ->where('msisdn', $msisdn)
            ->delete('msisdn', $msisdn);
    }

    public static function getProgress($msisdn)
    {
        return use_table('ussd_progress')
            ->where('msisdn', $msisdn)
            ->first();
    }

    public static function insertProgress($data)
    {
        return use_table('ussd_progress')->save([
            'msisdn' => $data->msisdn ?? '',
            'session_id' => $data->sessionid ?? '',
            'option' => $data->option ?? '',
            'ussd_type' => $data->ussd_type ?? '',
            'network' => $data->network ?? '',
            'name' => $data->name ?? '',
            'company_name' => $data->company_name ?? '',
            'contact_person' => $data->contact_person ?? '',
            'company_phone' => $data->company_phone ?? '',
            'location' => $data->location ?? '',
            'phone_number' => $data->phone_number ?? '',
            'age' => $data->age ?? '',
            'gender' => $data->gender ?? '',
            'qualification' => $data->qualification ?? '',
            'position' => $data->position ?? '',
            'position_to_hire' => $data->position_to_hire ?? '',
            'comments' => $data->comments ?? '',
        ]);
    }

    public static function updateProgress($field, $data, $msisdn)
    {

        $query = use_table('ussd_progress');

        $query->primaryKey = 'msisdn';

        switch ($field) {
            case 'option':
                return $query
                    ->simpleUpdate(['msisdn' => $msisdn],
                    [
                            'session_id' => $data->sessionid,
                            'option' => $data->option,
                    ]);

                    // ->set('session_id', $data->sessionid)
                    // ->set('option', $data->option)
                    // ->where('msisdn', $msisdn)
                    // ->update();
            break;
            case $field:
                return $query->simpleUpdate(['msisdn' => $msisdn],
                    [
                            'session_id' => $data->sessionid,
                            "{$field}" => $data->{$field},
                    ]);


                    // ->set('session_id', $data->sessionid)
                    // ->set($field, $data->{$field})
                    // ->where('msisdn', $msisdn)
                    // ->update();
            break;
            
            default:
                return false;
            break;
        }
    }

    public static function deleteProgress($msisdn)
    {
        return use_table('ussd_progress')
            ->where('msisdn', $msisdn)
            ->delete('msisdn', $msisdn);
    }

    public static function saveJobSeeker($jobSeeker)
    {
        return use_table('ussd_job_seekers')->save([
            'msisdn'        => $jobSeeker->msisdn,
            'name'          => $jobSeeker->name,
            'gender'        => $jobSeeker->gender,
            'age'           => $jobSeeker->age,
            'location'      => $jobSeeker->location,
            'qualification' => $jobSeeker->qualification,
            'phone_number'  => $jobSeeker->phone_number,
            'network'       => $jobSeeker->network,
            'status'        => 'pending',
            'created_at'    => datetime()
        ]);
    }

    public static function saveJobEmployer($employer)
    {
        return use_table('ussd_employers')->save([
            'msisdn'           => $employer->msisdn,
            'company_name'     => $employer->company_name,
            'location'         => $employer->location,
            'contact_person'   => $employer->contact_person,
            'company_phone'    => $employer->company_phone,
            'phone_number'     => $employer->phone_number,
            'position'         => $employer->position,
            'position_to_hire' => $employer->position_to_hire,
            'network'          => $employer->network,
            'status'        => 'pending',
            'created_at'    => datetime()
        ]);
    }

    public static function saveEnquirer($enquirer)
    {
        return use_table('ussd_enquirers')->save([
            'msisdn'       => $enquirer->msisdn,
            'name' => $enquirer->name,
            'location'     => $enquirer->location,
            'phone_number' => $enquirer->phone_number,
            'comments'     => $enquirer->comments,
            'network'      => $enquirer->network,
            'status'        => 'pending',
            'created_at'    => datetime()
        ]);
    }

    public static function smsToUser($phoneNumber, $message, $type = 'Job Seeker')
    {
        app()->use->service('Frog/FrogService');
        
        // Use Frog Service (Wigal Frog)
        $frog = app('Frog/FrogService');

        $data = (object) [
            'to' => $phoneNumber,
            'title' => "USSD Message for ". $type,
            'message' => $message
        ];

        return $frog->sendSmsMessage($data);
    }

    public static function smsToStaff($phoneNumber, $message)
    {
        app()->use->service('Frog/FrogService');
        
        // Use Frog Service (Wigal Frog)
        $frog = app('Frog/FrogService');

        $data = (object) [
            'to' => $phoneNumber,
            'title' => "USSD Message for Staff",
            'message' => $message
        ];

        return $frog->sendSmsMessage($data);
    }
}
/* End of Ussd_helper file */
