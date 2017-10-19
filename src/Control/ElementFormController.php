<?php

namespace DNADesign\ElementalUserForms\Control;

use DNADesign\Elemental\Controllers\ElementController;
use SilverStripe\Control\Controller;
use SilverStripe\UserForms\Control\UserDefinedFormController;
use SilverStripe\Control\Director;
use SilverStripe\Core\Injector\Injector;

class ElementFormController extends ElementController
{
    private static $allowed_actions = [
        'Form',
        'process',
        'finished'
    ];

    /**
     * @return SilverStripe\Forms\Form
     */
    public function Form()
    {
        $request = $this->getRequest();

        $user = UserDefinedFormController::create($this->element);
        $user->setRequest($request);

        return $user->Form();
    }

    public function process($data)
    {
        $request = $this->getRequest();

        $user = UserDefinedFormController::create($this->element);
        $user->setRequest($request);

        return $user->process($data, $user->Form());
    }

    public function finished()
    {
        $request = $this->getRequest();

        $user = UserDefinedFormController::create($this->element);
        $user->setRequest($request);
        $user->finished();

        $page = $this->getPage();
        $controller = Injector::inst()->create($page->getControllerName(), $this->element->getPage());
        $element = $this->element;

        return $controller->customise([
            'Content' => $element->renderWith($element->getRenderTemplates('_ReceivedFormSubmission')),
        ]);
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
