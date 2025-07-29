<?php

namespace App\Packages\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use App\Packages\Mail\Traits\QueueMail;

class Mailer extends \CI_Email
{

	// Use queue
	use QueueMail;

	/**
	 * Mailer variable
	 *
	 * @var object
	 */
	public $mailer;

	/**
	 * Configuration variable
	 *
	 * @var array
	 */
	public $config = [];

	/**
	 * CodeIgniter variable
	 *
	 * @var object
	 */
	public $ci;

	/**
	 * Used as the User-Agent and X-Mailer headers' value.
	 *
	 * @var	string
	 */
	public $useragent	= 'WebbyMailer';

	public function __construct(array $config = []) 
	{
		$this->ci = app();
		$this->mailer = new PHPMailer(true);
		$this->config = $config;
	}

	/**
	 * Use CodeIgniter Database
	 *
	 * @return mixed
	 */
	public function useDb()
	{
		$this->ci->load->database();
		return $this;
	}

	/**
	 * Initialize email configuration
	 *
	 * @return bool|object
	 */
	public function initializeMail()
	{
		if (empty($this->config)) {
			return false;
		}

		$this->configureMail($this->config);
		return $this;
	}

	/**
	 * Email Server Configuration
	 * 
	 * Including debug_mode, smtp_host, username, 
	 * password, smtp_security, port
	 * 
	 * @param array $config
	 * @return object
	 */
	public function configureMail(array $config)
	{

		$config = (object)$config;

		//Set PHPMailer to use SMTP.
		$this->mailer->isSMTP(); 
		//Enable SMTP debugging. 
		$this->mailer->SMTPDebug = $config->smtp_debug;                                          
		//Set SMTP host name                          
		$this->mailer->Host = $config->smtp_host;
		//Set this to true if SMTP host requires authentication to send email
		$this->mailer->SMTPAuth = $config->smtp_auth;                          
		//Provide username and password     
		$this->mailer->Username = $config->smtp_user;                 
		$this->mailer->Password = $config->smtp_pass;
		//Set TCP port to connect to 
		$this->mailer->Port = $config->smtp_port;                            
		//If SMTP requires TLS encryption then set it
		$this->mailer->SMTPSecure = $config->smtp_security;

		return $this;         
	}
	
	/**
	 * Set Email From
	 *
	 * @param string $email
	 * @return object
	 */
	public function setEmailFrom($email) 
	{ 
		$this->mailer->From = $email;
		return $this;
	}
	
	/**
	 * Set Name
	 *
	 * @param string $name
	 * @return object
	 */
	public function setNameFrom($name)
	{
		$this->mailer->FromName = $name;
		return $this;
	}

	/**
	 * Send email to
	 *
	 * @param mixed $email
	 * @return object
	 */
	public function mailTo($email)
	{
			$to = $this->strToArray($email);
            $to = $this->cleanEmail($to);

            if ($this->validate) {
                $this->validate_email($to);
            }

            $i = 0;

            foreach ($to as $address) {

                $this->mailer->addAddress($address);

                $i++;
            }

		return $this; 
	}

	/**
	 * Email with name
	 *
	 * @param string $email
	 * @param string $name
	 * @return object
	 */
	public function mailToWithName($email, $name = '')
	{
		    $to = $this->strToArray($email);
            $names = $this->extractName($to);
            $to = $this->cleanEmail($to);
            
            if ($this->validate) {
                $this->validate_email($to);
            }

            $i = 0;

            if( ! empty($name)) {

            	$this->mailer->addAddress($email, $name);

            } else {
            	foreach ($to as $address) {

	                $this->mailer->addAddress($address, $names[$i]);

	                $i++;
            	}
            }
            
		return $this; 
	}

	/**
	 * Queue mail
	 *
	 * Add queue email to queue table.
	 * @return  mixed
	 */
	public function queue($subject = '', $mailTemplate = 'emails.default')
	{
		$subject = clean($subject);

		$mailTemplate = dotToslash($mailTemplate);

		$date = datetime(); // Function called from webby_helper.php

		$to = is_array($this->recipients) ? implode(", ", $this->recipients) : $this->recipients;
		
		$cc = is_array($this->ccs) ? implode(", ", $this->ccs) : $this->ccs;
		
		$bcc = is_array($this->bccs) ? implode(", ", $this->bccs) : $this->bccs;

		$mailDetails = [
			'subject' => $subject,
			'recipients' => $to,
			'cc' => $cc,
			'bcc' => $bcc,
			'message' => $this->content ?? $this->body,
			'template' => $mailTemplate,
			'headers' => serialize($this->headers),
			'status' => $this->status,
			'created_at' => $date,
			'updated_at' => $date
		];

		return $this->ci->db->insert($this->queueTable, $mailDetails);
	}
	
