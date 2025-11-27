<?php

namespace Tests\Unit;

use Tests\TestCase;

class CorsConfigTest extends TestCase
{
    public function test_cors_paths_include_face_endpoints(): void
    {
        $paths = config('cors.paths');
        $this->assertIsArray($paths);
        $this->assertTrue(in_array('face/*', $paths));
    }
}