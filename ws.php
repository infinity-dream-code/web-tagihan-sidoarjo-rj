
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
        $ids = array_values(array_filter(array_map('intval', explode(',', str_replace(' ', '', (string) $arrayTagihan)))));
        $amounts = is_array($billam)
            ? array_map('intval', $billam)
            : array_map('intval', explode(',', str_replace(' ', '', (string) $billam)));

        if (empty($ids) || count($ids) !== count($amounts)) {
            return false;
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sqlBills = "
            SELECT b.AA, b.BILLAM, b.BILLCD, COALESCE(m.isINSTALLMENT, 0) AS is_installment
            FROM scctbill b
            LEFT JOIN mst_tagihan m ON m.kode = b.BILLCD
            WHERE b.CUSTID = ? AND b.AA IN ($placeholders)
        ";
        $stmtBills = $this->db->prepare($sqlBills);
        $stmtBills->execute(array_merge([(int) $custid], $ids));
        $bills = [];
        foreach ($stmtBills->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $bills[(int) $row['AA']] = $row;
        }

        $paidMap = $this->getPaidMap($custid, $nocust);
        $validIds = [];
        $validAmounts = [];

        foreach ($ids as $i => $aa) {
            if (!isset($bills[$aa])) {
                return false;
            }
            $bill = $bills[$aa];
            $sisa = max(0, (int) $bill['BILLAM'] - (int) ($paidMap[$aa] ?? 0));
            $amt = (int) $amounts[$i];
            $bolehCicil = (int) $bill['is_installment'] === 1;

            if ($amt <= 0 || $amt > $sisa) {
                return false;
            }
            if (!$bolehCicil && $amt !== $sisa) {
                return false;
            }

            $validIds[] = $aa;
            $validAmounts[] = $amt;
        }

        $billamCsv = implode(',', $validAmounts);
        $billtot = array_sum($validAmounts);
        $tagihanAA = implode(',', $validIds);

        $insert = "
            INSERT INTO scctva (CUSTID, NOCUST, NMCUST, NOVA, ArrayTagihan, BILLAM, BILLTOT, STATUS, CREATED_AT)
            VALUES (:custid, :nocust, :namacust, :nova, :arrayTagihan, :billam, :billtot, 1, NOW())
        ";
        $stmtInsert = $this->db->prepare($insert);
        $stmtInsert->execute([
            ':custid' => $custid,
            ':nocust' => $nocust,
            ':namacust' => $namacust,
            ':nova' => $nocust,
            ':arrayTagihan' => $tagihanAA,
            ':billam' => $billamCsv,
            ':billtot' => $billtot,
        ]);

        return $nocust;
    }

    public function cekTagihanByVA($va_number, $tahun_akademik = null)
    {
        $siswa = $this->getSiswaByVa($va_number);
        if (!$siswa) {
            return null;
        }

        return $this->attachTagihan($siswa, $va_number, $tahun_akademik);
    }

    public function cekTagihanByVAPw($va_number, $password, $tahun_akademik = null)
    {
        $num2nd = $this->stripVa($va_number);

        $stmtUser = $this->db->prepare("SELECT userlogin, kunci FROM sm_user WHERE userlogin = :userlogin LIMIT 1");
        $stmtUser->execute([':userlogin' => $num2nd]);
        $user = $stmtUser->fetch(PDO::FETCH_ASSOC);
        if (!$user || $user['kunci'] !== sha1($password)) {
            return null;
        }

        $siswa = $this->getSiswaByVa($va_number);
        if (!$siswa) {
            return null;
        }

        return $this->attachTagihan($siswa, $va_number, $tahun_akademik);
    }

    private function stripVa($va_number)
    {
        foreach (['751000', '797766'] as $prefix) {
            if (strpos((string) $va_number, $prefix) === 0) {
                return substr($va_number, strlen($prefix));
            }
        }

        return $va_number;
    }

    private function getSiswaByVa($va_number)
    {
        $num2nd = $this->stripVa($va_number);
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
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    private function attachTagihan(array $siswa, $va_number, $tahun_akademik = null)
    {
        $num2nd = $siswa['no_cust'];

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

        $selectCols = "
            b.AA, b.BILLCD, b.BILLNM AS nama_tagihan, b.BILLAM AS total_tagihan,
            b.BILLAC AS periode, b.BTA AS tahun_akademik_tagihan,
            b.FTGLTagihan, b.FURUTAN,
            COALESCE(m.isINSTALLMENT, 0) AS is_installment,
            d.BILLAM AS nominal_detail, d.tahun AS tahun_detail, u.NamaAkun AS akun_detail,
            b.PAIDST, b.PAIDDT
        ";
        $joins = "
            FROM scctbill b
            LEFT JOIN mst_tagihan m ON m.kode = b.BILLCD
            LEFT JOIN scctbill_detail d ON b.CUSTID = d.CUSTID AND b.BILLCD = d.BILLCD
            LEFT JOIN u_akun u ON d.KodePost = u.KodeAkun
        ";

        $sqlBelum = "
        SELECT $selectCols
        $joins
        WHERE $baseFilter AND b.PAIDST = '0' AND b.FSTSBolehBayar = '1'
        ORDER BY b.FURUTAN ASC, d.tahun ASC
    ";
        $stmtBelum = $this->db->prepare($sqlBelum);
        $params = [':custid' => $siswa['id']];
        if ($tahun_akademik) {
            $params[':tahun_akademik'] = $tahun_akademik;
        }
        $stmtBelum->execute($params);
        $belumLunas = $stmtBelum->fetchAll(PDO::FETCH_ASSOC);

        $sqlLunas = "
        SELECT $selectCols
        $joins
        WHERE $baseFilter AND b.PAIDST = '1'
        ORDER BY b.PAIDDT DESC, b.FURUTAN ASC
    ";
        $stmtLunas = $this->db->prepare($sqlLunas);
        $stmtLunas->execute($params);
        $lunas = $stmtLunas->fetchAll(PDO::FETCH_ASSOC);

        $paidMap = $this->getPaidMap($siswa['id'], $num2nd);

        $siswa['tahun_dipilih'] = $tahun_akademik ?: 'Semua Tahun Akademik';
        $siswa['tagihan'] = $this->groupData($belumLunas, $paidMap);
        $siswa['tagihan_lunas'] = $this->groupData($lunas, $paidMap);

        return $siswa;
    }

    private function getPaidMap($custid, $nocust)
    {
        $sql = "SELECT ArrayTagihan, BILLAM FROM scctva WHERE CUSTID = :custid OR NOCUST = :nocust";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':custid' => $custid,
            ':nocust' => $nocust,
        ]);

        $paid = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $aas = array_map('trim', explode(',', (string) $row['ArrayTagihan']));
            $ams = array_map('trim', explode(',', (string) $row['BILLAM']));
            foreach ($aas as $i => $aa) {
                if ($aa === '') {
                    continue;
                }
                $amt = isset($ams[$i]) ? (int) $ams[$i] : 0;
                $paid[$aa] = (int) ($paid[$aa] ?? 0) + $amt;
            }
        }

        return $paid;
    }

    private function groupData(array $rows, array $paidMap)
    {
        $grouped = [];
        foreach ($rows as $row) {
            $key = $row['AA'];
            if (!isset($grouped[$key])) {
                $total = (int) $row['total_tagihan'];
                $sudah = (int) ($paidMap[$row['AA']] ?? $paidMap[(string) $row['AA']] ?? 0);
                $grouped[$key] = [
                    'AA' => $row['AA'],
                    'BILLCD' => $row['BILLCD'],
                    'nama_tagihan' => $row['nama_tagihan'],
                    'total_tagihan' => $row['total_tagihan'],
                    'periode' => $row['periode'],
                    'tahun_akademik_tagihan' => $row['tahun_akademik_tagihan'],
                    'FTGLTagihan' => $row['FTGLTagihan'] ?? null,
                    'FURUTAN' => $row['FURUTAN'] ?? null,
                    'is_installment' => (int) ($row['is_installment'] ?? 0) === 1 ? 1 : 0,
                    'sudah_dibayar' => $sudah,
                    'sisa_tagihan' => max(0, $total - $sudah),
                    'PAIDST' => $row['PAIDST'],
                    'PAIDDT' => $row['PAIDDT'],
                    'detail' => []
                ];
            }
            if (!empty($row['nominal_detail'])) {
                $grouped[$key]['detail'][] = [
                    'nominal_detail' => $row['nominal_detail'],
                    'akun_detail' => $row['akun_detail']
                ];
            }
        }

        return array_values($grouped);
    }
}
