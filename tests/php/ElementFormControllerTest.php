<?php

namespace DNADesign\ElementalUserForms\Tests;

use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\Dev\FunctionalTest;
use SilverStripe\Dev\TestOnly;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Form;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\TextField;
use DNADesign\Elemental\Controllers\ElementController;
use DNADesign\Elemental\Models\ElementalArea;
use DNADesign\Elemental\Tests\Src\TestElement;
use DNADesign\Elemental\Tests\Src\TestPage;
use DNADesign\ElementalUserForms\Model\ElementForm;
use SilverStripe\Versioned\Versioned;

class ElementFormControllerTest extends FunctionalTest
{
    protected static $fixture_file = 'ElementFormTest.yml';

    protected static $use_draft_site = true;

    protected static $extra_dataobjects = array(
        TestPage::class,
        TestElement::class
    );

    protected function setUp()
    {
        Versioned::set_stage(Versioned::DRAFT);
        parent::setUp();
    }

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

        $response = $this->submitForm('UserForm_Form_2', 'action_process', array('TestValue' => 'Updated'));
        $this->assertContains(
            'received your submission',
            $response->getBody(),
            'Form values are submitted to correct element form'
        );
    }
}
