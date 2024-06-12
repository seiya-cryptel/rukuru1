<?php

/**
 * Feature test for the root URL.
 *
 * This test is for the root URL. It should return a 302 redirect response.
 *  -> /ja or /en
 */
it('returns a 302 redirect response', function () {
    $response = $this->get('/');

    $response->assertStatus(302);
});
