<?php

use Base\Console\Command;

use React\EventLoop\Factory;
use React\EventLoop\Loop; // Correct namespace for the modern way
use React\EventLoop\LoopInterface; // Use LoopInterface for type hinting
use React\Promise\Promise;

use React\Promise\PromiseInterface; // Import the PromiseInterface

// PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

class EmailSenderCommand extends Command
{

    protected LoopInterface $loop; // Type-hint with the correct interface
    protected int $emailsSentCount = 0;

    public function __construct()
    {
        parent::__construct();
        $this->use->database();
        // Correct way to get the default event loop instance
        $this->loop = Loop::get();
    }

    public function index()
    {
        $this->info('Email Sender Command is running...');
    }

    /**
     * Entry point for the `webby send:emails` command.
     */
    public function processQueue()
    {
        dd(app()->db);
        // dd($this->use);
        echo "Starting email queue processor (ReactPHP)..." . PHP_EOL;
        echo "Press Ctrl+C to stop." . PHP_EOL;

        // --- Important: Bootstrap Webby/CodeIgniter environment ---
        // This remains the critical part for you to adapt for Webby.
        // As we discussed, look into how your other console commands (like migrations)
        // manage to access CodeIgniter services. You'll likely need to replicate that setup here.
        // For example, if your base `Console` class already sets up CI, then you're good.
        // Otherwise, you might need something like:
        /*
        if (!function_exists('get_instance')) {
            // Path to your main CodeIgniter entry file that defines get_instance()
            // This is just a placeholder, find the correct bootstrap for Webby CLI
            require_once FCPATH . '../vendor/sylynder/engine/CodeIgniter/Framework/core/CodeIgniter.php'; // Adjust path
        }
        $CI = &get_instance(); // Get the CI super object
        // Now you can load CI libraries, models etc.
        // $CI->load->database();
        // $CI->load->library('email');
        */
        // -----------------------------------------------------------

        $this->setupEmailProcessor();

        // Run the ReactPHP event loop
        $this->loop->run();
    }

    protected function setupEmailProcessor()
    {
        // Simulate an email queue (in a real app, this would be a DB table or Redis queue)
        $emailQueue = [
            ['id' => 1, 'to' => 'user1@example.com', 'subject' => 'Welcome!', 'body' => 'Hello from Webby!'],
            ['id' => 2, 'to' => 'user2@example.com', 'subject' => 'Newsletter', 'body' => 'Check out our latest updates.'],
            ['id' => 3, 'to' => 'user3@example.com', 'subject' => 'Important Notice', 'body' => 'Action required.'],
            ['id' => 4, 'to' => 'user4@example.com', 'subject' => 'Follow Up', 'body' => 'Just checking in!'],
            ['id' => 5, 'to' => 'user5@example.com', 'subject' => 'Exclusive Offer', 'body' => 'Limited time only.'],
        ];

        // This timer will check the queue every 2 seconds
        $this->loop->addPeriodicTimer(2, function () use (&$emailQueue) {
            $batchSize = 2; // Process 2 emails at a time
            $emailsToProcess = array_splice($emailQueue, 0, $batchSize);

            if (empty($emailsToProcess)) {
                echo "No more emails in queue. Waiting for new tasks..." . PHP_EOL;
                return;
            }

            foreach ($emailsToProcess as $emailData) {
                echo sprintf("Attempting to send email ID %d to %s...\n", $emailData['id'], $emailData['to']);
                $this->sendEmailAsync($emailData)
                    ->then(function ($result) use ($emailData) {
                        echo sprintf("Email ID %d to %s sent successfully. (Total sent: %d)\n", $emailData['id'], $emailData['to'], ++$this->emailsSentCount);
                    })
                    ->catch(function (\Throwable $e) use ($emailData) {
                        echo sprintf("Failed to send email ID %d to %s: %s\n", $emailData['id'], $emailData['to'], $e->getMessage());
                    });
            }
        });

        // Add a shutdown handler for graceful exit
        $this->loop->addSignal(SIGINT, function (int $signal) {
            echo PHP_EOL . "SIGINT received. Shutting down email processor gracefully..." . PHP_EOL;
            $this->loop->stop();
        });
        $this->loop->addSignal(SIGTERM, function (int $signal) {
            echo PHP_EOL . "SIGTERM received. Shutting down email processor gracefully..." . PHP_EOL;
            $this->loop->stop();
        });
    }

