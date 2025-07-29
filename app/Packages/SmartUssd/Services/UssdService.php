<?php 

use Base\Services\Service;

class UssdService extends Service 
{

    private $rawData;
    private $track;
    private $progress;
    private $transaction;

    public $msisdn;
    public $sessionId;
    public $networkId;
    public $mode;
    public $userdata;
    public $username;
    public $trafficId;

    public $responseData;
    public $nickname;

    protected $caret = "^";
    protected $pipe = "|";

    public const STARTMODE = 'START';
    public const MOREMODE = 'MORE';
    public const ENDMODE = 'END';

    public const USSD_JOB_TYPE = 'job';
    public const USSD_LABOUR_TYPE = 'labour';
    public const USSD_ENQUIRY_TYPE = 'enquiry';

    public function __construct($request = [])
    {

        $this->use->helper('SmartUssd/Ussd');

        if (!empty($request)) {
            $this->setRawData($request);
        }
        
    }

    public function setRawData($request = [])
    {
        $this->rawData = (object) clean($request);   
        $this->prepare();
    }

    private function prepare()
    {
        $request = $this->rawData;

        if (empty($request)) {
            return false;
        }

        $this->msisdn = $request->msisdn ?? '';
        $this->sessionId = $request->sessionid ?? '';
        $this->networkId = $request->network ?? '';
        $this->mode = $request->mode ?? '';
        $this->userdata = $request->userdata ?? '';
        $this->username = $request->username ?? '';
        $this->trafficId = $request->trafficid ?? '';
        
        // $MSISDN =  $_GET['msisdn'];
        // $SESSION_ID =$_GET['sessionid'];
        // $NETWORKID = $_GET['network'];
        // $MODE = $_GET['mode'];
        // $DATA = $_GET['userdata'];

        // $USERNAME= $_GET['username'];

        // $TRAFFIC_ID= $_GET['trafficid'];

        // $TIME=date("Y/m/d h:i:s");
        // $today=date("Y-m-d");
        // $RESPONSE_DATA = "";
        // $mobile_moneyApi="https://api.reddeonline.com/v1/receive";
        // $nickname="bakeside";

    }

    public function welcomeMessage($name = "Nnoboa HR")
    {
        return "Welcome to ".$name.".^Select an Option:^^1. For Jobs ^2. For Labour ^3. For Enquiries";
    }

    public function responseData($message, $mode = UssdService::MOREMODE)
    {
        $this->responseData = "$this->networkId|$mode|$this->msisdn|$this->sessionId|$message|$this->username |$this->trafficId";
        return htmlspecialchars($this->responseData);
    }

