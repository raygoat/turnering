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

JHtml::_('behavior.tooltip');
JHTML::_('script', 'system/multiselect.js', false, true);
// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_turnering/assets/css/list.css');

$user = JFactory::getUser();
$userId = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$ordering = ($listOrder == 'a.ordering');
$canCreate = $user->authorise('core.create', 'com_turnering');
$canEdit = $user->authorise('core.edit', 'com_turnering');
$canCheckin = $user->authorise('core.manage', 'com_turnering');
$canChange = $user->authorise('core.edit.state', 'com_turnering');
$canDelete = $user->authorise('core.delete', 'com_turnering');
?>

<form action="<?php echo JRoute::_('index.php?option=com_turnering&view=kredse'); ?>" method="post"
      name="adminForm" id="adminForm">

    
    <table class="front-end-list">
        <thead>
        <tr>
            
				<th class="align-left">
					<?php echo JHtml::_('grid.sort',  'COM_TURNERING_KREDSE_ALDER', 'a.alder', $listDirn, $listOrder); ?>
				</th>

				<th class="align-left">
					<?php echo JHtml::_('grid.sort',  'COM_TURNERING_KREDSE_KREDS', 'a.kreds', $listDirn, $listOrder); ?>
				</th>

				<th class="align-left">
					<?php echo JHtml::_('grid.sort',  'COM_TURNERING_KREDSE_DBUKREDSNR', 'a.dbukredsnr', $listDirn, $listOrder); ?>
				</th>

				<th class="align-left">
					<?php echo JHtml::_('grid.sort',  'COM_TURNERING_KREDSE_LABEL', 'a.label', $listDirn, $listOrder); ?>
				</th>

				<th class="align-left">
					<?php echo JHtml::_('grid.sort',  'COM_TURNERING_KREDSE_GENNEMGAAENDE', 'a.gennemgaaende', $listDirn, $listOrder); ?>
				</th>

            <?php if (isset($this->items[0]->state)) : ?>
                <th class="align-left">
                    <?php echo JHtml::_('grid.sort', 'JPUBLISHED', 'a.state', $listDirn, $listOrder); ?>
                </th>
            <?php endif; ?>

            <?php if (isset($this->items[0]->id)) : ?>
                <th class="nowrap align-left">
                    <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                </th>
            <?php endif; ?>

            				<?php if ($canEdit || $canDelete): ?>
					<th class="align-center">
				<?php echo JText::_('COM_TURNERING_KREDSE_ACTIONS'); ?>
				</th>
				<?php endif; ?>

        </tr>
        </thead>
        <tfoot>
        <tr>
            <td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
                <?php echo $this->pagination->getListFooter(); ?>
            </td>
        </tr>
        </tfoot>
        <tbody>
        <?php foreach ($this->items as $i => $item) : ?>
            <?php $canEdit = $user->authorise('core.edit', 'com_turnering'); ?>

            

            <tr class="row<?php echo $i % 2; ?>">
                
					<td>
						<?php echo $item->alder; ?>
					</td>

					<td>
						<?php echo $item->kreds; ?>
					</td>

					<td>
						<?php echo $item->dbukredsnr; ?>
					</td>

					<td>
					<?php if (isset($item->checked_out) && $item->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'kredse.', $canCheckin); ?>
					<?php endif; ?>
					<a href="<?php echo JRoute::_('index.php?option=com_turnering&view=kredsform&id=' . (int) $item->id); ?>">

						<?php echo $this->escape($item->label); ?>
					</a>
					</td>

					<td>
						<?php echo $item->gennemgaaende; ?>
					</td>

                <?php if (isset($this->items[0]->state)) : ?>
                    <td class="align-left">
                        <button
                            type="button" <?php echo ($canEdit || $canChange) ? 'onclick="window.location.href=\'' . JRoute::_('index.php?option=com_turnering&task=kredsform.publish&id=' . $item->id . '&state=' . (($item->state + 1) % 2) . '\'"', false, 2) : 'disabled="disabled"'; ?>>
                            <?php if ($item->state == 1): ?>
                                <?php echo JText::_('JPUBLISHED'); ?>
                            <?php else: ?>
                                <?php echo JText::_('JUNPUBLISHED'); ?>
                            <?php endif; ?>
                        </button>
                    </td>
                <?php endif; ?>
                <?php if (isset($this->items[0]->id)) : ?>
                    <td class="align-left">
                        <?php echo (int)$item->id; ?>
                    </td>
                <?php endif; ?>

                				<?php if ($canEdit || $canDelete): ?>
					<td class="align-center">
					</td>
				<?php endif; ?>

            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($canCreate): ?>
        <button type="button"
                onclick="window.location.href = '<?php echo JRoute::_('index.php?option=com_turnering&task=kredsform.edit&id=0', false, 2); ?>';"><?php echo JText::_('COM_TURNERING_ADD_ITEM'); ?></button>
    <?php endif; ?>

    <div>
        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="boxchecked" value="0"/>
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>

<script type="text/javascript">
    if (typeof jQuery == 'undefined') {
        var headTag = document.getElementsByTagName("head")[0];
        var jqTag = document.createElement('script');
        jqTag.type = 'text/javascript';
        jqTag.src = '//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js';
        jqTag.onload = jQueryCode;
        headTag.appendChild(jqTag);
    } else {
        jQueryCode();
    }

    function jQueryCode() {
        jQuery('.delete-button').click(function () {
            var item_id = jQuery(this).attr('data-item-id');
            if (confirm("<?php echo JText::_('COM_TURNERING_DELETE_MESSAGE'); ?>")) {
                window.location.href = '<?php echo JRoute::_('index.php?option=com_turnering&task=kredsform.remove&id=', false, 2) ?>' + item_id;
            }
        });
    }

</script>