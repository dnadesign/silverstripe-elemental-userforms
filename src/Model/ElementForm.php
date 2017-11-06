<?php

namespace DNADesign\ElementalUserForms\Model;

use SilverStripe\UserForms\Control\UserDefinedFormController;
use SilverStripe\UserForms\UserForm;
use SilverStripe\Control\Controller;
use DNADesign\Elemental\Models\BaseElement;
use DNADesign\ElementalUserForms\Control\ElementFormController;

class ElementForm extends BaseElement
{
    use UserForm;

    private static $table_name = 'ElementForm';

    private static $icon = 'font-icon-block-form';

    private static $controller_class = ElementFormController::class;

    private static $singular_name = 'form';

    private static $plural_name = 'forms';

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
