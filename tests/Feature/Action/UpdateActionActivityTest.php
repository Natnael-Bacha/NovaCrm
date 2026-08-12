<?php

use App\Models\Action;
use App\Models\Lead;
use App\Models\User;


it('allows an admin to update an action activity type', function () {

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
        route('updateActionActivity', $action),
        [
            'activity_type' => 'meeting',
        ]
    );

    $response->assertRedirect();

    $response->assertSessionHas(
        'success',
        'Activity type updated successfully.'
    );

    $this->assertDatabaseHas('actions', [
        'id' => $action->id,
        'activity_type' => 'meeting',
    ]);
});


it('prevents a non-admin from updating an action activity type', function () {

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
        'description' => 'Follow up with the customer.',
    ]);

    $response = $this->actingAs($agent)->put(
        route('updateActionActivity', $action),
        [
            'activity_type' => 'meeting',
        ]
    );

    $response->assertForbidden();

    $this->assertDatabaseHas('actions', [
        'id' => $action->id,
        'activity_type' => 'follow_up_call',
    ]);
});


it('rejects an invalid activity type when updating an action activity', function () {

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
        route('updateActionActivity', $action),
        [
            'activity_type' => 'invalid_activity',
        ]
    );

    $response->assertSessionHasErrors('activity_type');

    $this->assertDatabaseHas('actions', [
        'id' => $action->id,
        'activity_type' => 'follow_up_call',
    ]);
});


it('only changes the activity type when updating action activity', function () {

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

    $scheduledTime = now()->addDay();

    $action = Action::create([
        'lead_id' => $lead->id,
        'activity_type' => 'follow_up_call',
        'assigned_to' => $admin->id,
        'status' => 'on_progress',
        'scheduled_time' => $scheduledTime,
        'description' => 'Original description.',
    ]);

    $response = $this->actingAs($admin)->put(
        route('updateActionActivity', $action),
        [
            'activity_type' => 'property_visit',
        ]
    );

    $response->assertRedirect();

    $this->assertDatabaseHas('actions', [
        'id' => $action->id,
        'activity_type' => 'property_visit',
        'assigned_to' => $admin->id,
        'status' => 'on_progress',
        'description' => 'Original description.',
    ]);
});


it('prevents an unauthenticated user from updating action activity', function () {

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

    $response = $this->put(
        route('updateActionActivity', $action),
        [
            'activity_type' => 'meeting',
        ]
    );

    $response->assertRedirect(route('login'));

    $this->assertDatabaseHas('actions', [
        'id' => $action->id,
        'activity_type' => 'follow_up_call',
    ]);
});