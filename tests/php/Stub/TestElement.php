<?php

namespace DNADesign\ElementalUserForms\Tests\Stub;

use SilverStripe\Dev\TestOnly;
use DNADesign\Elemental\Models\BaseElement;

class TestElement extends BaseElement implements TestOnly
{
    private static $db = [
        'TestValue' => 'Text'
    ];

    private static $controller_class = TestElementController::class;
}
