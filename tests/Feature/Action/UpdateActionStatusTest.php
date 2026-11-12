<?php

use App\Models\Action;
use App\Models\Lead;
use App\Models\User;

it('allows an admin to update an action status', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $lead = Lead::create([
        'full_name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '0912345678',
        'budget_range' => '1,000,000',
        'preferred_location' => 'Bole',
        'lead_source' => 'website',
        'lead_type' => 'buyer',
        'current_stage' => 'new',
    ]);

    $action = Action::create([
        'lead_id' => $lead->id,
        'activity_type' => 'follow_up_call',
        'assigned_to' => $admin->id,
        'status' => 'on_progress',
        'scheduled_time' => now()->addDay(),
        'description' => 'Follow up with the customer.',
    ]);

    $response = $this->actingAs($admin)->put(
        route('updateActionStatus', $action),
        [
            'status' => 'done',
        ]
    );

    $response->assertRedirect();

    $response->assertSessionHas(
        'success',
        'Status updated successfully.'
    );

    $this->assertDatabaseHas('actions', [
        'id' => $action->id,
        'status' => 'done',
    ]);
});

it('rejects an invalid status when updating an action status', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $lead = Lead::create([
        'full_name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '0912345678',
        'budget_range' => '1,000,000',
        'preferred_location' => 'Bole',
        'lead_source' => 'website',
        'lead_type' => 'buyer',
        'current_stage' => 'new',
    ]);

    $action = Action::create([
        'lead_id' => $lead->id,
        'activity_type' => 'follow_up_call',
        'assigned_to' => $admin->id,
        'status' => 'on_progress',
        'scheduled_time' => now()->addDay(),
        'description' => 'Follow up with the customer.',
    ]);

    $response = $this->actingAs($admin)->put(
        route('updateActionStatus', $action),
        [
            'status' => 'invalid_status',
        ]
    );

    $response->assertSessionHasErrors('status');

    $this->assertDatabaseHas('actions', [
        'id' => $action->id,
        'status' => 'on_progress',
    ]);
});

it('prevents a non-admin from updating an action status', function () {
    $agent = User::factory()->create([
        'role' => 'agent',
    ]);

    $lead = Lead::create([
        'full_name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '0912345678',
        'budget_range' => '1,000,000',
        'preferred_location' => 'Bole',
        'lead_source' => 'website',
        'lead_type' => 'buyer',
        'current_stage' => 'new',
    ]);

    $action = Action::create([
        'lead_id' => $lead->id,
        'activity_type' => 'follow_up_call',
        'assigned_to' => $agent->id,
        'status' => 'on_progress',
        'scheduled_time' => now()->addDay(),
        'description' => 'Follow up with the customer.',
    ]);

    $response = $this->actingAs($agent)->put(
        route('updateActionStatus', $action),
        [
            'status' => 'done',
        ]
    );

    $response->assertForbidden();

    $this->assertDatabaseHas('actions', [
        'id' => $action->id,
        'status' => 'on_progress',
    ]);
});

it('requires the status when updating an action status', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $lead = Lead::create([
        'full_name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '0912345678',
        'budget_range' => '1,000,000',
        'preferred_location' => 'Bole',
        'lead_source' => 'website',
        'lead_type' => 'buyer',
        'current_stage' => 'new',
    ]);

    $action = Action::create([
        'lead_id' => $lead->id,
        'activity_type' => 'follow_up_call',
        'assigned_to' => $admin->id,
        'status' => 'on_progress',
        'scheduled_time' => now()->addDay(),
        'description' => 'Follow up with the customer.',
    ]);

    $response = $this->actingAs($admin)->put(
        route('updateActionStatus', $action),
        []
    );

    $response->assertSessionHasErrors('status');

    $this->assertDatabaseHas('actions', [
        'id' => $action->id,
        'status' => 'on_progress',
    ]);
});

it('allows an admin to change an action status from done to on progress', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $lead = Lead::create([
        'full_name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '0912345678',
        'budget_range' => '1,000,000',
        'preferred_location' => 'Bole',
        'lead_source' => 'website',
        'lead_type' => 'buyer',
        'current_stage' => 'new',
    ]);

    $action = Action::create([
        'lead_id' => $lead->id,
        'activity_type' => 'meeting',
        'assigned_to' => $admin->id,
        'status' => 'done',
        'scheduled_time' => now()->addDay(),
        'description' => 'Property meeting.',
    ]);

    $response = $this->actingAs($admin)->put(
        route('updateActionStatus', $action),
        [
            'status' => 'on_progress',
        ]
    );

    $response->assertRedirect();

    $this->assertDatabaseHas('actions', [
        'id' => $action->id,
        'status' => 'on_progress',
    ]);
});

it('allows a supervisor to update action status for their own agent', function () {
    $supervisor = User::factory()->create(['role' => 'supervisor']);
    $agent = User::factory()->create([
        'role' => 'agent',
        'supervisor_id' => $supervisor->id,
    ]);
    $lead = Lead::create([
        'full_name' => 'Team Lead',
        'phone' => '0912345101',
        'lead_source' => 'website',
        'lead_type' => 'buyer',
        'current_stage' => 'new',
        'agent_id' => $agent->id,
    ]);
    $action = Action::create([
        'lead_id' => $lead->id,
        'activity_type' => 'follow_up_call',
        'assigned_to' => $agent->id,
        'status' => 'on_progress',
        'scheduled_time' => now()->addDay(),
    ]);

    $response = $this->actingAs($supervisor)->put(route('updateActionStatus', $action), [
        'status' => 'done',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Status updated successfully.');
    $this->assertDatabaseHas('actions', [
        'id' => $action->id,
        'status' => 'done',
    ]);
});

it('prevents a supervisor from updating an action assigned to another supervisors agent', function () {
    $supervisor = User::factory()->create(['role' => 'supervisor']);
    $otherSupervisor = User::factory()->create(['role' => 'supervisor']);
    $agent = User::factory()->create([
        'role' => 'agent',
        'supervisor_id' => $otherSupervisor->id,
    ]);
    $lead = Lead::create([
        'full_name' => 'Other Team Lead',
        'phone' => '0912345102',
        'lead_source' => 'website',
        'lead_type' => 'buyer',
        'current_stage' => 'new',
        'agent_id' => $agent->id,
    ]);
    $action = Action::create([
        'lead_id' => $lead->id,
        'activity_type' => 'follow_up_call',
        'assigned_to' => $agent->id,
        'status' => 'on_progress',
        'scheduled_time' => now()->addDay(),
    ]);

    $response = $this->actingAs($supervisor)->put(route('updateActionStatus', $action), [
        'status' => 'done',
    ]);

    $response->assertForbidden();
    $this->assertDatabaseHas('actions', [
        'id' => $action->id,
        'status' => 'on_progress',
    ]);
});
