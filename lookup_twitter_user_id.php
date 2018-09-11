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

<?php

require "vendor/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;

include "twitter_tokens.php";

$username = filter_input(INPUT_GET, 'username');

$connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
$result = $connection->get('users/show', ['screen_name' => $username, 'include_entities' => 'false']);

$response;
if (property_exists($result, 'id')) {
    $response['id'] = $result->id;
} else if (property_exists($result, 'errors')) {
    foreach ($result->errors as $error) {
        $code = $error->code;
        if ($code == 50) {
            $response['error'] = 'User not found';
            break;
        } else if ($code == 63) {
            $response['error'] = 'User is suspended';
            break;
        }
    }
}

if (!isset($response['id']) && !isset($response['error'])) {
    $response['error'] = 'Error validating username';
    error_log(json_encode($result));
}

echo json_encode($response);