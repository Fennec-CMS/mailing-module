<?php
/**
 ************************************************************************
 * @copyright 2015 David Lima
 * @license Apache 2.0 (http://www.apache.org/licenses/LICENSE-2.0) 
 ************************************************************************
 */
namespace Fennec\Modules\Mailing\Model;

use \Fennec\Model\Base;

/**
 * Mailing contact model
 *
 * @author David Lima
 * @version r1.0
 */
class Contact extends Base
{
    
    /**
     * Table to save data
     *
     * @var string
     */
    public static $table = "mailingcontacts";

    /**
     * Contact email
     * @var string
     */
    public $email;
    
    /**
     * Contact name
     * 
     * @var string(128)
     */
    public $name;

    /**
     * Contact registration date
     * 
     * @var string|datetime
     */
    public $date;
    
    /**
     * User IP
     * 
     * @var string(11)
     */
    public $ip;
    
    /**
     * Internal video ID
     * 
     * @var int
     */
    public $id;
    
    /**
     * Create or update a contact
     *
     * @return PDOStatement
     */
    public function save()
    {
        $data = $this->prepare();
        if (isset($data['valid']) && ! $data['valid']){
            return $data;
        } else {
            try {
                if ($this->id) {
                    $video = $this->getByColumn('id', $this->id)[0];
                    $this->image = $video->image;
                    $query = $this->update(self::$table)
                        ->set($data)
                        ->where("id = '{$this->id}'")
                        ->execute();
                } else {
                    $query = $this->insert($data)
                        ->into(self::$table)
                        ->execute();
                    
                    $this->id = $query;
                }

                return array(
                    'result' => (isset($video) ? 'Contact updated!' : 'Contact created!')
                );
            } catch (\Exception $e) {
                return array(
                    'result' => 'Failed to ' . (isset($video) ? 'update' : 'create') . ' contact!',
                    'errors' => array($e->getMessage())
                );
            }
        }
    }

    /**
     * Runs a SQL DELETE statement
     * 
     * @param int $id Video internal ID to remove
     * @return PDOStatement
     */
    public function remove($id)
    {
        $id = (int) $id;
        return $this->delete()
            ->from(self::$table)
            ->where("id = $id")
            ->execute();
    }
    
    /**
     * Prepare data to create/update video
     *
     * @return multitype:string |multitype:\Fennec\Model\string \Fennec\Model\integer
     */
    private function prepare()
    {
        $this->email = filter_var($this->email, \FILTER_SANITIZE_STRING);
        $this->name = filter_var($this->name, \FILTER_SANITIZE_STRING);
        
        $protocol = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://';
        
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $this->ip = filter_var($_SERVER['REMOTE_ADDR'], \FILTER_SANITIZE_STRING);
        } else {
            $this->ip = null;
        }
        
        $errors = $this->validate();
        if (! $errors['valid']) {
            return $errors;
        }

        return array(
            'name' => $this->name,
            'email' => $this->email,
            'ip' => $this->ip
        );
    }

    /**
     * Validate post data
     *
     * @return multitype:string
     */
    private function validate()
    {
        $validation = array(
            'valid' => true,
            'errors' => array()
        );

        if (! $this->email) {
            $validation['valid'] = false;
            $validation['errors']['email'] = "Email is a required field";
        }
        
        $alreadyExists = $this->getByColumn('email', $this->email, 1);
        if ($alreadyExists) {
            $validation['valid'] = false;
            $validation['errors']['email'] = "This email is already registered!";
        }
        
        return $validation;
    }
}
