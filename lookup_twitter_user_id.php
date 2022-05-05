<?php

/*
 * Copyright 2018 Frostphyr
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require "vendor/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;

include "twitter_tokens.php";

$username = filter_input(INPUT_GET, 'username');

$connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
try {
    $result = $connection->get('users/show', ['screen_name' => $username, 'include_entities' => 'false']);
} catch (TwitterOAuthException $e) {
    error_log($e->getMessage());
}

if (property_exists($result, 'id')) {
    $response['id'] = $result->id;
} else if (property_exists($result, 'errors')) {
    $response['error_code'] = $result->errors[0]->code;
}

if (!isset($response['id']) && !isset($response['error_code'])) {
    $response['error_code'] = -1;
}

echo json_encode($response);