	/**
	 * Reply to email
	 *
	 * @param string $email
	 * @param string $nameOrTitle
	 * @return static
	 */
	public function replyTo($email, $nameOrTitle = '')
	{
		$this->mailer->addReplyTo($email, $nameOrTitle);
		return $this;
	}
	
	/**
	 * CC email addresses
	 *
	 * @param string|array $email
	 * @return object
	 */
	public function carbonCopy($email)
	{
		$cc = $this->strToArray($email);
        $names = $this->extractName($cc);
        $cc = $this->cleanEmail($cc);

        if ($this->validate) {
            $this->validate_email($cc);
        }

        $i = 0;

        foreach ($cc as $address) {

            $this->mailer->addCC($address, $names[$i]);

            $i++;
        }

        return $this;
	}
	
	/**
	 * BCC email addresses
	 *
	 * @param string|array $email
	 * @return object
	 */
	public function blindCarbonCopy($email)
	{
		$bcc = $this->strToArray($email);
        $names = $this->extractName($bcc);
        $bcc = $this->cleanEmail($bcc);

        if ($this->validate) {
            $this->validate_email($bcc);
        }

        $i = 0;

        foreach ($bcc as $address) {

            $this->mailer->addBCC($address, $names[$i]);

            $i++;
        }

		return $this;
	}
	
	/**
	 * Email Attachments
	 *
	 * @param string $filepath
	 * @return object
	 */
	public function mailAttachment($filepath)
	{
		$this->mailer->addAttachment($filepath);
		return $this;
	}
	
	/**
	 * Email Attachments with file names
	 * specified
	 *
	 * @param string $filepath
	 * @param string $filename
	 * @return object
	 */
	public function mailAttachmentWithName($filepath, $filename)
	{
		$this->mailer->addAttachment($filepath, $filename); // with Optional name and file extension
		return $this;
	}

	/**
	 * Is email an HTML Content
	 *
	 * @param boolean $bool
	 * @return object
	 */
	public function isHTML($bool)
	{	
		$this->mailer->isHTML($bool);
		return $this;
	}

	/**
	 * Set view to pass html template as body 
	 *
	 * @param string $mailTemplate
	 * @param string|array $emailData
	 * @return object
	 */
	public function setMailTemplate($mailTemplate, $emailData)
	{
		// $mailTemplate is the view's location or path

		if (is_json($emailData)) {
			$emailData = json_decode($emailData);
		}

		$this->body = $this->ci->load->view($mailTemplate, $emailData, true);
		return $this;
	}
	
	/**
	 * Set an email subject
	 *
	 * @param string $subject
	 * @return object
	 */
	public function setSubject($subject)
	{
		$subject = (string) $subject;

		$this->mailer->Subject = $subject;
		return $this;
	}
	
	/**
	 * Set an email body
	 *
	 * @param string $body
	 * @return object
	 */
	public function setBody($body)
	{
		$body = (string) $body;
		
		$this->mailer->Body = $body;
		return $this;
	}

	/**
	 * Undocumented function
	 *
	 * @param string $altBody
	 * @return object
	 */
	public function setAltBody($altBody)
	{
		$this->mailer->AltBody = $altBody;
		return $this;
	}
	
	/**
	 * Pass email body with subjet, body and altbody
	 *
	 * @param string $subject
	 * @param string $body
	 * @param string $altBody
	 * @return object
	 */
	public function setSubjectBody($subject, $body, $altBody='')
	{
		$subject = (string) $subject;
		$altBody = (string) $altBody;

		$this->mailer->Subject = $subject;
		$this->mailer->Body = $body;
		$this->mailer->AltBody = $altBody;
		return $this;
	}
	
	/**
	 * Execute and Send Email
	 *
	 * @return bool
	 */
	public function sendMail()
	{
		if(!$this->mailer->send())
		{
			return false;
		} 
		else
		{
			return true;
		}
	}

	/**
	 * Get errors if any
	 *
	 * @return mixed
	 */
	public function mailError()
	{
		return $this->mailer->ErrorInfo;
	}

	/**
	 * Fall on CodeIgniter Email
	 * Use it as a backup function
	 * @return bool|object
	 */
	public function useCiMail()
	{
		$config = $this->config;

		if (!$config) {
			return false;
		}

		$this->ci->load->library('email');
		$this->ci->email->initialize($config);
		return $this->ci->email;
	}

	/**
	 * @author Kwame Oteng Appiah-Nti
	 * The one implemented in Ivan Tcholakov My_Email class
	 * did not work based on my implementation
	 * 
	 * @param string|array $email
	 * @return string|array
	 */
	protected function extractName($email) {

        
        if (!is_array($email)) {

            $email = trim($email);
            $email = explode('@', $email);
            return $name = $email[0];

        }

        $result = [];

        foreach ($email as $address) {

            $address = trim($address);
            $email = explode('@', $address);
            $name = $email[0];

            $result[] = $name;
        }

        return $result;
	}
	
