<?php
class CloudinaryService {
    private $cloudName = 'dlmaw4de5';
    private $apiKey = '391585418545416';
    private $apiSecret = '9XaBev8kuJ-bLJCSQurCTwXivDM';

    public function uploadImage($fileTmpPath) {
        $timestamp = time();
        $params = [
            'timestamp' => $timestamp,
            'upload_preset' => null // We are using signed upload, so no preset needed if we sign correctly
        ];
        
        // Signature generation:
        // 1. Sort parameters by name (timestamp is the only one here for now)
        // 2. Create string: key=value&key=value...
        // 3. Append api_secret
        // 4. SHA1 hash
        
        $stringToSign = "timestamp=" . $timestamp . $this->apiSecret;
        $signature = sha1($stringToSign);

        $postFields = [
            'api_key' => $this->apiKey,
            'timestamp' => $timestamp,
            'signature' => $signature,
            'file' => new CURLFile($fileTmpPath)
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.cloudinary.com/v1_1/{$this->cloudName}/image/upload");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // Disable SSL verification for local dev if needed (better to have it enabled but sometimes causes issues on local xampp)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            error_log('Cloudinary cURL Error: ' . curl_error($ch));
            curl_close($ch);
            return false;
        }
        curl_close($ch);

        if ($httpCode !== 200) {
            error_log('Cloudinary API Error: ' . $response);
            return false;
        }

        $data = json_decode($response, true);
        return $data['secure_url'] ?? false;
    }
}
?>
