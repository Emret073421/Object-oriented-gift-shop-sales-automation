<?php
class RaporManager extends TemelManager {

    public function getir($parametre = null) { return []; }
    public function sil($id = null) { return false; }

    // Aylık Rapor Getirme
    public function getirAylik(Rapor $rapor)
    {
        $ay = $rapor->getAy();
        $yil = $rapor->getYil();

        if ($ay < 1 || $ay > 12 || $yil < 2000) {
            return ['basarili' => false, 'mesaj' => 'Geçersiz tarih parametreleri.'];
        }

        $ayStr = str_pad($ay, 2, '0', STR_PAD_LEFT);
        $baslangic = "$yil-$ayStr-01 00:00:00";
        $bitis = date("Y-m-t 23:59:59", strtotime($baslangic));

        // 1. Aylık İstatistikleri Hesapla (SATIS, IADE, ALIS)
        $sqlOzet = "SELECT 
                        SUM(CASE WHEN islem_tipi = 'SATIS' THEN toplam_tutar ELSE 0 END) AS satis_toplam,
                        SUM(CASE WHEN islem_tipi = 'IADE' THEN ABS(toplam_tutar) ELSE 0 END) AS iade_toplam,
                        SUM(CASE WHEN islem_tipi = 'ALIS' THEN ABS(toplam_tutar) ELSE 0 END) AS alis_toplam,
                        SUM(CASE WHEN islem_tipi = 'SATIS' THEN miktar ELSE 0 END) AS satilan_adet,
                        COUNT(DISTINCT CASE WHEN islem_tipi = 'SATIS' THEN islem_kodu ELSE NULL END) AS siparis_sayisi
                    FROM islemler 
                    WHERE islem_tarihi BETWEEN '$baslangic' AND '$bitis' AND islem_tipi IN ('SATIS', 'IADE', 'ALIS')";

        $qOzet = $this->db->query($sqlOzet);
        $ozet = $qOzet ? $qOzet->fetch_assoc() : ['satis_toplam' => 0, 'iade_toplam' => 0, 'alis_toplam' => 0, 'satilan_adet' => 0, 'siparis_sayisi' => 0];

        $satisToplam = (float)$ozet['satis_toplam'];
        $iadeToplam = (float)$ozet['iade_toplam'];
        $alisToplam = (float)$ozet['alis_toplam'];
        $netKazanc = $satisToplam - $iadeToplam - $alisToplam;

        $satilanAdet = (int)$ozet['satilan_adet'];
        $siparisAdet = (int)$ozet['siparis_sayisi'];

        // 2. Tablo Satırlarını Oluştur (ALIS da dahil)
        $sqlTablo = "SELECT i.islem_kodu, i.islem_tarihi, i.miktar, i.birim_fiyat, i.toplam_tutar, i.islem_tipi, u.ad AS urun_adi 
                     FROM islemler i 
                     LEFT JOIN urunler u ON i.urun_id = u.id 
                     WHERE i.islem_tarihi BETWEEN '$baslangic' AND '$bitis' AND i.islem_tipi IN ('SATIS', 'IADE', 'ALIS')
                     ORDER BY i.islem_tarihi DESC";

        $qTablo = $this->db->query($sqlTablo);
        $tabloHtml = '';

        if ($qTablo && $qTablo->num_rows > 0) {
            while ($row = $qTablo->fetch_assoc()) {
                if ($row['islem_tipi'] === 'SATIS') {
                    $tipBadge = '<span class="badge bg-success fs-7">Satış</span>';
                    $tutarRenk = 'text-success';
                    $isaret = '';
                } elseif ($row['islem_tipi'] === 'IADE') {
                    $tipBadge = '<span class="badge bg-danger fs-7">İade</span>';
                    $tutarRenk = 'text-danger';
                    $isaret = '-';
                } else { // ALIS
                    $tipBadge = '<span class="badge bg-warning text-dark fs-7">Mal Alış</span>';
                    $tutarRenk = 'text-warning';
                    $isaret = '-';
                }

                $tabloHtml .= '<tr>
                                <td>
                                    <div class="fw-bold text-dark">' . date('d.m.Y H:i', strtotime($row['islem_tarihi'])) . '</div>
                                    <div class="text-muted fs-7">' . htmlspecialchars($row['islem_kodu']) . '</div>
                                </td>
                                <td>
                                    <div class="fw-bold">' . htmlspecialchars($row['urun_adi'] ?? 'Silinmiş/Bilinmeyen Ürün') . '</div>
                                    <div>' . $tipBadge . '</div>
                                </td>
                                <td class="text-center fw-bold">' . (int)$row['miktar'] . '</td>
                                <td class="text-end">₺' . number_format($row['birim_fiyat'], 2) . '</td>
                                <td class="text-end fw-bold ' . $tutarRenk . '">' . $isaret . '₺' . number_format($row['toplam_tutar'], 2) . '</td>
                               </tr>';
            }
        } else {
            $tabloHtml = '<tr><td colspan="5" class="text-center py-5 text-muted"><i class="fa-solid fa-receipt fa-2x mb-2 d-block"></i>Bu aya ait finansal hareket bulunamadı.</td></tr>';
        }

        // Model nesnesine sonuçları atıyoruz
        $rapor->setSatisToplam(number_format($satisToplam, 2, ',', '.') . ' ₺');
        $rapor->setIadeToplam(number_format($iadeToplam, 2, ',', '.') . ' ₺');
        $rapor->setAlisToplam(number_format($alisToplam, 2, ',', '.') . ' ₺');
        $rapor->setNetKazanc(number_format($netKazanc, 2, ',', '.') . ' ₺');
        $rapor->setSatilanAdet($satilanAdet . ' Adet');
        $rapor->setSiparisAdet($siparisAdet . ' Adet');
        $rapor->setTabloHtml($tabloHtml);

        return [
            'basarili' => true,
            'satis_toplam' => $rapor->getSatisToplam(),
            'iade_toplam' => $rapor->getIadeToplam(),
            'alis_toplam' => $rapor->getAlisToplam(),
            'net_kazanc' => $rapor->getNetKazanc(),
            'satilan_adet' => $rapor->getSatilanAdet(),
            'siparis_adet' => $rapor->getSiparisAdet(),
            'tablo_html' => $rapor->getTabloHtml()
        ];
    }

