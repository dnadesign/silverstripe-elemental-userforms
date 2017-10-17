<?php

namespace DNADesign\ElementalUserForms\Control;

use DNADesign\Elemental\Controllers\ElementController;
use SilverStripe\Control\Controller;
use SilverStripe\UserForms\Control\UserDefinedFormController;

/**
 * Handles Form Submissions
 */
class ElementFormController extends ElementController
{
    /**
     * {@inheritDoc}
     */
    public function __construct($element = null)
    {
        parent::__construct($element);

        $current = Controller::curr();

        if ($current->getRequest()->isPOST()) {
            // handle the post request.
            $user = UserDefinedFormController::create($element);
            $form = $user->Form();

            $user->process($current->getRequest()->postVars(), $form);
        }
    }

    /**
     * @param string $action
     *
     * @return string
     */
    public function Link($action = null)
    {
        $id = $this->element->ID;
        $segment = Controller::join_links('element', $id, $action);
        $page = Director::get_current_page();

        if ($page && !($page instanceof ElementController)) {
            return $page->Link($segment);
        }

        if ($controller = $this->getParentController()) {
            return $controller->Link($segment);
        }

        return $segment;
    }
}
