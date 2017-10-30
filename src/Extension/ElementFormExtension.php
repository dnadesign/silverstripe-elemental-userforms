<?php

namespace DNADesign\ElementalUserForms\Extension;

use SilverStripe\Forms\FieldGroup;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\ORM\DataExtension;

class ElementFormExtension extends DataExtension
{
    public function updateCMSFields(FieldList $fields)
    {
        $this->moveTitleAndDisplayedCheckbox($fields);
    }

    /**
     * Move the Title and Displayed checkbox from the Content to Form Fields tab
     *
     * @param FieldList $fields
     * @return $this
     */
    protected function moveTitleAndDisplayedCheckbox(FieldList $fields)
    {
        /** @see DNADesign\Elemental\Models\BaseElement */
        $composite = $fields->fieldByName('Root.Main.TitleAndDisplayed');

        $fields->removeByName('TitleAndDisplayed');
        $fields->findOrMakeTab('Root.FormFields')->unshift($composite);

        return $this;
    }
}
