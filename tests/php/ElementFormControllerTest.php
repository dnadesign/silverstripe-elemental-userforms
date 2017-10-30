<?php

namespace DNADesign\ElementalUserForms\Tests;

use DNADesign\Elemental\Models\BaseElement;
use DNADesign\ElementalUserForms\Control\ElementFormController;
use DNADesign\ElementalUserForms\Model\ElementForm;
use DNADesign\ElementalUserForms\Tests\Stub\TestElement;
use DNADesign\ElementalUserForms\Tests\Stub\TestPage;
use SilverStripe\Dev\FunctionalTest;
use SilverStripe\UserForms\Control\UserDefinedFormController;
use SilverStripe\Versioned\Versioned;

class ElementFormControllerTest extends FunctionalTest
{
    protected static $fixture_file = 'ElementFormTest.yml';

    protected static $use_draft_site = true;

    protected static $extra_dataobjects = [
        TestPage::class,
        TestElement::class,
    ];

    public function testElementFormRendering()
    {
        $this->logInWithPermission('ADMIN');
        $page = $this->objFromFixture(TestPage::class, 'page1');

        $element = $this->objFromFixture(ElementForm::class, 'formelement');

        $response = $this->get($page->URLSegment);
        $formAction = sprintf('%s/element/%d/Form', $page->URLSegment, $element->ID);

        $this->assertContains(
            $formAction,
            $response->getBody(),
            'Element forms are rendered through ElementalArea templates'
        );
    }

    public function testElementFormSubmission()
    {
        $this->logInWithPermission('ADMIN');
        $page = $this->objFromFixture(TestPage::class, 'page1');

        $element = $this->objFromFixture(TestElement::class, 'element1');

        $response = $this->get($page->URLSegment);

        $response = $this->submitForm('UserForm_Form_2', 'action_process', ['TestValue' => 'Updated']);
        $this->assertContains(
            'received your submission',
            $response->getBody(),
            'Form values are submitted to correct element form'
        );
    }

    public function testUserFormControllerInitIsCalled()
    {
        $userFormControllerMock = $this->getMockBuilder(UserDefinedFormController::class)
            ->setMethods(['doInit'])
            ->getMock();

        $userFormControllerMock->expects($this->once())->method('doInit');

        $controller = new ElementFormController(new BaseElement);
        $controller->getRequest()->setSession($this->session());
        $controller->setUserFormController($userFormControllerMock);

        $this->assertSame($userFormControllerMock, $controller->getUserFormController());
        $controller->doInit();
    }
}
