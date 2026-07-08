<?php

namespace Tests\Feature;

use Tests\TestCase;

class SidebarLayoutTest extends TestCase
{
    public function test_management_page_renders_a_single_page_shell_with_sidebar(): void
    {
        $this->withSession(['logged_in' => true, 'user_email' => 'admin@example.com']);

        $response = $this->get('/surat/tambah');

        $response->assertStatus(200);
        $response->assertSee('Sistem Surat');
        $response->assertSee('Manajemen Surat');
        $response->assertSee('Buat Nomor Surat');
    }
}
