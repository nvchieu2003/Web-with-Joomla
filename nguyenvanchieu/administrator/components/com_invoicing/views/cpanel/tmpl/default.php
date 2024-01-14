<?php
/**
 * @package		Invoicing
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

//JHtml::_('behavior.tooltip');
//JHtml::_('behavior.modal');

if(version_compare(JVERSION, '3.0', 'ge')) {
	JHTML::_('jquery.framework');
} else {
	JHTML::_('behavior.mootools');
}

//$this->loadHelper('Select');
//$this->loadHelper('Cparams');
//$this->loadHelper('Format');
//$this->loadHelper('Dates');

$document = \JFactory::getDocument();
$document->addScript(JURI::root().'media/com_invoicing/jqplot/jquery.jqplot.min.js');
$document->addScript(JURI::root().'media/com_invoicing/jqplot/plugins/jqplot.barRenderer.min.js');
$document->addScript(JURI::root().'media/com_invoicing/jqplot/plugins/jqplot.categoryAxisRenderer.min.js');
$document->addScript(JURI::root().'media/com_invoicing/jqplot/plugins/jqplot.canvasTextRenderer.min.js');
$document->addScript(JURI::root().'media/com_invoicing/jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js');
$document->addStyleSheet(JURI::root().'media/com_invoicing/js/jqplot/jquery.jqplot.min.css');
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<input type="hidden" name="option" value="com_invoicing" />
<input type="hidden" name="view" value="cpanel" />
<input type="hidden" id="task" name="task" value="read" />
<input type="hidden" name="hidemainmenu" id="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
<?php echo JHTML::_( 'form.token' ); ?>

<h1><?php echo \JText::_('COM_INVOICING_STATISTICS')?></h1>
<?php echo InvoicingHelperSelect::currencies('currency_id',$this->currency_id);?>
<div>
<div id="leftcolumn">
	<div id="dates_filters"> <?php
	echo JHTML::_('calendar',$this->filters->dateFilterFrom, "dateFilterFrom", "dateFilterFrom", "%Y-%m-%d");
	echo JHTML::_('calendar',$this->filters->dateFilterTo, "dateFilterTo", "dateFilterTo", "%Y-%m-%d");?>
	<button class="btn btn-mini btn-primary" onclick="document.adminForm.submit();">
		<span class="icon-filter"></span>
		<?php echo \JText::_('INVOICING_FILTER'); ?>
	</button>
	</div>
	<div id = "csv" align="right">
	<a href="<?php echo \JRoute::_('index.php?option=com_invoicing&view=cpanel&format=csv&currency_id='.$this->currency_id.'&dateFilterFrom='.$this->filters->dateFilterFrom.'&dateFilterTo='.$this->filters->dateFilterTo.'&monthfrom='.$this->filters->monthfrom.'&yearfrom='.$this->filters->yearfrom.'&monthto='.$this->filters->monthto.'&yearto='.$this->filters->yearto)?>">
		<?php echo \JText::_('COM_INVOICING_DOWNLOAD_CSV') ?>
	 </a>
	</div>
	<div id="dailychart" class="example-chart" ></div>

	<div id="months_filters">
	<?php 
	
	echo InvoicingHelperFormat::formatMonths("monthfrom",$this->filters->monthfrom);
	echo InvoicingHelperFormat::formatYears("yearfrom",$this->filters->yearfrom);
	
	echo InvoicingHelperFormat::formatMonths("monthto",$this->filters->monthto);
	echo InvoicingHelperFormat::formatYears("yearto",$this->filters->yearto);
	?>
	<button class="btn btn-mini btn-primary" onclick="document.adminForm.submit();">
	<span class="icon-filter"></span> <?php echo \JText::_('INVOICING_FILTER'); ?></button>
	</div>
	<div id="monthlychart" class="example-chart" ></div>
</div>
<div id="rightcolumn">

	<div id="pendingorders">
	<?php 
	if ($this->nb_pending_orders == 0)
		echo \JText::_('COM_INVOICING_NO_ORDER_PENDING');
	else if ($this->nb_pending_orders == 1) 
		echo \JText::_('COM_INVOICING_1_PENDING_ORDER');
	else
		echo sprintf(\JText::_('COM_INVOICING_X_PENDINGS_ORDER'),$this->nb_pending_orders);
	?>
	</div>
	<table width="100%" id="statistics" class="table table-striped">
		<tbody>
			<tr class="row0">
				<td width="50%"><?php echo \JText::_('COM_INVOICING_LAST_YEAR')?></td>
				<td align="right" width="25%"><?php echo $this->stats->lastyear_number;?></td>
				<td align="right" width="25%"><?php echo InvoicingHelperFormat::formatPrice($this->stats->lastyear_sum,$this->currency_id);?></td>
			</tr>
			<tr class="row1">
				<td><?php echo \JText::_('COM_INVOICING_THIS_YEAR')?></td>
				<td align="right"><?php echo $this->stats->thisyear_number;?></td>
				<td align="right" width="25%"><?php echo InvoicingHelperFormat::formatPrice($this->stats->thisyear_sum,$this->currency_id);?></td>
			</tr>
			<tr class="row0">
				<td><?php echo \JText::_('COM_INVOICING_LAST_MONTH')?></td>
				<td align="right"><?php echo $this->stats->lastmonth_number;?></td>
				<td align="right" width="25%"><?php echo InvoicingHelperFormat::formatPrice($this->stats->lastmonth_sum,$this->currency_id);?></td>
			</tr>
			<tr class="row1">
				<td><?php echo \JText::_('COM_INVOICING_THIS_MONTH')?></td>
				<td align="right"><?php echo $this->stats->thismonth_number;?></td>
				<td align="right" width="25%"><?php echo InvoicingHelperFormat::formatPrice($this->stats->thismonth_sum,$this->currency_id);?></td>
			</tr>
			<tr class="row0">
				<td width="50%"><?php echo \JText::_('COM_INVOICING_LAST_7_DAYS')?></td>
				<td align="right" width="25%"><?php echo $this->stats->lastsevendays_number;?></td>
				<td align="right" width="25%"><?php echo InvoicingHelperFormat::formatPrice($this->stats->lastsevendays_sum,$this->currency_id);?></td>
			</tr>
			<tr class="row1">
				<td width="50%"><?php echo \JText::_('COM_INVOICING_YESTERDAY')?></td>
				<td align="right" width="25%"><?php echo $this->stats->yesterday_number;?></td>
				<td align="right" width="25%"><?php echo InvoicingHelperFormat::formatPrice($this->stats->yesterday_sum,$this->currency_id);?></td>
			</tr>
			<tr class="row0">
				<td width="50%"><strong><?php echo \JText::_('COM_INVOICING_TODAY')?></strong></td>
				<td align="right" width="25%">
					<strong><?php echo $this->stats->today_number;?></strong>
				</td>
				<td align="right" width="25%">
					<?php echo InvoicingHelperFormat::formatPrice($this->stats->today_sum,$this->currency_id);?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<div style="clear:both"></div>
</div>

<?php
	$script = "
	jQ(document).ready(function(){
		var plot1 = jQ.jqplot ('dailychart', [".json_encode($this->dailypoints)."] , {
				// Give the plot a title.
				title: '',
				// You can specify options for all axes on the plot at once with
				// the axesDefaults object.  Here, we're using a canvas renderer
				// to draw the axis label which allows rotated text.
				axesDefaults: {
						tickRenderer: jQ.jqplot.CanvasAxisTickRenderer ,
						tickOptions: {
							angle: -30,
							fontSize: '10pt'
						}
				},
				// Likewise, seriesDefaults specifies default options for all
				// series in a plot.  Options specified in seriesDefaults or
				// axesDefaults can be overridden by individual series or
				// axes options.
				// Here we turn on smoothing for the line.
				seriesDefaults: {
						rendererOptions: {
								smooth: true
						}
				},
				// An axes object holds options for all axes.
				// Allowable axes are xaxis, x2axis, yaxis, y2axis, y3axis, ...
				// Up to 9 y axes are supported.
				axes:{
							xaxis:{
									renderer: jQ.jqplot.CategoryAxisRenderer
							}
					}
			});
	});
	
	jQ(document).ready(function(){
    // A Bar chart from a single series will have all the bar colors the same.
    //var line1 = [['January', 400],['February', 6000],['March', 2000],['April', 5000],['May', 6000]];
    var line1 = ".json_encode($this->monthlypoints)." 
	jQ('#monthlychart').jqplot([line1], {
        title:'',
        seriesDefaults:{
            renderer:jQ.jqplot.BarRenderer
        },
        axesDefaults: {
            tickRenderer: jQ.jqplot.CanvasAxisTickRenderer ,
            tickOptions: {
              angle: -30,
              fontSize: '10pt'
            }
        },
        axes:{
            xaxis:{
                renderer: jQ.jqplot.CategoryAxisRenderer
            }
        }
    });
});";

\JFactory::getApplication()->getDocument()->addScriptDeclaration($script);
?>
</form>