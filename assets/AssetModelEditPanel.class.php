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
?>

<?php
	// Include the classfile for AssetModelEditPanelBase
	require(__PANELBASE_CLASSES__ . '/AssetModelEditPanelBase.class.php');

	/**
	 * This is a quick-and-dirty draft panel object to do Create, Edit, and Delete functionality
	 * of the AssetModel class.  It extends from the code-generated
	 * abstract AssetModelEditPanelBase class.
	 *
	 * Any display custimizations and presentation-tier logic can be implemented
	 * here by overriding existing or implementing new methods, properties and variables.
	 *
	 * Additional qform control objects can also be defined and used here, as well.
	 * 
	 * @package My Application
	 * @subpackage PanelDraftObjects
	 * 
	 */
	class AssetModelEditPanel extends AssetModelEditPanelBase {
		
		// Specify the Location of the Template (feel free to modify) for this Panel
		protected $strTemplate = 'AssetModelEditPanel.tpl.php';
		// Image File Control
		public $ifcImage;
		// An array of custom fields
		public $arrCustomFields;
    // Asset Custom fields
    public $chkAssetCustomFields;
		
		public function __construct($objParentObject, $strClosePanelMethod, $objAssetModel = null, $strControlId = null) {
			
			try {
				parent::__construct($objParentObject, $strClosePanelMethod, $objAssetModel, $strControlId);
			} catch (QCallerException $objExc) {
				$objExc->IncrementOffset();
				throw $objExc;
			}

      // Create
			
			// Create the Image File Control
			$this->ifcImage_Create();
			// Create all custom asset model fields
			$this->customFields_Create();
      // Create Asset Custom Fields
      $this->chkAssetCustomFields_Create();

			$this->UpdateCustomFields();
			
			// Modify Code Generated Controls
			$this->lstCategory->Required = true;
			$this->lstManufacturer->Required = true;
			$this->btnSave->RemoveAllActions('onclick');
			$this->btnSave->AddAction(new QClickEvent(), new QServerControlAction($this, 'btnSave_Click'));
			$this->btnSave->CausesValidation = QCausesValidation::SiblingsOnly;
			
			// Add Enter Key Events to each control except the Cancel Button
			$arrControls = array($this->txtShortDescription, $this->lstCategory, $this->lstManufacturer, $this->txtAssetModelCode, $this->txtLongDescription, $this->ifcImage);
			foreach ($arrControls as $ctlControl) {
				$ctlControl->CausesValidation = true;
				$ctlControl->AddAction(new QEnterKeyEvent(), new QServerControlAction($this, 'btnSave_Click'));
				$ctlControl->AddAction(new QEnterKeyEvent(), new QTerminateAction());
			}
			
			$this->strOverflow = QOverflow::Auto;
		}

		// Create and Setup lstCategory with alphabetic ordering
		protected function lstCategory_Create() {
			$this->lstCategory = new QListBox($this);
			$this->lstCategory->Name = QApplication::Translate('Category');
			$this->lstCategory->AddItem(QApplication::Translate('- Select One -'), null);
			$objCategoryArray = Category::LoadAllWithFlags(true, false, 'short_description ASC');
			if ($objCategoryArray) foreach ($objCategoryArray as $objCategory) {
				$objListItem = new QListItem($objCategory->__toString(), $objCategory->CategoryId);
				if (($this->objAssetModel->Category) && ($this->objAssetModel->Category->CategoryId == $objCategory->CategoryId))
					$objListItem->Selected = true;
				$this->lstCategory->AddItem($objListItem);
			}
		}
		
		// Create and Setup lstManufacturer with alphabetic ordering
		protected function lstManufacturer_Create() {
			$this->lstManufacturer = new QListBox($this);
			$this->lstManufacturer->Name = QApplication::Translate('Manufacturer');
			$this->lstManufacturer->AddItem(QApplication::Translate('- Select One -'), null);
			$objManufacturerArray = Manufacturer::LoadAll(QQ::Clause(QQ::OrderBy(QQN::Manufacturer()->ShortDescription)));
			if ($objManufacturerArray) foreach ($objManufacturerArray as $objManufacturer) {
				$objListItem = new QListItem($objManufacturer->__toString(), $objManufacturer->ManufacturerId);
				if (($this->objAssetModel->Manufacturer) && ($this->objAssetModel->Manufacturer->ManufacturerId == $objManufacturer->ManufacturerId))
					$objListItem->Selected = true;
				$this->lstManufacturer->AddItem($objListItem);
			}
		}

		// Create the Image File Control
		protected function ifcImage_Create() {
			$this->ifcImage = new QImageFileControl($this);
			$this->ifcImage->UploadPath = "../images/asset_models/";
			$this->ifcImage->WebPath = "../images/asset_models/";
			$this->ifcImage->ThumbUploadPath = "../images/asset_models/thumbs/";
			$this->ifcImage->ThumbWebPath = "../images/asset_models/thumbs/";
			// $this->ifcImage->FileName = $this->objAssetModel->ImagePath;
			$this->ifcImage->Name = 'Upload Picture';
			$this->ifcImage->BuildThumbs = true;
			$this->ifcImage->ThumbWidth = 150;
			$this->ifcImage->ThumbHeight = 250;
			$this->ifcImage->Required = false;
			// $this->ifcImage->ThumbPrefix = "thumb_";
			$this->ifcImage->Prefix = QApplication::$TracmorSettings->ImageUploadPrefix;
			$this->ifcImage->Suffix = "_asset_model";
		}
		
		// Create all Custom Asset Fields
		protected function customFields_Create() {
		
			// Load all custom fields and their values into an array objCustomFieldArray->CustomFieldSelection->CustomFieldValue
			$this->objAssetModel->objCustomFieldArray = CustomField::LoadObjCustomFieldArray(4, $this->blnEditMode, $this->objAssetModel->AssetModelId);
			
			// Create the Custom Field Controls - labels and inputs (text or list) for each
			$this->arrCustomFields = CustomField::CustomFieldControlsCreate($this->objAssetModel->objCustomFieldArray, $this->blnEditMode, $this, false, true, false);
		}

    protected function chkAssetCustomFields_Create(){
      $this->chkAssetCustomFields = new QCheckBoxList($this);
      $this->chkAssetCustomFields->Name = 'Asset Custom Fields:';

      $arrAssetCustomFiieldOptions = EntityQtypeCustomField::LoadArrayByEntityQtypeId(QApplication::Translate(EntityQtype::Asset));
      if(count($arrAssetCustomFiieldOptions)>0){
        if ($this->blnEditMode){
          $arrChosenCustomFieldId = array();
          $arrChosenCustomField = AssetCustomFieldAssetModel::LoadArrayByAssetModelId($this->objAssetModel->AssetModelId);
          foreach ($arrChosenCustomField as $objChosenCustomField){
            $arrChosenCustomFieldId[] = $objChosenCustomField->CustomFieldId;
          }
        }
        foreach($arrAssetCustomFiieldOptions as $arrAssetCustomFieldOption){
          $selected = false;
          if($this->blnEditMode){
            $selected = in_array($arrAssetCustomFieldOption->CustomField->CustomFieldId,$arrChosenCustomFieldId);
          }
          /*     else{
                 $selected = $arrAssetCustomFieldOption->CustomField->AllAssetModelsFlag;
               }
          *///Excluding AllAssetModelsFligged Items just untill stupping qcodo 4.22
          $role=RoleEntityQtypeCustomFieldAuthorization::LoadByRoleIdEntityQtypeCustomFieldIdAuthorizationId(
            QApplication::$objRoleModule->RoleId,
            $arrAssetCustomFieldOption->EntityQtypeCustomFieldId,
            2
          );
          if(!$arrAssetCustomFieldOption->CustomField->AllAssetModelsFlag
            &&$arrAssetCustomFieldOption->CustomField->ActiveFlag
            && (int)$role->AuthorizedFlag==1){
            $this->chkAssetCustomFields->AddItem(new QListItem($arrAssetCustomFieldOption->CustomField->ShortDescription,
              $arrAssetCustomFieldOption->CustomField->CustomFieldId,
              $selected
            ));
          }
        }
      }
      if ($this->chkAssetCustomFields->ItemCount==0){
        $this->chkAssetCustomFields->Display = false;
      }
    }
		
		// Save Button Click Actions
		public function btnSave_Click($strFormId, $strControlId, $strParameter) {
			
			$this->UpdateAssetModelFields();
			$this->objAssetModel->Save();

			// Adding AssetCustomFieldsAssetModels with allAssetModel flag checked
      $this->UpdateAssetModelCustomFields();
			// Assign input values to custom fields
			if ($this->arrCustomFields) {
				// Save the values from all of the custom field controls
				CustomField::SaveControls($this->objAssetModel->objCustomFieldArray, $this->blnEditMode, $this->arrCustomFields, $this->objAssetModel->AssetModelId, 4);
			}

			if ($this->ifcImage->FileName) {
				// Retrieve the extension (.jpg, .gif) from the filename
				$explosion = explode(".", $this->ifcImage->FileName);
				// Set the file name to ID_asset_model.ext
				$this->ifcImage->FileName = sprintf('%s%s%s.%s', $this->ifcImage->Prefix, $this->objAssetModel->AssetModelId, $this->ifcImage->Suffix, $explosion[1]);
				// Set the image path for saving the asset model
				$this->txtImagePath->Text = $this->ifcImage->FileName;
				// Upload the file to the server
				$this->ifcImage->ProcessUpload();
				
				// Save the image path information to the AssetModel object
				$this->objAssetModel->ImagePath = $this->txtImagePath->Text;
				$this->objAssetModel->Save(false, true);
			}
			
			$lstAssetModel = $this->ParentControl->ParentControl->lstAssetModel;
			$lstAssetModel->AddItem($this->txtShortDescription->Text, $this->objAssetModel->AssetModelId);
			$lstAssetModel->SelectedValue = $this->objAssetModel->AssetModelId;
			$this->ParentControl->ParentControl->lstAssetModel_Select($this->objForm->FormId, $this->ControlId, null);
			
			$this->ParentControl->RemoveChildControls(true);
			$this->CloseSelf(true);
		}
		
		// Cancel Button Click Action
		public function btnCancel_Click($strFormId, $strControlId, $strParameter) {
			
			$this->ParentControl->RemoveChildControls(true);
			$this->CloseSelf(true);
		}
		//Set display logic for the CustomFields
		protected function UpdateCustomFields(){
			if($this->arrCustomFields){
				foreach ($this->arrCustomFields as $objCustomField) {	
					//If the role doesn't have edit access for the custom field and the custom field is required, the field shows as a label with the default value
					if (!$objCustomField['blnEdit']){				
						$objCustomField['lbl']->Display=true;
						$objCustomField['input']->Display=false;
						if(($objCustomField['blnRequired']))
							$objCustomField['lbl']->Text=$objCustomField['EditAuth']->EntityQtypeCustomField->CustomField->DefaultCustomFieldValue->__toString();			
					}		
				}
			}
			
		}
    protected function UpdateAssetModelCustomFields(){

      $arrAssetCustomFieldsToAdd = array();
      $this->chkAssetCustomFields->SelectedValues;
      // Generate array of Custom Field values for All Asset Models must be presented in all cases
      $arrAllAssetModelsFlaggedObjects = EntityQtypeCustomField::LoadArrayByEntityQtypeId(QApplication::Translate(EntityQtype::Asset));
      $arrAllAssetModelsFlag = array();
      foreach ($arrAllAssetModelsFlaggedObjects as $arrAllAssetModelsFlaggedObject){
        if ($arrAllAssetModelsFlaggedObject->CustomField->AllAssetModelsFlag){
          $arrAllAssetModelsFlag[] = $arrAllAssetModelsFlaggedObject->CustomField->CustomFieldId;
        }
      }

      $arrAssetCustomFieldsToAdd = array_merge($this->chkAssetCustomFields->SelectedValues,$arrAllAssetModelsFlag);
      $arrAssetCustomFieldsToAdd = array_unique($arrAssetCustomFieldsToAdd);

      // If new asset model add AssetCustomFields for All together with selected
      if(!$this->blnEditMode){
        foreach($arrAssetCustomFieldsToAdd as $keyAssetCustomField){
          $newAssetCustomField = new AssetCustomFieldAssetModel();
          $newAssetCustomField->CustomFieldId = $keyAssetCustomField;
          $newAssetCustomField->AssetModelId  = $this->objAssetModel->AssetModelId;
          $newAssetCustomField->Save();
        }
      }
      // Delete items if unchecked
      else{
        $currentAssetCustomFields = AssetCustomFieldAssetModel::LoadArrayByAssetModelId($this->objAssetModel->AssetModelId);
        foreach($currentAssetCustomFields as $currentAssetCustomField){
          if (!(in_array($currentAssetCustomField->CustomField->CustomFieldId,$arrAssetCustomFieldsToAdd))){
            $currentAssetCustomField->Delete();
          }
        }
        foreach($arrAssetCustomFieldsToAdd as $keyAssetCustomField){
          $blnToAdd = true;
          foreach($currentAssetCustomFields as $currentAssetCustomField){
            if ($currentAssetCustomField->CustomField->CustomFieldId == $arrAssetCustomFieldsToAdd){
              $blnToAdd = false;
            }
          }
          if($blnToAdd){
            $newAssetCustomField = new AssetCustomFieldAssetModel();
            $newAssetCustomField->CustomFieldId = $keyAssetCustomField;
            $newAssetCustomField->AssetModelId  = $this->objAssetModel->AssetModelId;
            $newAssetCustomField->Save();
          }
        }
      }
    }
	}
?>
