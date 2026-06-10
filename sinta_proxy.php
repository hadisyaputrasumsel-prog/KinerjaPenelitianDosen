<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$name = isset($_GET['name']) ? urlencode($_GET['name']) : '';
if (!$name) {
    echo json_encode(["error" => "No name provided"]);
    exit;
}

$url = "https://sinta.kemdiktisaintek.go.id/authors?aff=8263&q=" . $name;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
// Add fake headers to bypass basic checks
$headers = [
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
    'Accept-Language: en-US,en;q=0.9,id;q=0.8',
    'Connection: keep-alive',
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$html = curl_exec($ch);
curl_close($ch);

if (preg_match_all('/profile\/(\d+)/', $html, $matches)) {
    // Assuming the first match is the best match
    $id = $matches[1][0];
    echo json_encode(["success" => true, "id" => $id]);
} else {
    echo json_encode(["success" => false, "message" => "ID not found or blocked by Cloudflare"]);
}
?>
