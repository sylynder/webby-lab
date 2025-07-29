<?php

use Base\Helpers\Uuid;
use Base\Http\HttpStatus;
use App\Packages\Auth\Middleware\ApiAuthMiddleware;

/**
 * ApiKeyController Controller
 * This is a basic Key Management REST controller to make and delete keys
 *
 * @package         CodeIgniter
*/
class ApiKeyController extends ApiAuthMiddleware
{

    protected $methods = [
        'index_put' => ['level' => 10, 'limit' => 10],
        'index_delete' => ['level' => 10],
        'level_post' => ['level' => 10],
        'regenerate_post' => ['level' => 10],
    ];

    /**
     * Generate and Insert a key into the database
     *
     * @access public
     * @return void
     */
    public function index_post()
    {
        // Build a new key
        $key = $this->generateKey();

        $post = $this->getContent(true);

        $user_id = $post['user_id'] ?? '';

        if (is_numeric($user_id) || empty($user_id)) {
            $this->response([
                'status' => false,
                'message' => 'No User ID provided',
                'reason' => 'No User ID provided'
            ], HttpStatus::BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        // If no key level provided, provide a generic key
        $level = isset($post['level']) ? $post['level'] : 1;

        $ignore_limits = isset($post['ignore_limits']) ? $post['ignore_limits'] : "";

        $ignore_limits = ctype_digit($ignore_limits) ? (int) $post['ignore_limits'] : 1;
        
        $data = [
            'user_id' => $user_id, 
            'level' => $level,
            'ignore_limits' => $ignore_limits,
            'ip_addresses' => ip_address(),
            // 'created_at' => datetime(),
            'date_created' => today()
        ];

        // Insert the new key
        if ($this->insertKey($key, $data)) {
            $this->response([
                'status' => true,
                'key' => $key,
                'message' => 'Key Created Successfully',
                'reason' => 'Key Created Successfully'
            ], HttpStatus::CREATED); // CREATED (201) being the HTTP response code
        } else {
            $this->response([
                'status' => false,
                'message' => 'Could not save the key',
                'reason' => 'Could not save the key'
            ], HttpStatus::INTERNAL_SERVER_ERROR); // INTERNAL_SERVER_ERROR (500) being the HTTP response code
        }
    }

    /**
     * Insert a key into the database
     *
     * @access public
     * @return void
     */
    public function index_put()
    {
        // Build a new key
        $key = $this->generateKey();

        // If no key level provided, provide a generic key
        $level = $this->put('level') ? $this->put('level') : 1;
        $ignoreLimits = ctype_digit($this->put('ignore_limits')) ? (int) $this->put('ignore_limits') : 1;

        // Insert the new key
        if ($this->insertKey($key, ['level' => $level, 'ignore_limits' => $ignoreLimits])) {
            $this->response([
                'status' => true,
                'key' => $key
            ], HttpStatus::CREATED); // CREATED (201) being the HTTP response code
        } else {
            $this->response([
                'status' => false,
                'message' => 'Could not save the key'
            ], HttpStatus::INTERNAL_SERVER_ERROR); // INTERNAL_SERVER_ERROR (500) being the HTTP response code
        }
    }

    /**
     * Remove a key from the database to stop it working
     *
     * @access public
     * @return void
     */
    public function index_delete()
    {
        $key = $this->delete('key');

        // Does this key exist?
        if (!$this->keyExists($key)) {
            // It doesn't appear the key exists
            $this->response([
                'status' => false,
                'message' => 'Invalid API key'
            ], HttpStatus::BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        // Destroy it
        $this->deleteKey($key);

        // Respond that the key was destroyed
        $this->response([
            'status' => true,
            'message' => 'API key was deleted'
        ], HttpStatus::NO_CONTENT); // NO_CONTENT (204) being the HTTP response code
    }

    /**
     * Change the level
     *
     * @access public
     * @return void
     */
    public function level_post()
    {
        $key = $this->post('key');
        $newLevel = $this->post('level');

        // Does this key exist?
        if (!$this->keyExists($key)) {
            // It doesn't appear the key exists
            $this->response([
                'status' => false,
                'message' => 'Invalid API key'
            ], HttpStatus::BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        // Update the key level
        if ($this->updateKey($key, ['level' => $newLevel])) {
            $this->response([
                'status' => true,
                'message' => 'API key was updated'
            ], HttpStatus::OK); // OK (200) being the HTTP response code
        } else {
            $this->response([
                'status' => false,
                'message' => 'Could not update the key level'
            ], HttpStatus::INTERNAL_SERVER_ERROR); // INTERNAL_SERVER_ERROR (500) being the HTTP response code
        }
    }

    /**
     * Suspend a key
     *
     * @access public
     * @return void
     */
    public function suspend_post()
    {
        $key = $this->post('key');

        // Does this key exist?
        if (!$this->keyExists($key)) {
            // It doesn't appear the key exists
            $this->response([
                'status' => false,
                'message' => 'Invalid API key'
            ], HttpStatus::BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        // Update the key level
        if ($this->updateKey($key, ['level' => 0])) {
            $this->response([
                'status' => true,
                'message' => 'Key was suspended'
            ], HttpStatus::OK); // OK (200) being the HTTP response code
        } else {
            $this->response([
                'status' => false,
                'message' => 'Could not suspend the user'
            ], HttpStatus::INTERNAL_SERVER_ERROR); // INTERNAL_SERVER_ERROR (500) being the HTTP response code
        }
    }

    /**
     * Regenerate a key
     *
     * @access public
     * @return void
     */
    public function regenerate_post()
    {
        $oldKey = $this->post('key');
        $keyDetails = $this->getKey($oldKey);

        // Does this key exist?
        if (!$keyDetails) {
            // It doesn't appear the key exists
            $this->response([
                'status' => false,
                'message' => 'Invalid API key'
            ], HttpStatus::BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        // Build a new key
        $newKey = $this->generateKey();

        // Insert the new key
        if ($this->insertKey($newKey, ['level' => $keyDetails->level, 'ignore_limits' => $keyDetails->ignore_limits])) {
            // Suspend old key
            $this->updateKey($oldKey, ['level' => 0]);

            $this->response([
                'status' => true,
                'key' => $newKey
            ], HttpStatus::CREATED); // CREATED (201) being the HTTP response code
        } else {
            $this->response([
                'status' => false,
                'message' => 'Could not save the key'
            ], HttpStatus::INTERNAL_SERVER_ERROR); // INTERNAL_SERVER_ERROR (500) being the HTTP response code
        }
    }

    /* Helper Methods */

    private function generateKey()
    {
        do {
            // Generate a random salt
            $str = str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789');

            $code = str_shuffle($str . str_replace('-', '', Uuid::v4()));

            $new_key = substr($code, 0, config_item('api_key_length'));

        } while ($this->keyExists($new_key));

        return $new_key;
    }

    /* Private Data Methods */

    private function getKey($key)
    {
        return $this->api->db
            ->where(config_item('api_key_column'), $key)
            ->get(config_item('api_keys_table'))
            ->row();
    }

    private function keyExists($key)
    {
        return $this->api->db
            ->where(config_item('api_key_column'), $key)
            ->count_all_results(config_item('api_keys_table')) > 0;
    }

    private function insertKey($key, $data)
    {
        $data[config_item('api_key_column')] = $key;
        // $data['date_created'] = function_exists('now') ? now() : time();

        return $this->api->db
            ->set($data)
            ->insert(config_item('api_keys_table'));
    }

    private function updateKey($key, $data)
    {
        return $this->api->db
            ->where(config_item('api_key_column'), $key)
            ->update(config_item('api_keys_table'), $data);
    }

    private function deleteKey($key)
    {
        return $this->api->db
            ->where(config_item('api_key_column'), $key)
            ->delete(config_item('api_keys_table'));
    }
}
