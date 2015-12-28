<?php
/**
 ************************************************************************
 * @copyright 2015 David Lima
 * @license Apache 2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 ************************************************************************
 */
namespace Fennec\Modules\Mailing\Controller\Admin;

use \Fennec\Controller\Admin\Index as AdminController;
use \Fennec\Modules\Mailing\Model\Contact as ContactModel;

/**
 * Mailing management module
 *
 * @author David Lima
 * @version r1.0
 */
class Index extends AdminController
{

    /**
     * Contact Model
     * 
     * @var \Fennec\Modules\Mailing\Model\Contact
     */
    public $model;
    
    /**
     * Initial setup
     */
    public function __construct()
    {
        parent::__construct();
    
        $this->model = new ContactModel();
    
        $this->moduleInfo = array(
            'title' => 'Mailing'
        );
    }
    
    /**
     * Default action
     */
    public function indexAction()
    {
        $this->list = $this->model->getAll();
    }
    
    /**
     * If request is a POST, try to save a new contact on database
     */
    public function formAction()
    {
        if ($this->isPost()) {
            try {
                foreach ($this->getPost() as $postKey => $postValue) {
                    $this->$postKey = $postValue;
                }
        
                $this->model->setName($this->getPost('name'));
                $this->model->setEmail($this->getPost('email'));
                
                $this->result = $this->model->save();
                if (isset($this->result['errors'])) {
                    $this->result['result'] = implode('<br>', $this->result['errors']);
                }
            } catch (\Exception $e) {
                $this->exception = $e;
                $this->throwHttpError(500);
            }
        }
    }
    
    /**
     * Try to delete a contact
     */
    public function deleteAction()
    {
        header("Content-Type: Application/JSON");
        
        $result = array(
            'errors' => true,
            'result' => $this->translate('Invalid request')
        );
        
        if ($this->getParam('id') && is_numeric($this->getParam('id'))) {
            try {
                $id = (int) $this->getParam('id');
                $this->model->remove($id);
                $result['errors'] = false;
                $result['result'] = $this->translate('Contact removed');
            } catch (\Exception $e) {
                $result['result'] = $e->getMessage();
            }
        }
        
        print_r(json_encode($result));
    }
}