    /**
     * Sends an email asynchronously using ReactPHP's promise,
     * leveraging PHPMailer.
     *
     * @param array $emailData
     * @return PromiseInterface
     */
    protected function sendEmailAsync(array $emailData): PromiseInterface
    {
        return new Promise(function (callable $resolve, callable $reject) use ($emailData) {
            $delay = mt_rand(1000, 3000) / 1000; // Simulate network delay

            $this->loop->addTimer($delay, function () use ($emailData, $resolve, $reject) {
                try {
                    // Instantiate PHPMailer
                    // Important: Configure PHPMailer's SMTP settings here or via a dedicated config file.
                    // For Mailpit, you'd typically set protocol to SMTP, host to localhost, port to Mailpit's SMTP port (default 1025)
                    $mail = new PHPMailer(true); // `true` enables exceptions

                    // Server settings for Mailpit - SIMPLIFIED FOR LOCAL DEVELOPMENT
                    $mail->isSMTP();
                    $mail->Host       = 'localhost'; // Mailpit's host
                    $mail->Port       = 1025;        // Mailpit's SMTP port (default)
                    $mail->SMTPAuth   = false;       // Mailpit typically doesn't require auth
                    $mail->SMTPSecure = false;       // <--- CHANGE: Disable encryption for local Mailpit
                    $mail->SMTPAutoTLS = false;      // <--- ADD: Disable automatic TLS for local Mailpit
                    // $mail->SMTPOptions removed as it's often not needed when SMTPSecure is false
                    // $mail->SMTPOptions = [ // Often needed for local SMTP like Mailpit if no TLS cert is present
                    //     'ssl' => [
                    //         'verify_peer'       => false,
                    //         'verify_peer_name'  => false,
                    //         'allow_self_signed' => true
                    //     ]
                    // ];
                    // Recipients
                    $mail->setFrom('no-reply@yourwebbyapp.com', 'Webby Mailer');
                    $mail->addAddress($emailData['to']); // Add a recipient

                    // Content
                    $mail->isHTML(false); // Set email format to plain text (or true for HTML)
                    $mail->Subject = $emailData['subject'];
                    $mail->Body    = $emailData['body'];
                    $mail->AltBody = strip_tags($emailData['body']);

                    $mail->send();
                    $resolve(true);

                } catch (PHPMailerException $e) {
                    $reject(new \Exception("PHPMailer failed for ID " . $emailData['id'] . ": " . $e->getMessage() . " Mailer Error: " . $mail->ErrorInfo));
                } catch (\Throwable $e) { // Catch any other unexpected errors
                    $reject(new \Exception("Unexpected error during email sending for ID " . $emailData['id'] . ": " . $e->getMessage()));
                }
            });
        });
    }

    // protected function setupEmailProcessor()
    // {
    //     // Simulate an email queue (in a real app, this would be a DB table or Redis queue)
    //     $emailQueue = [
    //         ['id' => 1, 'to' => 'user1@example.com', 'subject' => 'Welcome!', 'body' => 'Hello from Webby!'],
    //         ['id' => 2, 'to' => 'user2@example.com', 'subject' => 'Newsletter', 'body' => 'Check out our latest updates.'],
    //         ['id' => 3, 'to' => 'user3@example.com', 'subject' => 'Important Notice', 'body' => 'Action required.'],
    //         ['id' => 4, 'to' => 'user4@example.com', 'subject' => 'Follow Up', 'body' => 'Just checking in!'],
    //         ['id' => 5, 'to' => 'user5@example.com', 'subject' => 'Exclusive Offer', 'body' => 'Limited time only.'],
    //     ];

    //     // This timer will check the queue every 2 seconds
    //     $this->loop->addPeriodicTimer(2, function () use (&$emailQueue) {
    //         $batchSize = 2; // Process 2 emails at a time
    //         $emailsToProcess = array_splice($emailQueue, 0, $batchSize);

    //         if (empty($emailsToProcess)) {
    //             echo "No more emails in queue. Waiting for new tasks..." . PHP_EOL;
    //             return;
    //         }

    //         foreach ($emailsToProcess as $emailData) {
    //             echo sprintf("Attempting to send email ID %d to %s...\n", $emailData['id'], $emailData['to']);
    //             $this->sendEmailAsync($emailData)
    //                 ->then(function ($result) use ($emailData) {
    //                     echo sprintf("Email ID %d to %s sent successfully. (Total sent: %d)\n", $emailData['id'], $emailData['to'], ++$this->emailsSentCount);
    //                 })
    //                 // CHANGE: Using catch() instead of otherwise()
    //                 ->catch(function (\Throwable $e) use ($emailData) {
    //                     echo sprintf("Failed to send email ID %d to %s: %s\n", $emailData['id'], $emailData['to'], $e->getMessage());
    //                 });
    //         }
    //     });

    //     // Add a shutdown handler for graceful exit
    //     $this->loop->addSignal(SIGINT, function (int $signal) {
    //         echo PHP_EOL . "SIGINT received. Shutting down email processor gracefully..." . PHP_EOL;
    //         $this->loop->stop();
    //     });
    //     $this->loop->addSignal(SIGTERM, function (int $signal) {
    //         echo PHP_EOL . "SIGTERM received. Shutting down email processor gracefully..." . PHP_EOL;
    //         $this->loop->stop();
    //     });
    // }

