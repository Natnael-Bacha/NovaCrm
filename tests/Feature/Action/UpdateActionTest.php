<?php

use App\Models\Action;
use App\Models\Lead;
use App\Models\User;

it('allows an admin to update an action', function () {

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
        'description' => 'Original description',
    ]);

    $response = $this->actingAs($admin)->put(
        route('updateAction', $action),
        [
            'lead_id' => $lead->id,
            'activity_type' => 'meeting',
            'assigned_to' => $admin->id,
            'status' => 'done',
            'scheduled_time' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'description' => 'Updated description',
        ]
    );

    $response->assertRedirect();

    $response->assertSessionHas(
        'success',
        'Action updated successfully.'
    );

    $this->assertDatabaseHas('actions', [
        'id' => $action->id,
        'lead_id' => $lead->id,
        'activity_type' => 'meeting',
        'assigned_to' => $admin->id,
        'status' => 'done',
        'description' => 'Updated description',
    ]);
});

it('prevents a non-admin from updating an action', function () {

    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

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
        'assigned_to' => $admin->id,
        'status' => 'on_progress',
        'scheduled_time' => now()->addDay(),
        'description' => 'Original description',
    ]);

    $response = $this->actingAs($agent)->put(
        route('updateAction', $action),
        [
            'lead_id' => $lead->id,
            'activity_type' => 'meeting',
            'assigned_to' => $agent->id,
            'status' => 'done',
            'scheduled_time' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'description' => 'Updated description',
        ]
    );

    $response->assertForbidden();

    $this->assertDatabaseHas('actions', [
        'id' => $action->id,
        'activity_type' => 'follow_up_call',
        'status' => 'on_progress',
        'description' => 'Original description',
    ]);
});

it('rejects an invalid activity type when updating an action', function () {

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
        'description' => 'Original description',
    ]);

    $response = $this->actingAs($admin)->put(
        route('updateAction', $action),
        [
            'lead_id' => $lead->id,
            'activity_type' => 'invalid_activity',
            'assigned_to' => $admin->id,
            'status' => 'on_progress',
            'scheduled_time' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'description' => 'Updated description',
        ]
    );

    $response->assertSessionHasErrors('activity_type');

    $this->assertDatabaseHas('actions', [
        'id' => $action->id,
        'activity_type' => 'follow_up_call',
    ]);
});

it('rejects an invalid assigned user when updating an action', function () {

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
        'description' => 'Original description',
    ]);

    $response = $this->actingAs($admin)->put(
        route('updateAction', $action),
        [
            'lead_id' => $lead->id,
            'activity_type' => 'meeting',
            'assigned_to' => 999999,
            'status' => 'done',
            'scheduled_time' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'description' => 'Updated description',
        ]
    );

    $response->assertSessionHasErrors('assigned_to');

    $this->assertDatabaseHas('actions', [
        'id' => $action->id,
        'assigned_to' => $admin->id,
    ]);
});

it('rejects an invalid status when updating an action', function () {

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
        'description' => 'Original description',
    ]);

    $response = $this->actingAs($admin)->put(
        route('updateAction', $action),
        [
            'lead_id' => $lead->id,
            'activity_type' => 'meeting',
            'assigned_to' => $admin->id,
            'status' => 'invalid_status',
            'scheduled_time' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'description' => 'Updated description',
        ]
    );

    $response->assertSessionHasErrors('status');

    $this->assertDatabaseHas('actions', [
        'id' => $action->id,
        'status' => 'on_progress',
    ]);
});

it('rejects a scheduled time in the past when updating an action', function () {

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
        'description' => 'Original description',
    ]);

    $response = $this->actingAs($admin)->put(
        route('updateAction', $action),
        [
            'lead_id' => $lead->id,
            'activity_type' => 'meeting',
            'assigned_to' => $admin->id,
            'status' => 'done',
            'scheduled_time' => now()->subDay()->format('Y-m-d H:i:s'),
            'description' => 'Updated description',
        ]
    );

    $response->assertSessionHasErrors('scheduled_time');

    $this->assertDatabaseHas('actions', [
        'id' => $action->id,
        'scheduled_time' => $action->scheduled_time,
    ]);
});

it('allows an action to be updated without a description', function () {

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
        'description' => 'Original description',
    ]);

    $response = $this->actingAs($admin)->put(
        route('updateAction', $action),
        [
            'lead_id' => $lead->id,
            'activity_type' => 'meeting',
            'assigned_to' => $admin->id,
            'status' => 'done',
            'scheduled_time' => now()->addDays(2)->format('Y-m-d H:i:s'),
        ]
    );

    $response->assertRedirect();

    $this->assertDatabaseHas('actions', [
        'id' => $action->id,
        'activity_type' => 'meeting',
        'status' => 'done',
        'description' => null,
    ]);
});

it('strips HTML tags when updating an action', function () {

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
        'description' => 'Original description',
    ]);

    $response = $this->actingAs($admin)->put(
        route('updateAction', $action),
        [
            'lead_id' => $lead->id,
            'activity_type' => 'meeting',
            'assigned_to' => $admin->id,
            'status' => 'done',
            'scheduled_time' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'description' => '<b>Updated</b> description',
        ]
    );

    $response->assertRedirect();

    $this->assertDatabaseHas('actions', [
        'id' => $action->id,
        'description' => 'Updated description',
    ]);
});
