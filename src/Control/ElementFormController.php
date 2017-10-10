<?php

namespace DNADesign\Elemental\UserForms\Control;

use DNADesign\Elemental\Controllers\ElementController;
use SilverStripe\Control\Controller;
use SilverStripe\UserForms\Control\UserDefinedFormController;

/**
 * Handles Form Submissions
 *
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
}
