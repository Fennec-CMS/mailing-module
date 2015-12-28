<?php
/**
 ************************************************************************
 * @copyright 2015 David Lima
 * @license Apache 2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 ************************************************************************
 */
namespace Fennec\Modules\Mailing\Controller;

use \Fennec\Controller\Base;
use \Fennec\Modules\Mailing\Model\Contact as ContactModel;

/**
 * Mailing management module
 *
 * @author David Lima
 * @version r1.0
 */
class Index extends Base
{
    
    /**
     * Contact Model
     * @var \Fennec\Modules\Mailing\Model\Contact
     */
    public $model;

    /**
     * Defines $this->model
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->model = new ContactModel();
    }
    
    /**
     * If request is a POST, try to save a new contact on database
     */
    public function registerAction()
    {
        header("Content-Type: Application/JSON");
        
        $result = array(
            'errors' => false,
            'result' => null
        );
        
        if ($this->isPost()) {
            try {
                foreach ($this->getPost() as $postKey => $postValue) {
                    $this->$postKey = $postValue;
                }
        
                $this->model->setName($this->getPost('name'));
                $this->model->setEmail($this->getPost('email'));
                
                $result = $this->model->save();
                if (isset($result['errors'])) {
                    $result['result'] = implode('<br>', $result['errors']);
                }
            } catch (\Exception $e) {
                $this->exception = $e;
                $this->throwHttpError(500);
            }
        }
        
        print_r(json_encode($result));
    }
}