    public function process($track = ZERO)
    {

        if ($this->mode == strtolower(UssdService::STARTMODE)) :
            
            $message = $this->welcomeMessage();

            Ussd::deleteTracking($this->msisdn);

            $this->rawData->userdata = ONE;
            $this->rawData->track = ONE;

            Ussd::insertTracking($this->rawData);

            echo $this->responseData($message);

            Ussd::deleteProgress($this->msisdn);

            Ussd::insertProgress($this->rawData);

        else :

            $track = Ussd::getTracking($this->msisdn);
            $trackId = $track->track ?? '';
            $progress = Ussd::getProgress($this->msisdn);

            $option = $progress->option ?? '';
            
            switch ($trackId) {

                case '1':
                    
                    if ($this->userdata == ONE) :

                        $this->rawData->track = TWO;
                        
                        Ussd::updateTracking($this->msisdn, $this->rawData);
                        
                        $message = "Seeker Details: ^^Enter Full Name: ";
                        echo $this->responseData($message);

                        $this->rawData->option = 'job';
                        Ussd::updateProgress('option', $this->rawData, $this->msisdn);

                        $this->rawData->ussd_type = 'job';
                        Ussd::updateProgress('ussd_type', $this->rawData, $this->msisdn);

                    elseif ($this->userdata == TWO) :

                        $this->rawData->track = TWO;
                        
                        Ussd::updateTracking($this->msisdn, $this->rawData);
                        
                        $message = "Labour Details: ^^Enter Company Name: ";
                        echo $this->responseData($message);

                        $this->rawData->option = 'labour';
                        Ussd::updateProgress('option', $this->rawData, $this->msisdn);

                        $this->rawData->ussd_type = 'labour';
                        Ussd::updateProgress('ussd_type', $this->rawData, $this->msisdn);

                    elseif ($this->userdata == THREE) :

                        $this->rawData->track = TWO;
                        
                        Ussd::updateTracking($this->msisdn, $this->rawData);
                        
                        $message = "Enquiry Details: ^^ Enter Your Name: ";
                        echo $this->responseData($message);

                        $this->rawData->option = 'enquirer';
                        Ussd::updateProgress('option', $this->rawData, $this->msisdn);

                        $this->rawData->ussd_type = 'enquirer';
                        Ussd::updateProgress('ussd_type', $this->rawData, $this->msisdn);

                    endif;

                break;

                case '2':

                    if ($option == 'job') :

                        $this->rawData->track = THREE;
                        Ussd::updateTracking($this->msisdn, $this->rawData);
                        
                        $message = "Enter Your Location:";
                        echo $this->responseData($message);
                        
                        $this->rawData->name = $this->userdata;
                        Ussd::updateProgress('name', $this->rawData, $this->msisdn);

                    elseif ($option == 'labour') :

                        $this->rawData->track = THREE;
                        Ussd::updateTracking($this->msisdn, $this->rawData);
                        
                        $message = "Enter Company Location:";
                        echo $this->responseData($message);
                        
                        $this->rawData->company_name = $this->userdata;
                        Ussd::updateProgress('company_name', $this->rawData, $this->msisdn);

                    elseif ($option == 'enquirer') :

                        $this->rawData->track = THREE;
                        Ussd::updateTracking($this->msisdn, $this->rawData);
                        
                        $message = "Enter Your Location:";
                        echo $this->responseData($message);
                        
                        $this->rawData->name = $this->userdata;
                        Ussd::updateProgress('name', $this->rawData, $this->msisdn);
                        
                    endif;

                break;

                case '3':

                    if ($option == 'job') :

                        $this->rawData->track = FOUR;
                        Ussd::updateTracking($this->msisdn, $this->rawData);
                        
                        $message = "Enter Your Phone Number:";
                        echo $this->responseData($message);
                        
                        $this->rawData->location = $this->userdata;
                        Ussd::updateProgress('location', $this->rawData, $this->msisdn);

                    elseif ($option == 'labour') :

                        $this->rawData->track = FOUR;
                        Ussd::updateTracking($this->msisdn, $this->rawData);
                        
                        $message = "Enter Company Phone Number:";
                        echo $this->responseData($message);
                        
                        $this->rawData->location = $this->userdata;
                        Ussd::updateProgress('location', $this->rawData, $this->msisdn);

                    elseif ($option == 'enquirer') :

                        $this->rawData->track = FOUR;
                        Ussd::updateTracking($this->msisdn, $this->rawData);
                        
                        $message = "Enter Your Phone Number:";
                        echo $this->responseData($message);
                        
                        $this->rawData->location = $this->userdata;
                        Ussd::updateProgress('location', $this->rawData, $this->msisdn);

                    endif;

                break;

                case '4':

                    if ($option == 'job') :

                        $this->rawData->track = FIVE;
                        Ussd::updateTracking($this->msisdn, $this->rawData);
                        
                        $message = "Enter Your Gender:";
                        echo $this->responseData($message);
                        
                        $this->rawData->phone_number = $this->userdata;
                        Ussd::updateProgress('phone_number', $this->rawData, $this->msisdn);
                    
                    elseif ($option == 'labour') :

                        $this->rawData->track = FIVE;
                        Ussd::updateTracking($this->msisdn, $this->rawData);
                        
                        $message = "Enter Name Of Contact Person:";
                        echo $this->responseData($message);
                        
                        $this->rawData->company_phone = $this->userdata;
                        Ussd::updateProgress('company_phone', $this->rawData, $this->msisdn);

                    elseif ($option == 'enquirer') :

                        $this->rawData->track = FIVE;
                        Ussd::updateTracking($this->msisdn, $this->rawData);
                        
                        $message = "Enter Your Comments:";
                        echo $this->responseData($message);
                        
                        $this->rawData->phone_number = $this->userdata;
                        Ussd::updateProgress('phone_number', $this->rawData, $this->msisdn);

                    endif;

                break;

                case '5':

                    if ($option == 'job') :

                        $this->rawData->track = SIX;
                        Ussd::updateTracking($this->msisdn, $this->rawData);
                        
                        $message = "Enter Your Age:";
                        echo $this->responseData($message);
                        
                        $this->rawData->gender = $this->userdata;
                        Ussd::updateProgress('gender', $this->rawData, $this->msisdn);
                        
                    elseif ($option == 'labour') :

                        $this->rawData->track = SIX;
                        Ussd::updateTracking($this->msisdn, $this->rawData);
                        
                        $message = "Enter Phone Number Of Contact Person:";
                        echo $this->responseData($message);
                        
                        $this->rawData->contact_person = $this->userdata;
                        Ussd::updateProgress('contact_person', $this->rawData, $this->msisdn);
                    
                    elseif ($option == 'enquirer') :

                        $this->rawData->track = FIVE;
                        Ussd::updateTracking($this->msisdn, $this->rawData);

                        $message = "Thank you for your information, an agent will contact you shortly";
                        echo $this->responseData($message, UssdService::ENDMODE);
                        
                        $this->rawData->comments = $this->userdata;
                        Ussd::updateProgress('comments', $this->rawData, $this->msisdn);

                        $enquirer = Ussd::getProgress($this->msisdn);

                        $saved = Ussd::saveEnquirer($enquirer);

                        if ($saved) :

                            // message to user
                            $message = "Hello ".ucwords($enquirer->name).", Your information has been received, an agent will contact you shortly";
                            Ussd::smsToUser($progress->msisdn, $message);

                            // message to staff
                            $message = "Hello Nnoboa, an Enquirer has just sent in their details.\n";
                            $message .="Name: {$enquirer->name},\n";
                            $message .="Phone Number: {$enquirer->phone_number},\n";
                            $message .="Location: {$enquirer->location},\n";
                            $message .="Comments: {$enquirer->comments}";
                            
                            Ussd::smsToStaff(['0243721004', '0209296301', '0543528299', '0245296936', '0248052496'], $message);
                        endif;

                    endif;
                    
                break;

                case '6':

                    if ($option == 'job') :

                        if (is_numeric($this->userdata)) :
                
                            $this->rawData->track = SEVEN;
                            Ussd::updateTracking($this->msisdn, $this->rawData);
                            
                            $message = "Enter Your Qualification:";
                            echo $this->responseData($message);
                            
                            $this->rawData->age = $this->userdata;
                            Ussd::updateProgress('age', $this->rawData, $this->msisdn);
                            
                        else :

                            $this->rawData->track = SIX;
                            Ussd::updateTracking($this->msisdn, $this->rawData);
                            
                            $message = "Invalid Age Input, Please Enter A Valid Age (number) :";
                            echo $this->responseData($message);
                            
                            $this->rawData->age = $this->userdata;
                            Ussd::updateProgress('age', $this->rawData, $this->msisdn);

                        endif;

                    elseif ($option == 'labour') :

                        $this->rawData->track = SEVEN;
                        Ussd::updateTracking($this->msisdn, $this->rawData);
                        
                        $message = "Enter Position Of Contact Person:";
                        echo $this->responseData($message);
                        
                        $this->rawData->phone_number = $this->userdata;
                        Ussd::updateProgress('phone_number', $this->rawData, $this->msisdn);

                    endif;
                    
                break;

                case '7':

                    if ($option == 'job') :

                        $this->rawData->track = SEVEN;
                        Ussd::updateTracking($this->msisdn, $this->rawData);
                        
                        $message = "Thank you for your information, an agent will contact you shortly";
                        echo $this->responseData($message, UssdService::ENDMODE);
                        
                        $this->rawData->qualification = $this->userdata;
                        Ussd::updateProgress('qualification', $this->rawData, $this->msisdn);

                        $seeker = Ussd::getProgress($this->msisdn);

                        $saved = Ussd::saveJobSeeker($seeker);

                        if ($saved) :
                            
                            // message to user
                            $message = "Hello ".ucwords($seeker->name).", Your information has been received, an agent will contact you shortly";
                            Ussd::smsToUser($progress->msisdn, $message);

                            // message to staff
                            $message = "Hello Nnoboa, a Job Seeker has just sent in their details.\n";
                            $message .="Name: {$seeker->name},\n";
                            $message .="Phone Number: {$seeker->phone_number},\n";
                            $message .="Age: {$seeker->age},\n";
                            $message .="Location: {$seeker->location},\n";
                            $message .="Qualification: {$seeker->qualification}";
                            
                            Ussd::smsToStaff(['0243721004', '0209296301', '0543528299', '0245296936', '0248052496'], $message);
                        endif;

                    elseif ($option == 'labour') :

                        $this->rawData->track = EIGHT;
                        Ussd::updateTracking($this->msisdn, $this->rawData);
                        
                        $message = "Enter Job Position To Hire:";
                        echo $this->responseData($message);
                        
                        $this->rawData->position = $this->userdata;
                        Ussd::updateProgress('position', $this->rawData, $this->msisdn);

                    endif;
                    
                break;

                case '8':

                    if ($option == 'labour') :

                        $this->rawData->track = EIGHT;
                        Ussd::updateTracking($this->msisdn, $this->rawData);
                        
                        $message = "Thank you for your information, an agent will contact you shortly";
                        echo $this->responseData($message, UssdService::ENDMODE);
                        
                        $this->rawData->position_to_hire = $this->userdata;
                        Ussd::updateProgress('position_to_hire', $this->rawData, $this->msisdn);

                        $employer = Ussd::getProgress($this->msisdn);

                        $saved = Ussd::saveJobEmployer($employer);

                        if ($saved) :

                            // message to user
                            $message = "Hello ".ucwords($employer->company_name).", Your information has been received, an agent will contact you shortly";
                            Ussd::smsToUser($progress->msisdn, $message);

                            // message to staff
                            $message = "Hello Nnoboa, an Employer has just sent in their details.\n";

                            $message .="Company: {$employer->company_name},\n";
                            $message .="Contact Person: {$employer->contact_person},\n";
                            $message .="Contact Number: {$employer->phone_number},\n";
                            $message .="Location: {$employer->location},\n";
                            $message .="Hiring: {$employer->position_to_hire}";
                            
                            Ussd::smsToStaff(['0243721004', '0209296301', '0543528299', '0245296936', '0248052496'], $message);
                        endif;
                        
                    endif;

                default:
                    // $message = "Please short code again to start all over";
                    // echo $this->responseData($message);
                break;
            }

        endif;

    }
    
}
/* End of UssdService file */
