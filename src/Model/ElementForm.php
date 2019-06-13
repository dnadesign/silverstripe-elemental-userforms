<?php

namespace DNADesign\ElementalUserForms\Model;

use DNADesign\Elemental\Models\BaseElement;
use DNADesign\ElementalUserForms\Control\ElementFormController;
use SilverStripe\Control\Controller;
use SilverStripe\Control\RequestHandler;
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

    /**
     * This is a workaround for "Email recipients" functionality. In the userforms module, this
     * relies on accessing the current userforms instance (EmailRecipient::getFormParent()) by
     * using the LeftAndMain.currentPage session variable. When using an elemental userform, that
     * session variable points to the parent page instead of the elemental userform instance.
     */
    public function getCMSFields()
    {
        $this->afterExtending('updateCMSFields', function (FieldList $fields) {
            /** @var GridField $recipientsGridField */
            $recipientsGridField = $fields->dataFieldByName('EmailRecipients');
            /** @var GridFieldDetailForm $detailForm */
            $detailForm = $recipientsGridField->getConfig()->getComponentByType(GridFieldDetailForm::class);

            // Re-build the email recipients CMS fields with the "form parent" record populated
            $detailForm->setItemEditFormCallback(function (Form $form) {
                $record = $form->getRecord();

                // EmailRecipient::getFormParent() will use these values if set, before falling back to the
                // LeftAndMain.currentParent session variable (which won't work for the reasons above)
                $record->FormID = $this->ID;
                $record->FormClass = $this->ClassName;

                // Re-build CMS fields
                $form->setFields($record->getCMSFields());
                // Re-populate the form
                $form->loadDataFrom($record, $record->ID == 0 ? Form::MERGE_IGNORE_FALSEISH : Form::MERGE_DEFAULT);

                // Everything below this point is copied from GridFieldDetailForm_ItemRequest::ItemEditForm()

                if ($record->ID && !$record->canEdit()) {
                    // Restrict editing of existing records
                    $form->makeReadonly();
                } elseif (!$record->ID && !$record->canCreate()) {
                    // Restrict creation of new records
                    $form->makeReadonly();
                }

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

        // $current may not have a functional Link(), e.g. QueuedTaskRunner during solr reindex
        // surpress E_USER_WARNING from RequestHandler::Link() if url_segment config missing
        set_error_handler(fn(int $errno, string $errstr) => true, E_USER_WARNING);
        $link = $current->Link();
        restore_error_handler();

        $form = $controller->Form();
        $form->setFormAction(
            Controller::join_links(
                $link,
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
