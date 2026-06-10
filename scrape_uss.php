<?php
$affId = 8263;
$page = 1;
$lecturers = [];

while(true) {
    echo "Scraping page $page...\n";
    $url = "https://sinta.kemdiktisaintek.go.id/affiliations/authors/$affId?page=$page";
    $options = [
        "http" => [
            "method" => "GET",
            "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n" .
                        "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n"
        ],
        "ssl" => [
            "verify_peer" => false,
            "verify_peer_name" => false,
        ]
    ];
    $context = stream_context_create($options);
    $html = @file_get_contents($url, false, $context);

    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    $items = $xpath->query('//div[contains(@class, "au-item")]');

    if ($items->length == 0) {
        echo "No authors found on this page. Stopping.\n";
        break;
    }

    foreach ($items as $item) {
        // Name & Sinta ID
        $nameNode = $xpath->query('.//div[@class="profile-name"]/a', $item)->item(0);
        if (!$nameNode) continue;
        $name = trim($nameNode->textContent);
        $href = $nameNode->getAttribute('href');
        $sintaId = 0;
        if (preg_match('/profile\/(\d+)/', $href, $m)) {
            $sintaId = $m[1];
        }

        // Prodi
        $prodiNode = $xpath->query('.//div[@class="profile-dept"]/a', $item)->item(0);
        $prodi = $prodiNode ? trim(preg_replace('/<i[^>]*><\/i>/', '', $prodiNode->nodeValue)) : 'Belum Diketahui';
        $prodi = trim($prodi);

        // Stats
        $statNodes = $xpath->query('.//div[contains(@class, "stat-num")]', $item);
        $sinta3Yr = 0;
        $sintaOverall = 0;
        if ($statNodes->length >= 2) {
            $sinta3Yr = (int) str_replace(',', '', trim($statNodes->item(0)->textContent));
            $sintaOverall = (int) str_replace(',', '', trim($statNodes->item(1)->textContent));
        }

        // Image URL
        $imgNode = $xpath->query('.//img[contains(@class, "avatar")]', $item)->item(0);
        $imageUrl = $imgNode ? $imgNode->getAttribute('src') : null;

        $lecturers[] = [
            'name' => $name,
            'sintaId' => $sintaId,
            'prodi' => $prodi,
            'image_url' => $imageUrl,
            'sintaOverall' => $sintaOverall,
            'sinta3Yr' => $sinta3Yr,
            'scholar' => 0,
            'scopus' => 0,
            'scopusHIndex' => 0,
            'hIndex' => 0
        ];
    }
    
    // Check if next page exists. The pagination usually has a link with ?page=2
    if (!strpos($html, "?page=" . ($page + 1))) {
        break;
    }
    $page++;
    sleep(1);
}

file_put_contents("database/data/lecturers.json", json_encode($lecturers, JSON_PRETTY_PRINT));
echo "Saved " . count($lecturers) . " lecturers to lecturers.json\n";
