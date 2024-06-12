<?php

/**
 * Feature test for the /ja URL.
 *
 * This test is for the /ja URL. It should return a 200 success response.
 */
it('returns a successful response', function () {
    $response = $this->get('/ja');

    $response->assertStatus(200);
});
