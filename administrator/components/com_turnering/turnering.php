<?php
/**
 * @version     1.0.0
 * @package     com_turnering
 * @copyright   Copyright (C) 2014 INC Trampa. Alle rettigheder forbeholdes.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      René Gedde <diverse@oob.dk> - http://oob.dk
 */


// no direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_turnering')) 
{
	throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

$controller	= JController::getInstance('Turnering');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
