<?php
/* =========================================================
   Gizem Genç Üçkuş — İletişim formu
   Mesajı form@ posta kutusuyla giriş yaparak info@ adresine iletir.
   Mesaj ve ziyaretçi bilgisi sunucuda saklanmaz.
   Şifre web kökünün dışındaki form-sifre.txt dosyasındadır. Dosya yoksa veya
   giriş başarısızsa mesaj kaybolmasın diye sunucunun mail() işlevi kullanılır.
   Sayaç dosyası yalnızca saatlik toplam gönderim sayısını tutar.
   ========================================================= */

const HEDEF = 'info@fizyoterapistgizemgencuckus.com';
const GONDEREN = 'form@fizyoterapistgizemgencuckus.com';
const ALAN_ADI = 'fizyoterapistgizemgencuckus.com';
const SMTP_SUNUCU = 'smtp.hostinger.com';
const SMTP_PORT = 465;
const BOS_SIFRE = 'sifreyi-buraya-yapistir';
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

function smtpSifresi()
{
    $dosya = dirname(__DIR__) . '/form-sifre.txt';
    $sifre = is_readable($dosya) ? trim((string) file_get_contents($dosya)) : '';
    return $sifre === BOS_SIFRE ? '' : $sifre;
}

function smtpYanit($baglanti)
{
    $satir = '';
    while (($parca = fgets($baglanti, 1024)) !== false) {
        $satir = $parca;
        if (strlen($parca) < 4 || $parca[3] !== '-') {
            break;
        }
    }
    return (int) substr($satir, 0, 3);
}

// Kayda yalnızca başarısız adımın numarası yazılır; şifre ve mesaj yazılmaz.
function smtpGonder($sifre, $ileti)
{
    $baglam = stream_context_create(array('ssl' => array('verify_peer' => true, 'verify_peer_name' => true)));
    $baglanti = @stream_socket_client('ssl://' . SMTP_SUNUCU . ':' . SMTP_PORT, $hataNo, $hataMetni, 10, STREAM_CLIENT_CONNECT, $baglam);
    if (!$baglanti) {
        error_log('form smtp: baglanti kurulamadi');
        return false;
    }
    stream_set_timeout($baglanti, 10);

    $adimlar = array(
        array('EHLO ' . ALAN_ADI, array(250)),
        array('AUTH LOGIN', array(334)),
        array(base64_encode(GONDEREN), array(334)),
        array(base64_encode($sifre), array(235)),
        array('MAIL FROM:<' . GONDEREN . '>', array(250)),
        array('RCPT TO:<' . HEDEF . '>', array(250, 251)),
        array('DATA', array(354)),
        array($ileti . "\r\n.", array(250)),
    );

    $tamam = smtpYanit($baglanti) === 220;
    foreach ($adimlar as $sira => $adim) {
        if (!$tamam) {
            break;
        }
        fwrite($baglanti, $adim[0] . "\r\n");
        $tamam = in_array(smtpYanit($baglanti), $adim[1], true);
        if (!$tamam) {
            error_log('form smtp: adim ' . $sira . ' basarisiz');
        }
    }
    if ($tamam) {
        fwrite($baglanti, "QUIT\r\n");
    }
    fclose($baglanti);
    return $tamam;
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
$govde = chunk_split(base64_encode(implode("\n", $satirlar)));

$sifre = smtpSifresi();
$gitti = $sifre !== '' && smtpGonder($sifre, implode("\r\n", array(
    'Date: ' . date('r'),
    'From: Web Sitesi <' . GONDEREN . '>',
    'To: <' . HEDEF . '>',
    'Reply-To: ' . $eposta,
    'Subject: ' . mb_encode_mimeheader($konu, 'UTF-8', 'B', "\r\n", 9),
    'Message-ID: <' . bin2hex(random_bytes(12)) . '@' . ALAN_ADI . '>',
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'Content-Transfer-Encoding: base64',
    '',
    rtrim($govde),
)));
if ($gitti) {
    yanit(200, true, 'Mesajınız bize ulaştı. Teşekkür ederiz.');
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
    $govde,
    implode("\r\n", $basliklar),
    '-f' . HEDEF
);

if (!$gitti) {
    yanit(500, false, 'Mesajınız gönderilemedi. Lütfen tekrar deneyin.');
}
yanit(200, true, 'Mesajınız bize ulaştı. Teşekkür ederiz.');
