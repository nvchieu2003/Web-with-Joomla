<?php
/**
 * @author     Daniel Dimitrov <daniel@compojoom.com>
 * @date       11.04.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controllerlegacy');

/**
 * Class ccommentControllerComments
 *
 * @since  5.0
 */
class CcommentControllerComments extends JControllerLegacy
{
	/**
	 * Gets the comments for an item
	 *
	 * @return void
	 */
	public function getcomments()
	{
		$input = JFactory::getApplication()->input;
		$id = $input->getInt('contentid', 0);
		$commentId = $input->getInt('comment', 0);
		$component = $input->getString('component', 'com_content');
		$start = $input->getInt('start', 0);
		$model = $this->getModel('comment');
		$config = ccommentConfig::getConfig($component);
		$total = $model->countComments($id, $component);
		$countParents = $model->countComments($id, $component, true);

		if ($commentId)
		{
			$pagination = new ccommentHelperPagination($commentId, $id, $component);
			$start = $pagination->findPage();
		}

		$comments = $model->getComments($id, $component, $start);

		if (count($comments))
		{
			$comments = ccommentHelperComment::prepareComments($comments, $config);
		}

		header('content-type:application/json');
		echo json_encode(
			array('info' => array(
				'page' => $start === 0 ? 1 : (int) $start,
				'countParents' => (int) $countParents,
				'total' => (int) $total),
				'models' => $comments)
		);
		jexit();
	}

	/**
	 * Search for specific comments
	 *
	 * TODO: fix this!!!
	 *
	 * @return void
	 */
	public function search()
	{
		$input = JFactory::getApplication()->input;
		$model = $this->getModel('comment', 'commentModel');
		$model->set('comObject', $this->comObject);

		$comments = $model->search($input->getInt('content_id', 0), $input->get('tsearch'), $input->getCmd('component', 'com_content'));

	}
}
