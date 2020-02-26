<?php

/*
 * Copyright (c) 2015 Syntax Era Development Studio
 *
 * Licensed under the MIT License (the "License"); you may not use this
 * file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 *      https://opensource.org/licenses/MIT
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace SparkPost\Mailer\Transport;

use Cake\Core\Configure;
use Cake\Mailer\AbstractTransport;
use Cake\Mailer\Email;
use GuzzleHttp\Client;
use Ivory\HttpAdapter\CakeHttpAdapter;
use SparkPost\APIResponseException;
use SparkPost\SparkPost;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;

/**
 * Spark Post Transport Class
 *
 * Provides an interface between the CakePHP Email functionality and the SparkPost API.
 *
 * @package SparkPost\Mailer\Transport
 */
class SparkPostTransport extends AbstractTransport
{
	/**
	 * Send mail via SparkPost REST API
	 *
	 * @param \Cake\Mailer\Email $email Email message
	 * @return array
	 */
	public function send(Email $email)
	{
		// Load SparkPost configuration settings
		$apiKey = $this->setConfig('apiKey');

		// Set up HTTP request adapter
		$adapter = new GuzzleAdapter(new Client());

		// Create SparkPost API accessor
		$sparkpost = new SparkPost(
			$adapter,
			[
				'key' => $apiKey,
				'async' => false
			]
		);

		// Pre-process CakePHP email object fields
		$to = $email->getTo();

		foreach ($to as $toEmail => $toName) {
			$recipients[] = [
				'address' => [
					'name' => mb_encode_mimeheader($toName),
					'email' => $toEmail
				]
			];
		}

		$bcc = $email->getBcc();

		foreach ($bcc as $bccMail) {
			$recipients[] = [
				'address' => [
					'email' => $bccMail,
					'header_to' => $toEmail
				]
			];
		}

		// Build message to send
		$message = [
			'content' => [
				$email->getEmailFormat() => $email->message($email->getEmailFormat()),
				'text' => $email->message('text'),
				'subject' => mb_decode_mimeheader($email->getSubject()),
			],
			'recipients' => $recipients
		];

		foreach ($email->getFrom() as $mail => $name) {
			$message['content']['from'] = [
				'name' => mb_encode_mimeheader($name),
				'email' => $mail,
			];
            //$message['return_path'] = $mail;
		}

		foreach ($email->getReplyTo() as  $mail => $name) {
			$message['content']['reply_to'] = $mail;
		}

		foreach ($email->getAttachments() as $name => $data) {
            if (in_array($data['mimetype'], ['image/jpeg', 'image/png'])) {
                $key = 'inline_images';
            } else {
                $key = 'attachments';
            }

            $message['content'][$key][] = [
				'name' => $name,
				'type' => $data['mimetype'],
				'data' => base64_encode(file_get_contents($data['file']))
			];
		}

		// Send message
		try {
			return $sparkpost->transmissions->post($message);
		} catch(APIResponseException $e) {
			// TODO: Determine if BRE is the best exception type
			throw new BadRequestException(sprintf('SparkPost API error %d (%d): %s (%s)',
				$e->getAPICode(), $e->getCode(), ucfirst($e->getAPIMessage()), $e->getAPIDescription()));
		}
	}
}
