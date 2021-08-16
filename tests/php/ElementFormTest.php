<?php

namespace DNADesign\ElementalUserForms\Tests;

use DNADesign\ElementalUserForms\Model\ElementForm;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\UserForms\Model\EditableFormField\EditableTextField;

class ElementFormTests extends SapphireTest
{
    public function testFormDisplaysInCMS()
    {
        $element = new ElementForm;

        $fields = $element->getCMSFields();

        $this->assertNotNull($fields->fieldByName('Root.FormFields'));
        $this->assertInstanceOf(GridField::class, $fields->fieldByName('Root.FormFields.Fields'));
    }

    public function testDuplicate()
    {
        $element = new ElementForm();
        $field = new EditableTextField();
        $field->Title = 'ABC';
        $field->write();
        $element->Fields()->add($field);
        $element2 = $element->duplicate();
        $this->assertSame('ABC', $element2->Fields()->last()->Title);
    }
}
