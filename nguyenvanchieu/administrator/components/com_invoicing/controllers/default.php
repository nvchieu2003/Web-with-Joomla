<?php
/**
 *  @package Bruce
 *  @copyright Copyright (c)203 Juloa.com
 *  @license GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

use Joomla\Utilities\ArrayHelper as JArrayHelper;

class InvoicingControllerDefault extends \JControllerLegacy {

    public function __construct($config= array()) {
		parent::__construct($config);
	
		// Apply, Save & New
		$this->registerTask('apply', 'save');
		$this->registerTask('save2new', 'save');
	}

	function init() {
		$document = $this->app->getDocument();
		$viewType = $document->getType();

		// Set the default view name from the Request
		$this->_view = $this->getView($this->controllerLabel, $viewType);

		// Push a model into the view
		$this->_model = $this->getModel( $this->controllerLabel );
		try {
			$this->_view->setModel( $this->_model, true );
		} catch(Exception $e) {

		}
	}

	/**
     * Redirects the browser or returns false if no redirect is set.
     *
     * @return  boolean  False if no redirect exists.
     */
    public function redirect() {
        if ($this->redirect)
        {
            $app = \JFactory::getApplication();
            $app->enqueueMessage($this->message, $this->messageType);
            \JApplicationBase::getInstance()->setHeader('Status', '303 see other', true);
            $app->redirect($this->redirect, 200);

            return true;
        }

        return false;
    }

    function display($cachable = false, $urlparams = false) {
		$this->init();
		$this->_view->setLayout("default");
		$this->_view->display();
	}

    function edit() {
		$this->init();
		$this->_view->setLayout("form");
		$this->_view->display();
	}
	
	function add() {
		$this->init();
		$this->_view->setLayout("form");
		$this->_view->display();
	}

	public function processData($post, $isNew) {
		$post = (object)$post;
		return $post;
	}

    function save() {
		$app = JFactory::getApplication();
		
		$this->canAccess();

        $post = JFactory::getApplication()->input->post->getArray();
        $isNew = $this->isNew($post);
        $post = $this->processData($post, $isNew);

		if(!isset($this->_model)) {
			$this->_model = $this->getModel( $this->controllerLabel );
		}

        if($isNew) {
            $id = $this->_model->save($post);
        } else {
            $id = $this->_model->update($post);
        }

		// Redirect the user and adjust session state based on the chosen task.
		$task = JFactory::getApplication()->input->getCmd('task');
		$app->enqueueMessage(\JText::_('INVOICING_ITEM_SAVED'), 'message');
		switch ($task)
		{
			case 'apply':
				$app->redirect( 'index.php?option=com_invoicing&view='.$this->controllerLabel.'&task=edit&id='.$id, 200 );
				break;
		
			case 'save2new':
				$app->redirect( 'index.php?option=com_invoicing&view='.$this->controllerLabel.'&task=add', 200 );
				break;
		
			default:
				$app->redirect( 'index.php?option=com_invoicing&view='.$this->controllerLabel, 200 );
			break;
		}
    }

    function remove() {
		$app = JFactory::getApplication();

		$this->canAccess();

		$ids = $app->input->get( 'cid', array());
		if (!is_array($ids)) {
			$table = array();
			$table[0] = $ids;
			$ids = $table;
		}
		
		foreach($ids as $id){
			$this->_model->delete($id);
		}
		
		$app->enqueueMessage(\JText::_('INVOICING_ITEM_REMOVED'), 'message');
		$app->redirect( 'index.php?option=com_invoicing&view='.$this->controllerLabel, 200 );
	}
	
	function unpublish() {
		$this->canAccess();
		$this->_changeState();
	}
	
	function publish() {
		$this->canAccess();
		$this->_changeState();
	}
	
	function _changeState() {
		$this->canAccess();
		$app = JFactory::getApplication();


		// Check for request forgeries
		JSession::checkToken() or jexit( 'Invalid Token' );

		$cid		= JFactory::getApplication()->input->get( 'cid', array(), '', 'array' );
		$publish	= ( $this->getTask() == 'publish' ? 1 : 0 );

		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) {
			$action = $publish ? 'publish' : 'unpublish';
			throw new Exception( JText::_( 'Select an item to' .$action, true ) );
		}
		
		$this->_model->changeState($publish,$cid);
		
		$app->redirect( 'index.php?option=com_invoicing&view='.$this->controllerLabel, 200 );
	}

    private function canAccess() {
        $app = JFactory::getApplication();

        //check if the user can access the page
        $user = JFactory::getUser();
        if(!$user->authorise('invoicing.'.$this->accessLabel,'com_invoicing')) {
            $app->enqueueMessage(\JText::_('INVOICING_ACCESS_DENIED'), 'error');
            $app->redirect('index.php', 200);
        }

        return true;
    }
}
