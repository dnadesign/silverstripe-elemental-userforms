<?php

namespace DNADesign\ElementalUserForms\Model;

use DNADesign\Elemental\Models\BaseElement;
use DNADesign\ElementalUserForms\Control\ElementFormController;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\UserForms\Control\UserDefinedFormController;
use SilverStripe\UserForms\UserForm;

class ElementForm extends BaseElement
{
    use UserForm {
        getCMSFields as userFormGetCMSFields;
    }

    private static $table_name = 'ElementForm';

    private static $icon = 'font-icon-block-form';

    private static $controller_class = ElementFormController::class;

    private static $singular_name = 'form';

    private static $plural_name = 'forms';

    private static $inline_editable = false;

    public function getCMSFields()
    {
        $this->afterExtending('updateCMSFields', function (FieldList $fields) {
            /** @var GridField $recipientsGridField */
            $recipientsGridField = $fields->dataFieldByName('EmailRecipients');
            /** @var GridFieldDetailForm $detailForm */
            $detailForm = $recipientsGridField->getConfig()->getComponentByType(GridFieldDetailForm::class);

            // Re-build CMS fields with "parent" form record populated
            $detailForm->setItemEditFormCallback(function (Form $form) {
                $record = $form->getRecord();

                // See EmailRecipient::getFormParent() for why this is necessary
                $record->FormID = $this->ID;
                $record->FormClass = $this->ClassName;

                // Re-build CMS fields
                $form->setFields($record->getCMSFields());
                $form->loadDataFrom($record, $record->ID == 0 ? Form::MERGE_IGNORE_FALSEISH : Form::MERGE_DEFAULT);

                if ($record->ID && !$record->canEdit()) {
                    // Restrict editing of existing records
                    $form->makeReadonly();
                } elseif (!$record->ID && !$record->canCreate()) {
                    // Restrict creation of new records
                    $form->makeReadonly();
                }

                // Use CMS tabset template which stops tabs from being rendered twice
                // Copied from GridFieldDetailForm_ItemRequest
                $form->Fields()->findOrMakeTab('Root')->setTemplate('SilverStripe\\Forms\\CMSTabSet');
            });
        });

        return $this->userFormGetCMSFields();
    }

    /**
     * @return UserForm
     */
    public function Form()
    {
        $controller = UserDefinedFormController::create($this);
        $current = Controller::curr();
        $controller->setRequest($current->getRequest());

        if ($current && $current->getAction() == 'finished') {
            return $controller->renderWith(UserDefinedFormController::class .'_ReceivedFormSubmission');
        }

        $form = $controller->Form();
        $form->setFormAction(
            Controller::join_links(
                $current->Link(),
                'element',
                $this->owner->ID,
                'Form'
            )
        );

        return $form;
    }

    public function Link($action = null)
    {
        $current = Controller::curr();

        if ($action === 'finished') {
            return Controller::join_links(
                $current->Link(),
                'finished'
            );
        }

        return parent::Link($action);
    }

    public function getType()
    {
        return _t(__CLASS__ . '.BlockType', 'Form');
    }
}
