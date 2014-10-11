<?php
/**
 * @version     1.0.0
 * @package     com_turnering
 * @copyright   Copyright (C) 2014 INC Trampa. Alle rettigheder forbeholdes.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      René Gedde <diverse@oob.dk> - http://oob.dk
 */

// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Kredse list controller class.
 */
class TurneringControllerKredse extends TurneringController
{
	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function &getModel($name = 'Kredse', $prefix = 'TurneringModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}