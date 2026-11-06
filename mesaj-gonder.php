<?php
/* =========================================================
   Gizem Genç Üçkuş — İletişim formu
   Mesajı info@ adresine iletir. Mesaj ve ziyaretçi bilgisi sunucuda saklanmaz.
   Sayaç dosyası yalnızca saatlik toplam gönderim sayısını tutar.
   ========================================================= */

const HEDEF = 'info@fizyoterapistgizemgencuckus.com';
const SAATLIK_SINIR = 30;

ini_set('display_errors', '0');
header('X-Robots-Tag: noindex');

function yanit($kod, $tamam, $metin)
{
    http_response_code($kod);
    $kabul = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '';
    if (strpos($kabul, 'application/json') !== false) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array('ok' => $tamam, 'mesaj' => $metin), JSON_UNESCAPED_UNICODE);
    } else {   // Betik çalışmayan tarayıcıda sade sayfa gösterilir
        header('Content-Type: text/html; charset=utf-8');
        echo '<!doctype html><html lang="tr"><head><meta charset="utf-8"><title>İletişim</title></head><body><p>'
            . htmlspecialchars($metin, ENT_QUOTES, 'UTF-8')
            . '</p><p><a href="/iletisim.html">İletişim sayfasına dön</a></p></body></html>';
    }
    exit;
}

function alan($ad)
{
    $deger = isset($_POST[$ad]) && is_string($_POST[$ad]) ? trim($_POST[$ad]) : '';
    if (!mb_check_encoding($deger, 'UTF-8')) {
        yanit(422, false, 'Mesajınız okunamadı. Lütfen yeniden yazın.');
    }
    return $deger;
}

function tekSatir($deger)
{
    return trim(preg_replace('/[\x00-\x1F\x7F]+/', ' ', $deger));
}

// Kişisel veri tutmaz: yalnızca "saat adet" yazar. Dosya web kökünün dışındadır.
function sinirAsildi()
{
    $dosya = @fopen(dirname(__DIR__) . '/.form-sayac', 'c+');
    if (!$dosya) {
        return false;
    }
    flock($dosya, LOCK_EX);
    $kayit = explode(' ', trim((string) stream_get_contents($dosya)));
    $saat = date('YmdH');
    $adet = (count($kayit) === 2 && $kayit[0] === $saat) ? (int) $kayit[1] : 0;
    $asildi = $adet >= SAATLIK_SINIR;
    if (!$asildi) {
        rewind($dosya);
        ftruncate($dosya, 0);
        fwrite($dosya, $saat . ' ' . ($adet + 1));
        fflush($dosya);
    }
    flock($dosya, LOCK_UN);
    fclose($dosya);
    return $asildi;
}

if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    yanit(405, false, 'Bu adres yalnızca iletişim formundan kullanılır.');
}

if (!empty($_SERVER['HTTP_ORIGIN'])) {
    $kaynak = parse_url($_SERVER['HTTP_ORIGIN'], PHP_URL_HOST);
    $site = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    if ($kaynak !== $site) {
        yanit(403, false, 'Mesaj gönderilemedi.');
    }
}

// Tuzak alanı yalnızca otomatik gönderimlerde dolar: mesaj gönderilmez, başarılı görünür
if (alan('site') !== '') {
    yanit(200, true, 'Mesajınız bize ulaştı. Teşekkür ederiz.');
}

$ad = tekSatir(alan('ad'));
$eposta = tekSatir(alan('eposta'));
$telefon = tekSatir(alan('telefon'));
$mesaj = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', alan('mesaj'));

$adUzunluk = mb_strlen($ad, 'UTF-8');
$mesajUzunluk = mb_strlen($mesaj, 'UTF-8');
$rakam = strlen(preg_replace('/\D/', '', $telefon));
if ($adUzunluk < 2 || $adUzunluk > 80
    || strlen($eposta) > 120 || !filter_var($eposta, FILTER_VALIDATE_EMAIL)
    || strlen($telefon) > 20 || ($telefon !== '' && $rakam < 10)
    || $mesajUzunluk < 5 || $mesajUzunluk > 2000) {
    yanit(422, false, 'Lütfen form alanlarını kontrol edin.');
}

if (sinirAsildi()) {
    yanit(429, false, 'Şu anda çok fazla mesaj geldi. Lütfen daha sonra tekrar deneyin.');
}

$konu = 'Web sitesinden mesaj: ' . mb_substr($ad, 0, 30, 'UTF-8');
$satirlar = array($mesaj, '', '---', 'Ad Soyad: ' . $ad, 'E-posta: ' . $eposta);
if ($telefon !== '') {
    $satirlar[] = 'Telefon: ' . $telefon;
}
$basliklar = array(
    'From: Web Sitesi <' . HEDEF . '>',
    'Reply-To: ' . $eposta,
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'Content-Transfer-Encoding: base64',
);

$gitti = mail(
    HEDEF,
    '=?UTF-8?B?' . base64_encode($konu) . '?=',
    chunk_split(base64_encode(implode("\n", $satirlar))),
    implode("\r\n", $basliklar),
    '-f' . HEDEF
);

if (!$gitti) {
    yanit(500, false, 'Mesajınız gönderilemedi. Lütfen tekrar deneyin.');
}
yanit(200, true, 'Mesajınız bize ulaştı. Teşekkür ederiz.');