	/**
	 * Get domain name
	 *
	 * @param string $email
	 * @return string
	 */
	protected function prepareDomainName($email)
	{
		$domainName = substr(strrchr($email, "@"), 1);
		return $domainName;
	}

	/**
	 * Clean Extended Email Address: Joe Smith <joe@smith.com>
	 *
	 * @param	string|array $email
	 * @return	array
	 */
	private function cleanEmail($email) : array|string
	{
		if ( ! is_array($email)) {
			return preg_match('/\<(.*)\>/', $email, $match) ? $match[1] : $email;
		}

		$cleanEmail = [];

		foreach ($email as $address) {
			$cleanEmail[] = preg_match('/\<(.*)\>/', $address, $match) ? $match[1] : $address;
		}

		return $cleanEmail;
	}

	/**
	 * Prepare a string to array
	 *
	 * @param string $email
	 * @return array
	 */
	private function strToArray($email)
	{
		return $this->_str_to_array($email);
	}

	/**
	 * Custom Header
	 *
	 * @param array $headers
	 * @return void
	 * @todo Try and implement this
	 */
	protected function customHeader($headers = [])
	{
		
	}

	private function isMultiArray($array = [])
	{
		if ([] === $array) return false;

		return array_keys($array) !== range(0, count($array) - 1);
		
	}

	/**
	 * Get queued mails
	 *
	 * Get queue emails.
	 * @return  array|object
	 */
	public function getMailQueues($limit = null, $offset = null)
	{
		if ($this->status != false) {
			$this->ci->db->where('status', $this->status);
			$this->ci->db->or_where('status', $this->sendStatus);
		}

		$query = $this->ci->db->get("{$this->queueTable}", $limit, $offset);

		return $query->result();
	}

	/**
	 * Send queued mails
	 *
	 * Send queue emails.
	 * @return  void
	 */
	public function sendQueue($limit = null, $offset = null)
	{
		$status = $this->status;

		$this->setStatus($this->status);

		$mails = $this->getMailQueues($limit, $offset);

		$this->ci->db->where('status', $status);
		$this->ci->db->set('status',$this->sendStatus);
		$this->ci->db->set('date', datetime());
		$this->ci->db->update($this->queueTable);

		foreach ($mails as $mail) {
			$recipients = explode(", ", $mail->recipients);

			$cc = !empty($mail->cc) ? explode(", ", $mail->cc) : [];
			$bcc = !empty($mail->bcc) ? explode(", ", $mail->bcc) : [];

			$this->headers = unserialize($mail->headers);
			
			$this->mailTo($recipients);
			$this->carbonCopy($cc);
			$this->blindCarbonCopy($bcc);
			
			if ($mail->subject) {
				$this->setSubject($mail->subject);
			} 

			$body = ($mail->template != $this->defaultTemplate)
				? $this->setMailTemplate($mail->template, $mail->message) 
				: $this->setMailTemplate($this->defaultTemplate, $mail->message);

			$this->isHTML(true);

			$body = $body ?? $this->body;

			$this->setBody($body);
			// $this->setBody($this->body);

			if ($this->sendMail()) {
				$status = $this->sentStatus;
			} else {
				$status = $this->failStatus;
			}

			$this->ci->db->where('id', $mail->id);

			$this->ci->db->set('status', $status);
			$this->ci->db->set('date', datetime());
			$this->ci->db->update($this->queueTable);
		}
	}

	/**
	 * Retry failed emails
	 *
	 * Resend failed or expired emails
	 * @return void
	 */
	public function retryQueue($retries = 11)
	{
		$expire = (time() - $this->expiration);
		$dateExpire = date("Y-m-d H:i:s", $expire);

		$this->ci->db->set('status', $this->status);
		$this->ci->db->where("(date < '{$dateExpire}' AND status = '{$this->sendStatus}')");
		$this->ci->db->or_where("status = '{$this->failStatus}'");

		$this->ci->db->update($this->queueTable);
		
		log_message('debug', 'Email queue retrying...');
	}

	public function checkQueueRetries($retries = 11)
	{
		return $this->ci->db->select('retries, status')
					 ->where('retries != ', $retries)
					 ->get($this->queueTable);
	}

	public function updateQueueRetries($count)
	{

		// $this->ci->db->set('retries', $count);
		// $this->ci->db->where("(date < '{$dateExpire}' AND status = '{$this->sendStatus}')");
		// $this->ci->db->or_where("status = '{$this->failStatus}'");

		// $this->ci->db->update($this->queueTable);
	}

	/**
	 * Check if email really is active
	 *
	 * @param string $email
	 * @return mixed
	 */
    public function emailReallyExists($email)
	{
		// $vmail = new VerifyMail;
		// $vmail->setStreamTimeoutWait(20);
		// $vmail->Debug= true;
		// $vmail->Debugoutput= 'html';
		// $vmail->setEmailFrom('developerkwame@gmail.com');
		// return $vmail->check($email);
	}

}
