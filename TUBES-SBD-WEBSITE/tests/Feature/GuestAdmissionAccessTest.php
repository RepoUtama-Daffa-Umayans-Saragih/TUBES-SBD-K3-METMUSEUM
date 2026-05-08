<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('non authenticated visitors are redirected to login before admission', function () {
    $this->get('/admission')
        ->assertRedirect('/login')
        ->assertSessionHas('error');
});

test('visitors with a valid guest session can access admission', function () {
    $this->withSession([
        'guest_user' => [
            'id' => 123,
            'name' => 'Guest Visitor',
        ],
    ])->get('/admission')
        ->assertOk();
});

test('guest login stores a valid guest session structure', function () {
    $this->post('/guest-login', [
        'email' => 'guest@example.com',
        'confirm_email' => 'guest@example.com',
        'first_name' => 'Guest',
        'last_name' => 'Visitor',
    ])
        ->assertRedirect(route('ticket.admission'))
        ->assertSessionHas('guest_user.id')
        ->assertSessionHas('guest_user.name', 'Guest Visitor');
});
