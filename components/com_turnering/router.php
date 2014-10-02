<?php

/**
 * @version     1.0.0
 * @package     com_turnering
 * @copyright   Copyright (C) 2014 INC Trampa. Alle rettigheder forbeholdes.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      René Gedde <diverse@oob.dk> - http://oob.dk
 */
// No direct access
defined('_JEXEC') or die;

/**
 * @param	array	A named array
 * @return	array
 */
function TurneringBuildRoute(&$query) {
    $segments = array();

    if (isset($query['task'])) {
        $segments[] = implode('/', explode('.', $query['task']));
        unset($query['task']);
    }
    if (isset($query['view'])) {
        $segments[] = $query['view'];
        unset($query['view']);
    }
    if (isset($query['id'])) {
        $segments[] = $query['id'];
        unset($query['id']);
    }

    return $segments;
}

/**
 * @param	array	A named array
 * @param	array
 *
 * Formats:
 *
 * index.php?/turnering/task/id/Itemid
 *
 * index.php?/turnering/id/Itemid
 */
function TurneringParseRoute($segments) {
    $vars = array();

    // view is always the first element of the array
    $vars['view'] = array_shift($segments);

    while (!empty($segments)) {
        $segment = array_pop($segments);
        if (is_numeric($segment)) {
            $vars['id'] = $segment;
        } else {
            $vars['task'] = $vars['view'] . '.' . $segment;
        }
    }

    return $vars;
}