    // Yıllık Rapor Getirme
    public function getirYillik(Rapor $rapor)
    {
        $yil = $rapor->getYil();

        if ($yil < 2000) {
            return ['basarili' => false, 'mesaj' => 'Geçersiz yıl parametresi.'];
        }

        // 1. Yıllık Toplam Hasılat ve Giderler
        $sqlYillik = "SELECT 
                        SUM(CASE WHEN islem_tipi = 'SATIS' THEN toplam_tutar ELSE 0 END) AS satis_toplam,
                        SUM(CASE WHEN islem_tipi = 'IADE' THEN ABS(toplam_tutar) ELSE 0 END) AS iade_toplam,
                        SUM(CASE WHEN islem_tipi = 'ALIS' THEN ABS(toplam_tutar) ELSE 0 END) AS alis_toplam
                      FROM islemler 
                      WHERE YEAR(islem_tarihi) = $yil AND islem_tipi IN ('SATIS', 'IADE', 'ALIS')";

        $qYillik = $this->db->query($sqlYillik);
        $yillik = $qYillik ? $qYillik->fetch_assoc() : ['satis_toplam' => 0, 'iade_toplam' => 0, 'alis_toplam' => 0];
        
        $yillikSatis = (float)$yillik['satis_toplam'];
        $yillikIade = (float)$yillik['iade_toplam'];
        $yillikAlis = (float)$yillik['alis_toplam'];
        $yillikNet = $yillikSatis - $yillikIade - $yillikAlis;

        // 2. Yılın En Çok Satılan Ürünü
        $sqlEnCok = "SELECT u.ad, SUM(i.miktar) AS toplam_satilan 
                     FROM islemler i 
                     INNER JOIN urunler u ON i.urun_id = u.id 
                     WHERE YEAR(i.islem_tarihi) = $yil AND i.islem_tipi = 'SATIS' 
                     GROUP BY i.urun_id, u.ad 
                     ORDER BY toplam_satilan DESC LIMIT 1";

        $qEnCok = $this->db->query($sqlEnCok);
        if ($qEnCok && $qEnCok->num_rows > 0) {
            $enCok = $qEnCok->fetch_assoc();
            $enCokSatanMetin = htmlspecialchars($enCok['ad']) . ' (' . (int)$enCok['toplam_satilan'] . ' Adet)';
        } else {
            $enCokSatanMetin = 'Bu yıla ait satış kaydı yok.';
        }

        // 3. 12 Ayın Dağılımını Hesapla
        $aylar = ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'];
        $tabloHtml = '';

        for ($i = 1; $i <= 12; $i++) {
            $ayStr = str_pad($i, 2, '0', STR_PAD_LEFT);
            $baslangic = "$yil-$ayStr-01 00:00:00";
            $bitis = date("Y-m-t 23:59:59", strtotime($baslangic));

            $sqlAy = "SELECT 
                        COUNT(DISTINCT CASE WHEN islem_tipi = 'SATIS' THEN islem_kodu ELSE NULL END) AS siparis,
                        SUM(CASE WHEN islem_tipi = 'SATIS' THEN miktar ELSE 0 END) AS urun_adet,
                        SUM(CASE WHEN islem_tipi = 'SATIS' THEN toplam_tutar ELSE 0 END) AS satis_tutar,
                        SUM(CASE WHEN islem_tipi = 'IADE' THEN ABS(toplam_tutar) ELSE 0 END) AS iade_tutar,
                        SUM(CASE WHEN islem_tipi = 'ALIS' THEN ABS(toplam_tutar) ELSE 0 END) AS alis_tutar
                      FROM islemler 
                      WHERE islem_tarihi BETWEEN '$baslangic' AND '$bitis' AND islem_tipi IN ('SATIS', 'IADE', 'ALIS')";

            $qAy = $this->db->query($sqlAy);
            $ayVeri = $qAy ? $qAy->fetch_assoc() : ['siparis' => 0, 'urun_adet' => 0, 'satis_tutar' => 0, 'iade_tutar' => 0, 'alis_tutar' => 0];

            $siparis = (int)$ayVeri['siparis'];
            $urunAdet = (int)$ayVeri['urun_adet'];
            $satisT = (float)$ayVeri['satis_tutar'];
            $iadeT = (float)$ayVeri['iade_tutar'];
            $alisT = (float)$ayVeri['alis_tutar'];
            $netAy = $satisT - $iadeT - $alisT;

            if ($siparis > 0 || $urunAdet > 0 || $satisT > 0 || $alisT > 0) {
                $satisGosterim = '<span class="text-success fw-bold">₺' . number_format($satisT, 2) . '</span>';
                $iadeGosterim = '<span class="text-danger">₺' . number_format($iadeT, 2) . '</span>';
                $alisGosterim = '<span class="text-warning">₺' . number_format($alisT, 2) . '</span>';
                $netGosterim = '<span class="fw-bold ' . ($netAy >= 0 ? 'text-success' : 'text-danger') . '">₺' . number_format($netAy, 2) . '</span>';
                $satirSinif = 'bg-light';
            } else {
                $satisGosterim = '<span class="text-muted">-</span>';
                $iadeGosterim = '<span class="text-muted">-</span>';
                $alisGosterim = '<span class="text-muted">-</span>';
                $netGosterim = '<span class="text-muted">-</span>';
                $satirSinif = '';
            }

            $tabloHtml .= '<tr class="' . $satirSinif . ' align-middle">
                            <td class="ps-4 fw-bold">' . $aylar[$i - 1] . '</td>
                            <td class="text-center">' . ($siparis > 0 ? $siparis : '-') . '</td>
                            <td class="text-center">' . ($urunAdet > 0 ? $urunAdet : '-') . '</td>
                            <td class="text-end">' . $satisGosterim . '</td>
                            <td class="text-end">' . $iadeGosterim . '</td>
                            <td class="text-end">' . $alisGosterim . '</td>
                            <td class="pe-4 text-end">' . $netGosterim . '</td>
                           </tr>';
        }

        $rapor->setYillikSatis(number_format($yillikSatis, 2, ',', '.') . ' ₺');
        $rapor->setYillikIade(number_format($yillikIade, 2, ',', '.') . ' ₺');
        $rapor->setYillikAlis(number_format($yillikAlis, 2, ',', '.') . ' ₺');
        $rapor->setYillikNet(number_format($yillikNet, 2, ',', '.') . ' ₺');
        $rapor->setEnCokSatan($enCokSatanMetin);
        $rapor->setTabloHtml($tabloHtml);

        return [
            'basarili' => true,
            'yillik_satis' => $rapor->getYillikSatis(),
            'yillik_iade' => $rapor->getYillikIade(),
            'yillik_alis' => $rapor->getYillikAlis(),
            'yillik_net' => $rapor->getYillikNet(),
            'en_cok_satan' => $rapor->getEnCokSatan(),
            'tablo_html' => $rapor->getTabloHtml()
        ];
    }
}
?>
