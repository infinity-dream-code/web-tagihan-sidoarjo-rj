
<?php
require_once __DIR__ . '/../config/database.php';

class Tagihan
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getAllTahunAkademik()
    {
        $sql = "SELECT urut, thn_aka 
            FROM mst_thn_aka 
            ORDER BY urut DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertVA($custid, $nocust, $namacust, $arrayTagihan, $billam)
    {
        $now = new DateTime();
        $year = substr($now->format('Y'), -1);
        $month = $now->format('m');
        $day = $now->format('d');

        $datePrefix = $year . $month . $day;
        $sql = "SELECT MAX(ID) AS last_id, MAX(NOVA) AS last_nova FROM scctva WHERE NOVA LIKE '%$datePrefix%'";
        $stmt = $this->db->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && $row['last_nova']) {
            $lastIncrement = (int)substr($row['last_nova'], -4);
            $increment = str_pad($lastIncrement + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $increment = '0001';
        }

        $va_core = "8" . $datePrefix . $increment;
        $va_full = "751000" . $va_core;

        $sqlTagihan = "SELECT AA FROM scctbill WHERE AA IN ($arrayTagihan)";
        $stmtTagihan = $this->db->query($sqlTagihan);
        $rows = $stmtTagihan->fetchAll(PDO::FETCH_ASSOC);
        $tagihanAA = implode(',', array_column($rows, 'AA'));

        $insert = "
        INSERT INTO scctva (CUSTID, NOCUST, NMCUST, NOVA, ArrayTagihan, BILLAM, STATUS, CREATED_AT)
        VALUES (:custid, :nocust, :namacust, :nova, :arrayTagihan, :billam, 1, NOW())
    ";
        $stmtInsert = $this->db->prepare($insert);
        $stmtInsert->execute([
            ':custid' => $custid,
            ':nocust' => $nocust,
            ':namacust' => $namacust,
            ':nova' => $va_core,
            ':arrayTagihan' => $tagihanAA,
            ':billam' => $billam
        ]);

        return $va_full;
    }

    public function cekTagihanByVA($va_number, $tahun_akademik = null)
    {
        if (strpos($va_number, '751000') === 0) {
            $num2nd = substr($va_number, 6);
        } else {
            $num2nd = $va_number;
        }

        $sql = "
        SELECT 
            c.CUSTID AS id,
            c.NOCUST AS no_cust,
            c.NUM2ND AS num2nd,
            c.NMCUST AS nama,
            c.CODE02 AS jenjang,
            c.DESC02 AS kelas,
            c.CODE03 AS kode_jurusan,
            c.DESC03 AS jurusan,
            c.DESC04 AS tahun_akademik,
            c.DESC05 AS alamat,
            c.GENUS AS orangtua,
            c.LastUpdate AS updated_at
        FROM scctcust c
        WHERE c.NOCUST = :nocust
        LIMIT 1
    ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':nocust' => $num2nd]);
        $siswa = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$siswa) {
            return null;
        }

        $sqlSaldo = "SELECT SALDO FROM v_saldo_va WHERE NOCUST = :nocust LIMIT 1";
        $stmtSaldo = $this->db->prepare($sqlSaldo);
        $stmtSaldo->execute([':nocust' => $num2nd]);
        $saldoRow = $stmtSaldo->fetch(PDO::FETCH_ASSOC);
        $saldo = $saldoRow['SALDO'] ?? 0;

        $siswa['va_number'] = $va_number;
        $siswa['saldo'] = $saldo;

        if (!$tahun_akademik || strtolower($tahun_akademik) === 'all') {
            $tahun_akademik = null;
        }

        $baseFilter = "b.CUSTID = :custid";
        if ($tahun_akademik) {
            $baseFilter .= " AND b.BTA = :tahun_akademik";
        }

        $sqlBelum = "
        SELECT 
            b.AA, b.BILLCD, b.BILLNM AS nama_tagihan, b.BILLAM AS total_tagihan,
            b.BILLAC AS periode, b.BTA AS tahun_akademik_tagihan,
            d.BILLAM AS nominal_detail, d.tahun AS tahun_detail, u.NamaAkun AS akun_detail,
            b.PAIDST, b.PAIDDT
        FROM scctbill b
        LEFT JOIN scctbill_detail d ON b.CUSTID = d.CUSTID AND b.BILLCD = d.BILLCD
        LEFT JOIN u_akun u ON d.KodePost = u.KodeAkun
        WHERE $baseFilter AND b.PAIDST = '0' AND b.FSTSBolehBayar = '1'
        ORDER BY b.BILLAC, d.tahun
    ";
        $stmtBelum = $this->db->prepare($sqlBelum);
        $params = [':custid' => $siswa['id']];
        if ($tahun_akademik) $params[':tahun_akademik'] = $tahun_akademik;
        $stmtBelum->execute($params);
        $belumLunas = $stmtBelum->fetchAll(PDO::FETCH_ASSOC);

        $sqlLunas = "
        SELECT 
            b.AA, b.BILLCD, b.BILLNM AS nama_tagihan, b.BILLAM AS total_tagihan,
            b.BILLAC AS periode, b.BTA AS tahun_akademik_tagihan,
            d.BILLAM AS nominal_detail, d.tahun AS tahun_detail, u.NamaAkun AS akun_detail,
            b.PAIDST, b.PAIDDT
        FROM scctbill b
        LEFT JOIN scctbill_detail d ON b.CUSTID = d.CUSTID AND b.BILLCD = d.BILLCD
        LEFT JOIN u_akun u ON d.KodePost = u.KodeAkun
        WHERE $baseFilter AND b.PAIDST = '1'
        ORDER BY b.PAIDDT DESC
    ";
        $stmtLunas = $this->db->prepare($sqlLunas);
        $stmtLunas->execute($params);
        $lunas = $stmtLunas->fetchAll(PDO::FETCH_ASSOC);

        $groupData = function ($rows) {
            $grouped = [];
            foreach ($rows as $row) {
                $key = $row['BILLCD'];
                if (!isset($grouped[$key])) {
                    $grouped[$key] = [
                        'AA' => $row['AA'],
                        'BILLCD' => $row['BILLCD'],
                        'nama_tagihan' => $row['nama_tagihan'],
                        'total_tagihan' => $row['total_tagihan'],
                        'periode' => $row['periode'],
                        'tahun_akademik_tagihan' => $row['tahun_akademik_tagihan'],
                        'PAIDST' => $row['PAIDST'],
                        'PAIDDT' => $row['PAIDDT'],
                        'detail' => []
                    ];
                }
                $grouped[$key]['detail'][] = [
                    'nominal_detail' => $row['nominal_detail'],
                    'akun_detail' => $row['akun_detail']
                ];
            }
            return array_values($grouped);
        };

        $siswa['tahun_dipilih'] = $tahun_akademik ?: 'Semua Tahun Akademik';
        $siswa['tagihan'] = $groupData($belumLunas);
        $siswa['tagihan_lunas'] = $groupData($lunas);

        return $siswa;
    }

    public function cekTagihanByVAPw($va_number, $password, $tahun_akademik = null)
    {
        if (strpos($va_number, '751000') === 0) {
            $num2nd = substr($va_number, 6);
        } else {
            $num2nd = $va_number;
        }

        $stmtUser = $this->db->prepare("SELECT userlogin, kunci FROM sm_user WHERE userlogin = :userlogin LIMIT 1");
        $stmtUser->execute([':userlogin' => $num2nd]);
        $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
        if (!$user || $user['kunci'] !== sha1($password)) {
            return null;
        }

        $sql = "
        SELECT 
            c.CUSTID AS id,
            c.NOCUST AS no_cust,
            c.NUM2ND AS num2nd,
            c.NMCUST AS nama,
            c.CODE02 AS jenjang,
            c.DESC02 AS kelas,
            c.CODE03 AS kode_jurusan,
            c.DESC03 AS jurusan,
            c.DESC04 AS tahun_akademik,
            c.DESC05 AS alamat,
            c.GENUS AS orangtua,
            c.LastUpdate AS updated_at
        FROM scctcust c
        WHERE c.NOCUST = :nocust
        LIMIT 1
    ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':nocust' => $num2nd]);
        $siswa = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$siswa) {
            return null;
        }

        $sqlSaldo = "SELECT SALDO FROM v_saldo_va WHERE NOCUST = :nocust LIMIT 1";
        $stmtSaldo = $this->db->prepare($sqlSaldo);
        $stmtSaldo->execute([':nocust' => $num2nd]);
        $saldoRow = $stmtSaldo->fetch(PDO::FETCH_ASSOC);
        $saldo = $saldoRow['SALDO'] ?? 0;

        $siswa['va_number'] = $va_number;
        $siswa['saldo'] = $saldo;

        if (!$tahun_akademik || strtolower($tahun_akademik) === 'all') {
            $tahun_akademik = null;
        }

        $baseFilter = "b.CUSTID = :custid";
        if ($tahun_akademik) {
            $baseFilter .= " AND b.BTA = :tahun_akademik";
        }

        // === TAGIHAN BELUM LUNAS ===
        $sqlBelum = "
        SELECT 
            b.AA, b.BILLCD, b.BILLNM AS nama_tagihan, b.BILLAM AS total_tagihan,
            b.BILLAC AS periode, b.BTA AS tahun_akademik_tagihan,
            b.FTGLTagihan, b.FURUTAN,
            d.BILLAM AS nominal_detail, d.tahun AS tahun_detail, u.NamaAkun AS akun_detail,
            b.PAIDST, b.PAIDDT
        FROM scctbill b
        LEFT JOIN scctbill_detail d ON b.CUSTID = d.CUSTID AND b.BILLCD = d.BILLCD
        LEFT JOIN u_akun u ON d.KodePost = u.KodeAkun
        WHERE $baseFilter 
          AND b.PAIDST = '0' 
          AND b.FSTSBolehBayar = '1'
        ORDER BY b.FURUTAN ASC, d.tahun ASC
    ";
        $stmtBelum = $this->db->prepare($sqlBelum);
        $params = [':custid' => $siswa['id']];
        if ($tahun_akademik) $params[':tahun_akademik'] = $tahun_akademik;
        $stmtBelum->execute($params);
        $belumLunas = $stmtBelum->fetchAll(PDO::FETCH_ASSOC);

        // === TAGIHAN SUDAH LUNAS ===
        $sqlLunas = "
        SELECT 
            b.AA, b.BILLCD, b.BILLNM AS nama_tagihan, b.BILLAM AS total_tagihan,
            b.BILLAC AS periode, b.BTA AS tahun_akademik_tagihan,
            b.FTGLTagihan, b.FURUTAN,
            d.BILLAM AS nominal_detail, d.tahun AS tahun_detail, u.NamaAkun AS akun_detail,
            b.PAIDST, b.PAIDDT
        FROM scctbill b
        LEFT JOIN scctbill_detail d ON b.CUSTID = d.CUSTID AND b.BILLCD = d.BILLCD
        LEFT JOIN u_akun u ON d.KodePost = u.KodeAkun
        WHERE $baseFilter AND b.PAIDST = '1'
        ORDER BY b.PAIDDT DESC, b.FURUTAN ASC
    ";
        $stmtLunas = $this->db->prepare($sqlLunas);
        $stmtLunas->execute($params);
        $lunas = $stmtLunas->fetchAll(PDO::FETCH_ASSOC);

        $groupData = function ($rows) {
            $grouped = [];
            foreach ($rows as $row) {
                $key = $row['BILLCD'];
                if (!isset($grouped[$key])) {
                    $grouped[$key] = [
                        'AA' => $row['AA'],
                        'BILLCD' => $row['BILLCD'],
                        'nama_tagihan' => $row['nama_tagihan'],
                        'total_tagihan' => $row['total_tagihan'],
                        'periode' => $row['periode'],
                        'tahun_akademik_tagihan' => $row['tahun_akademik_tagihan'],
                        'FTGLTagihan' => $row['FTGLTagihan'],
                        'FURUTAN' => $row['FURUTAN'],
                        'PAIDST' => $row['PAIDST'],
                        'PAIDDT' => $row['PAIDDT'],
                        'detail' => []
                    ];
                }
                if (!empty($row['nominal_detail'])) {   // hindari null/empty
                    $grouped[$key]['detail'][] = [
                        'nominal_detail' => $row['nominal_detail'],
                        'akun_detail' => $row['akun_detail']
                    ];
                }
            }
            return array_values($grouped);
        };

        $siswa['tahun_dipilih'] = $tahun_akademik ?: 'Semua Tahun Akademik';
        $siswa['tagihan'] = $groupData($belumLunas);        // belum lunas (urut FURUTAN ASC)
        $siswa['tagihan_lunas'] = $groupData($lunas);       // lunas (urut terbaru dulu)

        return $siswa;
    }
}
