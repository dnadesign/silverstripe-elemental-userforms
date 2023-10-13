<?php

namespace DNADesign\ElementalUserForms\Control;

use DNADesign\Elemental\Models\BaseElement;
use DNADesign\Elemental\Controllers\ElementController;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\UserForms\Control\UserDefinedFormController;
use SilverStripe\UserForms\Form\UserForm;

class ElementFormController extends ElementController
{
    private static $allowed_actions = [
        'Form',
        'process',
        'finished'
    ];

    /**
     * @var UserDefinedFormController
     */
    protected $userFormController;

    protected function init()
    {
        parent::init();

        $controller = $this->getUserFormController() ?: UserDefinedFormController::create($this->element);
        $controller->setRequest($this->getRequest());
        $controller->doInit();

        $this->setUserFormController($controller);
    }

    /**
     * @return UserForm
     */
    public function Form()
    {
        return $this->getUserFormController()->Form();
    }

    public function process($data)
    {
        $user = $this->getUserFormController();

        return $user->process($data, $user->Form());
    }

    public function finished()
    {
        $user = $this->getUserFormController();

        $user->finished();

        $page = $this->getPage();

        while ($page instanceof BaseElement) {
            $page = $page->getPage();
        }
        $controller = Injector::inst()->create($page->getControllerName(), $page->data());
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
        $page = Director::get_current_page();
        $id = $this->element->ID;

        if ($this->getAction() === 'finished'
            && $action !== 'finished'
            && $this->getRequest()->param('ID') == $id
        ) {
            $segment = $action;
        } else {
            $segment = Controller::join_links('element', $id, $action);
        }

        if ($page && !($page instanceof ElementController)) {
            return $page->Link($segment);
        }

        if ($controller = $this->getParentController()) {
            return $controller->Link($segment);
        }

        return $segment;
    }

    /**
     * Return the associated UserDefinedFormController
     *
     * @return UserDefinedFormController
     */
    public function getUserFormController()
    {
        return $this->userFormController;
    }

    /**
     * Set the associated UserDefinedFormController
     *
     * @param UserDefinedFormController $controller
     * @return $this
     */
    public function setUserFormController(UserDefinedFormController $controller)
    {
        $this->userFormController = $controller;
        return $this;
    }
}
