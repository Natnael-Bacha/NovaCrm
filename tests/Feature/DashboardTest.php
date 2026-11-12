<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get('/admin/dashboard');
    $response->assertRedirect('/login');
});

test('authenticated admin users can visit the dashboard', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $this->actingAs($user);

    $response = $this->get('/admin/dashboard');
    $response->assertOk();
    $response->assertSee('id="unitStatusChart"', false);
    $response->assertSee('id="leadPipelineChart"', false);
    $response->assertSee('id="salesTrendChart"', false);
    $response->assertSee('id="leadSourcesChart"', false);
    $response->assertSee('id="projectProgressChart"', false);
    $response->assertSee('window.dashboardCharts', false);
});

test('non-admin authenticated users cannot access the dashboard', function () {
    $user = User::factory()->create(['role' => 'agent']);
    $this->actingAs($user);

    $response = $this->get('/admin/dashboard');
    $response->assertForbidden();
});