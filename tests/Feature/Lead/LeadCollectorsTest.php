<?php

use App\Models\Lead;
use App\Models\Project;
use App\Models\Unit;
use App\Models\User;

it('provides collectors separately from agents on the admin leads page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $agent = User::factory()->create(['role' => 'agent']);
    $collector = User::factory()->create(['role' => 'collector']);

    $response = $this->actingAs($admin)->get(route('leads'));

    $response->assertOk();
    $response->assertViewHas('agents', fn ($agents) => $agents->modelKeys() === [$agent->id]);
    $response->assertViewHas('collectors', fn ($collectors) => $collectors->modelKeys() === [$collector->id]);
    $response->assertSee('x-for="agent in agents"', false);
    $this->assertMatchesRegularExpression(
        '/<select name="collector_id"[^>]*>.*?<template x-for="collector in collectors"/s',
        $response->getContent()
    );
});

it('rejects assigning a deal to a user who is not a collector', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $agent = User::factory()->create(['role' => 'agent']);
    $lead = Lead::create([
        'full_name' => 'Test Lead',
        'phone' => '0912345001',
        'lead_source' => 'website',
        'lead_type' => 'buyer',
        'current_stage' => 'new',
    ]);
    $project = Project::create([
        'project_name' => 'Test Project',
        'project_manager' => 'Test Manager',
        'location_address' => 'Test Address',
        'total_floors' => 10,
        'completed_floors' => 2,
        'total_units' => 20,
        'due_date' => now()->addYear()->toDateString(),
    ]);
    $unit = Unit::create([
        'project_id' => $project->id,
        'unit_number' => 'TEST-01',
        'floor' => 1,
        'unit_type' => 'apartment',
        'size' => 80,
        'price' => 1000000,
        'status' => 'available',
    ]);

    $response = $this->actingAs($admin)->post(route('createDeal', $lead), [
        'project_id' => $project->id,
        'unit_id' => $unit->id,
        'collector_id' => $agent->id,
        'deal_amount' => 1000000,
        'down_payment' => 100000,
        'payment_cycle' => 'monthly',
        'number_of_installments' => 12,
        'start_date' => now()->toDateString(),
        'commission_type' => 'percentage',
        'commission_value' => 2.5,
        'beneficiary' => 'internal_agent',
        'commission_trigger' => 'each_payment',
    ]);

    $response->assertSessionHasErrors('collector_id');
    $this->assertDatabaseCount('deals', 0);
});