    // /**
    //  * Simulates sending an email asynchronously using ReactPHP's promise.
    //  *
    //  * @param array $emailData
    //  * @return PromiseInterface
    //  */
    // protected function sendEmailAsync(array $emailData): PromiseInterface
    // {
    //     return new Promise(function (callable $resolve, callable $reject) use ($emailData) {
    //         $delay = mt_rand(1000, 3000) / 1000;

    //         $this->loop->addTimer($delay, function () use ($emailData, $resolve, $reject) {
    //             // IMPORTANT: This is where you would integrate with CodeIgniter's email library.
    //             // Assuming CI is properly bootstrapped and email library loaded and accessible via $this.
    //             /*
    //             try {
    //                 // Check if CI's email library is loaded and available through your console class
    //                 if (isset($this->email) && $this->email instanceof \CI_Email) {
    //                     $this->email->clear();
    //                     $this->email->to($emailData['to']);
    //                     $this->email->subject($emailData['subject']);
    //                     $this->email->message($emailData['body']);

    //                     if ($this->email->send()) {
    //                         $resolve(true);
    //                     } else {
    //                         $reject(new \Exception("CI Email sending failed: " . $this->email->print_debugger()));
    //                     }
    //                 } else {
    //                     $reject(new \Exception("CodeIgniter Email library not available in console context."));
    //                 }
    //             } catch (\Throwable $e) { // Catch Throwable here as well
    //                 $reject($e);
    //             }
    //             */

    //             // For now, just simulate success or failure
    //             if (rand(0, 10) < 9) { // 90% chance of success
    //                 $resolve(true);
    //             } else {
    //                 $reject(new \Exception("Simulated email sending failure for ID " . $emailData['id']));
    //             }
    //         });
    //     });
    // }

    // protected function sendEmailAsync(array $emailData): PromiseInterface
    // {
    //     return new Promise(function (callable $resolve, callable $reject) use ($emailData) {
    //         $delay = mt_rand(1000, 3000) / 1000;

    //         $this->loop->addTimer($delay, function () use ($emailData, $resolve, $reject) {
    //             // --- ACTUAL CI_Email INTEGRATION START ---
    //             try {
    //                 // How do your console commands normally access CI libraries/models?
    //                 // Option 1: If $this (EmailSender instance) is effectively a CI_Controller:
    //                 if (method_exists($this, 'load') && property_exists($this, 'email')) {
    //                     $this->use->library('email'); // Load the email library
    //                     $this->email->clear(); // Always clear previous email data

    //                     $this->email->from('no-reply@yourwebbyapp.com', 'Webby Mailer'); // Set your FROM address
    //                     $this->email->to($emailData['to']);
    //                     $this->email->subject($emailData['subject']);
    //                     $this->email->message($emailData['body']);

    //                     if ($this->email->send()) {
    //                         $resolve(true);
    //                     } else {
    //                         // CI_Email::send() returns false on failure. Use print_debugger() for details.
    //                         $reject(new \Exception("CI Email sending failed: " . $this->email->print_debugger()));
    //                     }
    //                 } else {
    //                     // Option 2: If get_instance() is available but not attached to $this
    //                     // This implies a manual CI setup in your console's bootstrap
    //                     $CI = app();
    //                     if (isset($CI->email) && $CI->email instanceof \CI_Email) {
    //                         $CI->email->clear();
    //                         $CI->email->from('no-reply@yourwebbyapp.com', 'Webby Mailer'); // Set your FROM address
    //                         $CI->email->to($emailData['to']);
    //                         $CI->email->subject($emailData['subject']);
    //                         $CI->email->message($emailData['body']);

    //                         if ($CI->email->send()) {
    //                             $resolve(true);
    //                         } else {
    //                             $reject(new \Exception("CI Email sending failed (via get_instance): " . $CI->email->print_debugger()));
    //                         }
    //                     } else {
    //                         $reject(new \Exception("CodeIgniter Email library not accessible."));
    //                     }
    //                 }
    //             } catch (\Throwable $e) { // Catch any errors during the CI email process
    //                 $reject(new \Exception("Error during CI Email setup/send: " . $e->getMessage()));
    //             }
    //             // --- ACTUAL CI_Email INTEGRATION END ---

    //             // Remove the simulated success/failure once you integrate CI_Email
    //             /*
    //         if (rand(0, 10) < 9) {
    //             $resolve(true);
    //         } else {
    //             $reject(new \Exception("Simulated email sending failure for ID " . $emailData['id']));
    //         }
    //         */
    //         });
    //     });
    // }
}
/* End of EmailSenderCommand file */
// Make sure to register this command in your routes/console.php