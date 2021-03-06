<?php
namespace Clinic\Controller;
use Clinic\Controller\AbstractAdminController;
use Zend\View\Model\ViewModel;

class AdminBaseController extends AbstractAdminController
{
    protected $_entityManager;
    protected $_repository;
    protected $_entityName;
    protected $_template;

    function __construct($entityManager, $entityPath, $entityName)
    {
        $this->_entityManager = $entityManager;
        $this->_entityName    = $entityName;
        $this->_repository    = $this->_entityManager->getRepository($entityPath);
        $this->_template      = "clinic/generic/{$entityName}";
    }
    /**
     * @inheritdoc
     */
    public function indexAction()
    {
        $entity = $this->_repository->findAll();
        $view   = new ViewModel([$this->_entityName . 's' => $entity]);
        $view->setTemplate($this->_template . '/all');
        return $view;
    }
    /**
     * @inheritdoc
     */
    public function profileAction()
    {
        $id = $this->params('id');
        $entity = $this->_repository->find($id);

        if(!is_null($entity) && !($this->_entityName == 'appointment')) {
            $view = new ViewModel([$this->_entityName => $entity]);
            $view->setTemplate($this->_template . '/profile');
            return $view;
        } else {
           $this->redirect()->toRoute('admin', [
                'controller' => $this->_entityName,
                'action'     => 'index',
            ]);
        }
    }
    /**
     * @inheritdoc
     */
    public function deleteAction()
    {
        $id = $this->params('id');
        $entity = $this->_repository->find($id);

        $url = $this->url()->fromRoute('admin', [
            'controller' => $this->_entityName,
            'action' => 'index',
        ]);

        // id not found
        if(is_null($entity)) {
            return $this->getMessagePage('error', 'Id not found', $url);
        }

        try {
            $this->_entityManager->remove($entity);
            $this->_entityManager->flush();
            return $this->getMessagePage(
                'success', "{$this->_entityName} with id {$id} was deleted!", $url
            );
        } catch (\Exception $e) {
            $errorCode = $e->getPrevious()->getCode();
            return $this->getMessagePage('error', "[$errorCode]: {$this->_entityName} was not to deleted!", $url);
        }
    }
    /**
     * @inheritdoc
     */
    public function addAction()
    {
        //TODO: Form
    }
    /**
     * @inheritdoc
     */
    public function editAction()
    {
        //TODO: Form
    }
}