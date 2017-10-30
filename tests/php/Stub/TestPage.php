<?php

namespace DNADesign\ElementalUserForms\Tests\Stub;

use Page;
use DNADesign\Elemental\Extensions\ElementalPageExtension;
use SilverStripe\Dev\TestOnly;

class TestPage extends Page implements TestOnly
{
    private static $extensions = [
        ElementalPageExtension::class
    ];
}
