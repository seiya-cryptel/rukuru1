<?php

/**
 * Feature test for G001N004GetRootEs
 *
 * Feature test for root es page.
 *
 */
it('returns a normal response and show welcome ja page', function () {
    $response = $this->get('/es');

    $response->assertStatus(200);
});
