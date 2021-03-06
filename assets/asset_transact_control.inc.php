<?php
/*
 * Copyright (c)  2009, Tracmor, LLC
 *
 * This file is part of Tracmor.
 *
 * Tracmor is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tracmor is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tracmor; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

$strTransactionHeader = null;
$strLocationName = null;
$strLabelAssetsTo = null;

switch ($this->intTransactionTypeId) {
	case 1:  // Move
		$strTransactionHeader = '<div class="title">Move Assets</div>';
		$strLocationName = 'Move To:';
		$strLabelAssetsTo = 'Assets to move';
		break;
	case 2:  // Check In
		$strTransactionHeader = '<div class="title">Check In Assets</div>';
		$strLocationName = 'Check In To:';
		$strLabelAssetsTo = 'Assets to check in';
		break;
	case 3:  // Check Out
		$strTransactionHeader = '<div class="title">Check Out Assets</div>';
		$strLabelAssetsTo = 'Assets to check out';
		break;
	case 8:  // Reserve
		$strTransactionHeader = '<div class="title">Reserve Assets</div>';
		$strLabelAssetsTo = 'Assets to reserve';
		break;
	case 9:  // Unreserve
		$strTransactionHeader = '<div class="title">Unreserve Assets</div>';
		$strLabelAssetsTo = 'Assets to unreserve';
		break;
	case 10:  // Archive
		$strTransactionHeader = '<div class="title">Archive Assets</div>';
		$strLabelAssetsTo = 'Assets to archive';
		break;
	case 11:  // Unarchive
		$strTransactionHeader = '<div class="title">Unarchive Assets</div>';
		$strLocationName = 'Unarchive To:';
		$strLabelAssetsTo = 'Assets to unarchive';
		break;
}

echo($strTransactionHeader);
?>
<br class="item_divider" />
<table class="datagrid" cellpadding="5" cellspacing="0" border="0" >
	<tr>
		<td class="record_header">
		<?php
    $this->btnSave->RenderWithError();
    echo('&nbsp;');
    $this->btnCancel->RenderWithError();
    ?>
    </td>
  </tr>
  <table class="datagrid" cellpadding="5" cellspacing="0" border="0" >
	<tr>
		<td style="vertical-align:top; width: 100%;">
			<table cellpadding="5" cellspacing="0" style="width: 100%;">
    <?php
      // Check Out
      if ($this->intTransactionTypeId == 3) {
        if (!(QApplication::$TracmorSettings->CheckOutToOtherUsers != "1" && QApplication::$TracmorSettings->CheckOutToContacts != "1")) {
    ?>
      <tr>
        <td colspan="2" class="record_subheader"><div class="title"><?php _t('Check out to:') ?></div></td>
      </tr>
      <tr>
    		<td style="vertical-align:top;" class="record_field_name"><?php $this->lstCheckOutTo->RenderWithError(); ?></td>
    		<td style="vertical-align:top;">
				<table><tr><td style="text-align:right;"><?php $this->lstUser->RenderWithError(); $this->lstToCompany->RenderWithName();?></td><td><?php $this->lblNewToCompany->RenderWithError();?></td></tr>
				  <tr><td style="text-align:right;"><?php $this->lstToContact->RenderWithName(); ?></td><td><?php $this->lblNewToContact->RenderWithError(); ?></td></tr></table>
			</td>
    	</tr>
    	<?php
        }
    	?>
    	<tr>
        <td colspan="2" class="record_subheader"><div class="title"><?php _t('Set due date:') ?></div></td>
      </tr>
      <tr>
    		<td class="record_field_name"><?php $this->lstDueDate->RenderWithError(); ?></td>
    		<td style="vertical-align:bottom;"><?php $this->dttDueDate->Render(); ?></td>
    	</tr>
    	<tr>
        <td colspan="2" class="record_subheader"></td>
      </tr>
    <?php
      } else {
    ?>
    	<tr>
    		<td class="record_field_name"><?php echo($strLocationName); ?></td>
    		<td><?php $this->lstLocation->RenderWithError(); ?></td>
    	</tr>
	<?php } ?>
    	<tr>
    		<td class="record_field_name"><?php if ($this->intTransactionTypeId == 3) _t("Reason: "); else _t("Note: "); ?></td>
    		<td><?php $this->txtNote->RenderWithError(); ?></td>
    	</tr>
    </table>
    </td>
	</tr>
</table>
<br class="item_divider" />
<table>
  <tr>
	   <td colspan="2"><div class="title"><?php _t($strLabelAssetsTo); //if ($this->intTransactionTypeId == 3) _t("Assets to check out"); ?></div></td>
	</tr>
  <tr>
		<td class="record_field_name">Asset Code:</td>
		<td>
		  <table>
		    <tr>
		      <td valign="top" width="200px"><?php $this->txtNewAssetCode->RenderWithError(); ?></td>
		      <td valign="top" width="20px"><?php $this->lblAddAsset->Render(); ?></td>
		      <td valign="top"><?php $this->btnAdd->Render(); ?></td>
		    </tr>
		  </table>
		</td>
	</tr>
</table>
<?php $this->dtgAssetTransact->RenderWithError(); ?>
<?php $this->dlgNew->Render(); ?>
<?php $this->ctlAssetSearchTool->Render(); ?>
