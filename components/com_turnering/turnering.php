<?php
/**
 * @version     1.0.0
 * @package     com_turnering
 * @copyright   Copyright (C) 2014 RayGoat. Alle rettigheder forbeholdes.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ray Goat
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

// Execute the task.
$controller	= JController::getInstance('Turnering');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
