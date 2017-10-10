<?php

namespace DNADesign\Elemental\UserForms\Model;

use SilverStripe\UserForms\Control\UserDefinedFormController;
use SilverStripe\UserForms\UserForm;
use SilverStripe\Control\Controller;
use DNADesign\Elemental\Models\BaseElement;
use DNADesign\Elemental\UserForms\Control\ElementFormController;

class ElementForm extends BaseElement
{
    use UserForm;

    /**
     * @var string
     */
    private static $table_name = 'ElementForm';

    /**
     * @var string
     */
    private static $title = 'Form';

    /**
     * @var string
     */
    private static $icon = 'elemental-userforms/images/form.svg';

    /**
     * @var string
     */
    private static $controller_class = ElementFormController::class;

    public function ElementForm()
    {
        $controller = new UserDefinedFormController($this);
        $current = Controller::curr();

        if ($current && $current->getAction() == 'finished') {
            return $controller->renderWith('ReceivedFormSubmission');
        }

        $form = $controller->Form();
        $form->setFormAction(Controller::join_links(
            $current->Link(),
            'element',
            $this->owner->ID
        ));

        return $form;
    }
}
