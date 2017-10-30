<?php

namespace DNADesign\ElementalUserForms\Tests;

use DNADesign\ElementalUserForms\Model\ElementForm;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Forms\GridField\GridField;

class ElementFormTests extends SapphireTest
{
    public function testFormDisplaysInCMS()
    {
        $element = new ElementForm;

        $fields = $element->getCMSFields();

        $this->assertNotNull($fields->fieldByName('Root.FormFields'));
        $this->assertInstanceOf(GridField::class, $fields->fieldByName('Root.FormFields.Fields'));
    }
}
