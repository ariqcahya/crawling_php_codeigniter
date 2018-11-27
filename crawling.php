function test(){
        
        $obj2 = array(
            "keywords" => "lele",
            "pages" =>1
        );

        $cookie = __DIR__ . '/cookie.txt';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL, 'http://cybex.pertanian.go.id/search?inputSearch='.$obj2['keywords'].'&page='.$obj2['pages']);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36');
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $data = curl_exec($ch);
        curl_close($ch);

        // echo $data;
        // $doc = new \DOMDocument();
        $doc = new DOMDocument;
        libxml_use_internal_errors(true);
        $doc->loadHTML($data);
        libxml_clear_errors();
        // $doc->loadHTML($data);

        $xpath = new \DOMXpath($doc);
        $articles = $xpath->query('//div[@class="arsipKeterangan"]');

        // all links in .blogArticle
        $links = [];
        foreach($articles as $container) {
            $arr = $container->getElementsByTagName("a");
            foreach($arr as $item => $value) {
                if($item == 0){
                    $href =  $value->getAttribute("href");
                    $text = trim(preg_replace("/[\r\n]+/", " ", $value->nodeValue));
                    $links[] = [
                        'href' => $href,
                        'text' => $text
                    ];
                }
                
            }
        }

        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode($links,true));

    }
