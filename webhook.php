<?php

/**
 * Copyright 2016 LINE Corporation
 *
 * LINE Corporation licenses this file to you under the Apache License,
 * version 2.0 (the "License"); you may not use this file except in compliance
 * with the License. You may obtain a copy of the License at:
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

require_once('./LINEBotTiny.php');

$channelAccessToken = '#######';
$channelSecret = '#######';

$client = new LINEBotTiny($channelAccessToken, $channelSecret);
foreach ($client->parseEvents() as $event) {
	switch ($event['type']) {
		case 'message':
			$message = $event['message'];
			switch ($message['type']) {
				case 'text':
					$client->replyMessage([
						'replyToken' => $event['replyToken'],
						'messages' => [
							[
							'type' => 'text',
							'text' => $message['text']
							]
						]
					]);
					break;
				case 'location':
					$lat = $message['latitude'];
					$lng = $message['longitude'];
					$hotUrl = 'http://webservice.recruit.co.jp/hotpepper/gourmet/v1/?key=e8a77202e4c8db72&lat=' . $lat . '&lng=' . $lng . '&range=5&order=4&format=json';
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $hotUrl);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					$result = curl_exec($ch);
					curl_close($ch);
					$data = json_decode($result, true);
					if (!empty($data['results']['shop'])) {
						$shopInfo0 = $data['results']['shop']['0']['name'] . "\n" . 'URL:' . $data['results']['shop']['0']['urls']['pc'];
						$shopInfo1 = $data['results']['shop']['1']['name'] . "\n" . 'URL:' . $data['results']['shop']['1']['urls']['pc'];
						$shopInfo2 = $data['results']['shop']['2']['name'] . "\n" . 'URL:' . $data['results']['shop']['2']['urls']['pc'];
						$shopInfo3 = $data['results']['shop']['3']['name'] . "\n" . 'URL:' . $data['results']['shop']['3']['urls']['pc'];
						$shopInfo4 = $data['results']['shop']['4']['name'] . "\n" . 'URL:' . $data['results']['shop']['4']['urls']['pc'];
						$client->replyMessage([
							'replyToken' => $event['replyToken'],
							'messages' => [
								[
								'type' => 'text',
								'text' => $shopInfo0
								],
								[
								'type' => 'text',
								'text' => $shopInfo1
								],
								[
								'type' => 'text',
								'text' => $shopInfo2
								],
								[
								'type' => 'text',
								'text' => $shopInfo3
								],
								[
								'type' => 'text',
								'text' => $shopInfo4
								]
							]
						]);
					} else {
						$client->replyMessage([
							'replyToken' => $event['replyToken'],
							'messages' => [
								[
								'type' => 'text',
								'text' => '近くにお店がありません'
								]
							]
						]);	
					}
					break;
				default:
					$client->replyMessage([
						'replyToken' => $event['replyToken'],
						'messages' => [
							[
							'type' => 'text',
							'text' => '位置情報かメッセージしか対応していません。'
							]
						]
					]);	
					break;
			}
		default:
			error_log('Unsupported event type: ' . $event['type']);
			break;
	}
};
