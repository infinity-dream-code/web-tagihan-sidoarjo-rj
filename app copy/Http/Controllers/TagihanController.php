<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TagihanController extends Controller
{
    private function wsUrl(?string $path = null): string
    {
        $url = rtrim(config('services.tagihan_ws.url'), '?&');

        if ($path) {
            $url .= (str_contains($url, '?') ? '&' : '?').'path='.$path;
        }

        return $url;
    }

    public static function normalizeVa(?string $va): string
    {
        $va = preg_replace('/\s+/', '', (string) $va);

        if (preg_match('/^(797766|751000)(\d+)$/', $va, $m)) {
            $va = $m[2];
        }

        $nocust = ltrim($va, '0');

        return $nocust !== '' ? $nocust : $va;
    }

    public static function formatNova(?string $nocust): string
    {
        $n = self::normalizeVa($nocust);

        if ($n === '') {
            return '-';
        }

        return $n;
    }

    public function cek(Request $request)
    {
        $request->validate([
            'no_cust' => 'required|string',
            'academic_year' => 'required|string'
        ]);

        $response = Http::timeout(30)
            ->withoutVerifying()
            ->post($this->wsUrl('cek-tagihan'), [
                'va' => self::normalizeVa($request->no_cust),
                'tahun_akademik' => $request->academic_year
            ]);

        $result = $response->json();
        $result = $this->withNova($result, $request->no_cust);

        return view('index', compact('result'))
            ->with([
                'va' => $request->no_cust,
                'academic_year' => $request->academic_year
            ]);
    }

    public function cek2(Request $request)
    {
        $request->validate([
            'no_cust' => 'required|string',
            'password' => 'required|string',
            'academic_year' => 'required|string'
        ]);

        $payload = [
            'va' => self::normalizeVa($request->no_cust),
            'password' => $request->password,
            'tahun_akademik' => $request->academic_year
        ];

        $response = Http::timeout(30)
            ->withoutVerifying()
            ->acceptJson()
            ->asJson()
            ->post($this->wsUrl('cek-tagihan-pw'), $payload);

        if ($response->status() === 404) {
            $response = Http::timeout(30)
                ->withoutVerifying()
                ->acceptJson()
                ->asJson()
                ->post($this->wsUrl('cek-tagihan'), $payload);
        }

        $result = $this->withNova($response->json(), $request->no_cust);

        if (empty($result['status'])) {
            return back()->with([
                'error' => $result['message'] ?? 'VA atau password salah, atau data tidak ditemukan',
                'va' => $request->no_cust,
                'academic_year' => $request->academic_year
            ])->withInput($request->except('password'));
        }

        return view('index3', compact('result'))
            ->with([
                'va' => $request->no_cust,
                'academic_year' => $request->academic_year
            ]);
    }

    private function withNova($result, ?string $fallback = null): array
    {
        if (!is_array($result)) {
            return ['status' => false, 'message' => 'Terjadi kesalahan'];
        }

        if (!empty($result['data'])) {
            $nocust = $result['data']['no_cust'] ?? $result['data']['va_number'] ?? $fallback;
            $result['data']['va_number'] = self::formatNova($nocust);
        }

        return $result;
    }

    private function extractVa($result, $fallback = null)
    {
        if (!is_array($result)) {
            return $fallback;
        }

        $data = $result['data'] ?? null;
        if (is_array($data)) {
            foreach (['va_number', 'NOVA', 'nova', 'NOCUST', 'nocust'] as $key) {
                if (array_key_exists($key, $data)) {
                    return $data[$key];
                }
            }
        }

        if (is_string($data) || is_numeric($data)) {
            return $data;
        }

        foreach (['va_number', 'nova', 'NOVA'] as $key) {
            if (array_key_exists($key, $result)) {
                return $result[$key];
            }
        }

        return $fallback;
    }

    public function tagihanView()
    {
        return view('tagihan');
    }

    public function buatVA(Request $request)
    {
        $request->validate([
            'custid' => 'required',
            'nocust' => 'required|string',
            'namacust' => 'required|string',
        ]);

        $pairs = [];
        $items = $request->input('items');
        if (is_array($items) && count($items)) {
            foreach ($items as $item) {
                $aa = (int) ($item['AA'] ?? $item['aa'] ?? 0);
                $amount = (int) ($item['amount'] ?? $item['billam'] ?? 0);
                if ($aa > 0 && $amount > 0) {
                    $pairs[] = ['aa' => $aa, 'amount' => $amount];
                }
            }
        } else {
            $arrayTagihan = $request->input('array_tagihan', $request->input('arrayTagihan', ''));
            if (is_array($arrayTagihan)) {
                $arrayTagihan = implode(',', $arrayTagihan);
            }
            $ids = collect(explode(',', (string) $arrayTagihan))
                ->map(fn ($id) => (int) trim($id))
                ->filter(fn ($id) => $id > 0)
                ->values();

            $billamRaw = $request->input('billam', $request->input('total', ''));
            if (is_array($billamRaw)) {
                $amounts = array_map('intval', $billamRaw);
            } else {
                $amounts = collect(explode(',', (string) $billamRaw))
                    ->map(fn ($n) => (int) trim($n))
                    ->values()
                    ->all();
            }

            foreach ($ids as $i => $aa) {
                $amount = (int) ($amounts[$i] ?? 0);
                if ($aa > 0 && $amount > 0) {
                    $pairs[] = ['aa' => $aa, 'amount' => $amount];
                }
            }
        }

        if (empty($pairs)) {
            return response()->json([
                'status' => false,
                'message' => 'Tagihan yang dipilih tidak valid',
            ], 422);
        }

        $idsCsv = collect($pairs)->pluck('aa')->implode(',');
        $billamCsv = collect($pairs)->pluck('amount')->implode(',');
        $total = (int) collect($pairs)->sum('amount');
        $nocust = self::normalizeVa($request->nocust);

        $payload = [
            'custid' => $request->custid,
            'nocust' => $nocust,
            'namacust' => $request->namacust,
            'array_tagihan' => $idsCsv,
            'arrayTagihan' => $idsCsv,
            'billam' => $billamCsv,
            'total' => $total,
            'billtot' => $total,
            'bank' => $request->input('bank', 'Muamalat'),
            'items' => $pairs,
        ];

        try {
            $wsUrl = $this->wsUrl('generate-va');
            Log::info('WS generate-va incoming', [
                'url' => $wsUrl,
                'request' => $request->all(),
                'payload' => $payload,
                'payload_types' => collect($payload)->map(fn ($v) => gettype($v) . ':' . json_encode($v))->all(),
                'empty_fields' => collect($payload)->filter(fn ($v) => $v === null || $v === '' || $v === false)->keys()->values()->all(),
            ]);

            $response = Http::timeout(30)
                ->withoutVerifying()
                ->acceptJson()
                ->asJson()
                ->post($wsUrl, $payload);

            Log::info('WS generate-va json', [
                'http' => $response->status(),
                'body' => $response->body(),
                'json' => $response->json(),
            ]);

            if ($response->failed() || empty($response->json()['status'])) {
                $formResponse = Http::timeout(30)
                    ->withoutVerifying()
                    ->asForm()
                    ->post($wsUrl, $payload);
                Log::info('WS generate-va form', [
                    'http' => $formResponse->status(),
                    'body' => $formResponse->body(),
                    'json' => $formResponse->json(),
                ]);
                if ($formResponse->successful()) {
                    $response = $formResponse;
                }
            }

            Log::info('WS generate-va response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            $result = $response->json();
            if (!is_array($result)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Gagal membuat nomor VA',
                ], 500);
            }

            $va = $this->extractVa($result, null);
            $vaOk = $va !== false && $va !== null && $va !== '' && $va !== 'false';

            if (!empty($result['status']) && $vaOk) {
                $result['data'] = array_merge(
                    is_array($result['data'] ?? null) ? $result['data'] : [],
                    ['va_number' => $nocust]
                );

                return response()->json($result);
            }

            $wsMessage = (string) ($result['message'] ?? '');
            $looksSuccess = stripos($wsMessage, 'berhasil') !== false;
            $message = ($wsMessage !== '' && !$looksSuccess)
                ? $wsMessage
                : 'Gagal menyimpan ke scctva. insertVA return false. Kalau NIS '.$nocust.' sudah ada di kolom NOVA, lepas UNIQUE di NOVA supaya tiap bayar bisa baris baru dengan nomor yang sama.';

            Log::warning('WS generate-va insert failed', [
                'ws_status' => $result['status'] ?? null,
                'ws_message' => $wsMessage,
                'va' => $va,
                'nocust' => $nocust,
            ]);

            return response()->json([
                'status' => false,
                'message' => $message,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error generate-va', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat membuat nomor VA',
            ], 500);
        }
    }

    public function listTahunAkademik()
    {
        try {
            $response = Http::timeout(30)
                ->withoutVerifying()
                ->get(config('services.tagihan_ws.url'), [
                    'path' => 'list-tahun-aka'
                ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'status' => false,
                'message' => 'API tidak memberikan response yang valid'
            ], 500);
        } catch (\Exception $e) {
            \Log::error('Error fetching tahun akademik', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Gagal mengambil data tahun akademik',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
