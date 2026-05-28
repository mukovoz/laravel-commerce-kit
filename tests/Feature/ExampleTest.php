<?php

namespace Lantera\ExtensionFramework\Tests\Feature;

use Lantera\ExtensionFramework\Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_service_provider_is_loaded(): void
    {
        // Confirms the package boots correctly inside the Laravel container
        $this->assertTrue(true);
    }
}
