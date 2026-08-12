<?php

use App\Models\Action;
use App\Models\Lead;
use App\Models\User;

it('allows an admin to create an action', function () {
    
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

    
    $response = $this->actingAs($admin)->post(
        route('createAction', $lead),
        [
            'activity_type' => 'follow_up_call',
            'assigned_to' => $admin->id,
            'status' => 'on_progress',
            'scheduled_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'description' => 'Follow up with the customer about the property.',
        ]
    );

    
    $response->assertRedirect();

    $response->assertSessionHas(
        'success',
        'Action created successfully'
    );

    $this->assertDatabaseHas('actions', [
        'lead_id' => $lead->id,
        'activity_type' => 'follow_up_call',
        'assigned_to' => $admin->id,
        'status' => 'on_progress',
        'description' => 'Follow up with the customer about the property.',
    ]);
});


it('rejects an invalid activity type when creating an action', function () {
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

    $response = $this->actingAs($admin)->post(
        route('createAction', $lead),
        [
            'activity_type' => 'invalid_activity',
            'assigned_to' => $admin->id,
            'status' => 'on_progress',
            'scheduled_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'description' => 'Follow up with the customer.',
        ]
    );

    $response->assertSessionHasErrors('activity_type');

    $this->assertDatabaseMissing('actions', [
    'lead_id' => $lead->id,
]);
});

it('prevents a non-admin from creating an action', function () {
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

    $response = $this->actingAs($agent)->post(
        route('createAction', $lead),
        [
            'activity_type' => 'follow_up_call',
            'assigned_to' => $agent->id,
            'status' => 'on_progress',
            'scheduled_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'description' => 'Follow up with the customer.',
        ]
    );

    $response->assertForbidden();

    $this->assertDatabaseMissing('actions', [
        'lead_id' => $lead->id,
    ]);
});


it('requires all required fields when creating an action', function () {
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

    $response = $this->actingAs($admin)->post(
        route('createAction', $lead),
        []
    );

    $response->assertSessionHasErrors([
        'activity_type',
        'assigned_to',
        'status',
        'scheduled_time',
    ]);

    $this->assertDatabaseMissing('actions', [
        'lead_id' => $lead->id,
    ]);
});


it('rejects an invalid assigned user when creating an action', function () {
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

    $response = $this->actingAs($admin)->post(
        route('createAction', $lead),
        [
            'activity_type' => 'follow_up_call',
            'assigned_to' => 999999,
            'status' => 'on_progress',
            'scheduled_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'description' => 'Follow up with the customer.',
        ]
    );

    $response->assertSessionHasErrors('assigned_to');

    $this->assertDatabaseMissing('actions', [
        'lead_id' => $lead->id,
    ]);
});


it('rejects an invalid status when creating an action', function () {
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

    $response = $this->actingAs($admin)->post(
        route('createAction', $lead),
        [
            'activity_type' => 'follow_up_call',
            'assigned_to' => $admin->id,
            'status' => 'invalid_status',
            'scheduled_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'description' => 'Follow up with the customer.',
        ]
    );

    $response->assertSessionHasErrors('status');

    $this->assertDatabaseMissing('actions', [
        'lead_id' => $lead->id,
    ]);
});


it('rejects a scheduled time in the past when creating an action', function () {
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

    $response = $this->actingAs($admin)->post(
        route('createAction', $lead),
        [
            'activity_type' => 'follow_up_call',
            'assigned_to' => $admin->id,
            'status' => 'on_progress',
            'scheduled_time' => now()->subDay()->format('Y-m-d H:i:s'),
            'description' => 'Follow up with the customer.',
        ]
    );

    $response->assertSessionHasErrors('scheduled_time');

    $this->assertDatabaseMissing('actions', [
        'lead_id' => $lead->id,
    ]);
});


it('allows an action to be created without a description', function () {
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

    $response = $this->actingAs($admin)->post(
        route('createAction', $lead),
        [
            'activity_type' => 'meeting',
            'assigned_to' => $admin->id,
            'status' => 'on_progress',
            'scheduled_time' => now()->addDay()->format('Y-m-d H:i:s'),
        ]
    );

    $response->assertRedirect();

    $this->assertDatabaseHas('actions', [
        'lead_id' => $lead->id,
        'activity_type' => 'meeting',
        'assigned_to' => $admin->id,
        'status' => 'on_progress',
        'description' => null,
    ]);
});


it('strips HTML tags from action input when creating an action', function () {
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

    $response = $this->actingAs($admin)->post(
        route('createAction', $lead),
        [
            'activity_type' => 'follow_up_call',
            'assigned_to' => $admin->id,
            'status' => 'on_progress',
            'scheduled_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'description' => '<b>Call</b> the customer',
        ]
    );

    $response->assertRedirect();

    $this->assertDatabaseHas('actions', [
        'lead_id' => $lead->id,
        'description' => 'Call the customer',
    ]);
});


