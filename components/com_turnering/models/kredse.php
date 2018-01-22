<?php

/**
 * @version     1.0.0
 * @package     com_turnering
 * @copyright   Copyright (C) 2014 RayGoat. Alle rettigheder forbeholdes.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Ray Goat
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Turnering records.
 */
class TurneringModelKredse extends JModelList
{

    /**
     * Constructor.
     *
     * @param    array    An optional associative array of configuration settings.
     * @see        JController
     * @since    1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                                'id', 'a.id',
                'ordering', 'a.ordering',
                'state', 'a.state',
                'created_by', 'a.created_by',
                'alder', 'a.alder',
                'kreds', 'a.kreds',
                'dbukredsnr', 'a.dbukredsnr',
                'label', 'a.label',
                'gennemgaaende', 'a.gennemgaaende',

            );
        }
        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since    1.6
     */
    protected function populateState($ordering = null, $direction = null)
    {

        // Initialise variables.
        $app = JFactory::getApplication();

        // List state information
        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
        $this->setState('list.limit', $limit);

        $limitstart = JFactory::getApplication()->input->getInt('limitstart', 0);
        $this->setState('list.start', $limitstart);

        // Load the filter state.
        $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        
		if(empty($ordering)) {
			$ordering = 'a.ordering';
		}

        

        // List state information.
        parent::populateState($ordering, $direction);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return    JDatabaseQuery
     * @since    1.6
     */
    protected function getListQuery()
    {
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query
            ->select(
                $this->getState(
                    'list.select', 'DISTINCT a.*'
                )
            );

        $query->from('`#__dbu_kredse` AS a');

        
    // Join over the users for the checked out user.
    $query->select('uc.name AS editor');
    $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');
    
		// Join over the created by field 'created_by'
		$query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');

        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int)substr($search, 3));
            } else {
                $search = $db->Quote('%' . $db->escape($search, true) . '%');
                $query->where('( a.alder LIKE '.$search.' )');
            }
        }

        

        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering');
        $orderDirn = $this->state->get('list.direction');
        if ($orderCol && $orderDirn) {
            $query->order($db->escape($orderCol . ' ' . $orderDirn));
        }

        return $query;
    }

    public function getItems()
    {
        $items = parent::getItems();
        
        return $items;
    }

    /**
     * Get the filter form
     *
     * @return  JForm/false  the JForm object or false
     *
     */
    public function getFilterForm()
    {
        $form = null;

        // Try to locate the filter form automatically. Example: ContentModelArticles => "filter_articles"
        if (empty($this->filterFormName)) {
            $classNameParts = explode('Model', get_called_class());

            if (count($classNameParts) == 2) {
                $this->filterFormName = 'filter_' . strtolower($classNameParts[1]);
            }
        }

        if (!empty($this->filterFormName)) {
            // Get the form.
            $form = new JForm($this->filterFormName);
            $form->loadFile(dirname(__FILE__) . DS . 'forms' . DS . $this->filterFormName . '.xml');
            $filter_data = JFactory::getApplication()->getUserState($this->context, new stdClass);
            $form->bind($filter_data);
        }

        return $form;
    }

    /**
     * Function to get the active filters
     */
    public function getActiveFilters()
    {
        $activeFilters = false;

        if (!empty($this->filter_fields)) {
            for ($i = 0 ; $i < count($this->filter_fields); $i++) {
                $filterName = 'filter.' . $this->filter_fields[$i];

                if (property_exists($this->state, $filterName) && (!empty($this->state->{$filterName}) || is_numeric($this->state->{$filterName}))) {
                    $activeFilters = true;
                }
            }
        }

        return $activeFilters;
    }

    private function getParameterFromRequest($paramName, $default = null, $type = 'string')
    {
        $variables = explode('.', $paramName);
        $input = JFactory::getApplication()->input;

        $nullFound = false;
        if (count($variables) > 1) {
            $data = $input->get($variables[0], null, 'ARRAY');
        } else {
            $data = $input->get($variables[0], null, $type);
        }
        for ($i = 1; $i < count($variables) && !$nullFound; $i++) {
            if (isset($data[$variables[$i]])) {
                $data = $data[$variables[$i]];
            } else {
                $nullFound = true;
            }
        }

        return ($nullFound) ? $default : JFilterInput::getInstance()->clean($data, $type);

    }


}
