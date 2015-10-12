<?php

class Ionpay
{
	public static function remote_caller($url, $req_params)
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/x-www-form-urlencoded',
			'Accept: application/json'
			));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($req_params));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . "/cacert.pem");

		$result = curl_exec($ch);
		curl_close($ch);

		if ($result === FALSE ) {
			throw new Exception('CURL Error: ' . curl_error($ch), curl_errno($ch));
		}
		else {
			$result_array = json_decode($result);
			return $result_array;
		}
	}
}