<?php

namespace DNADesign\ElementalUserForms\Tests\Stub;

use DNADesign\Elemental\Models\BaseElement;
use SilverStripe\Dev\TestOnly;

class TestElement extends BaseElement implements TestOnly
{
    private static $db = [
        'TestValue' => 'Text'
    ];

    private static $controller_class = TestElementController::class;
}
