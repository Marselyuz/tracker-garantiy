<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ReceiptController extends Controller
{
    public function index()
    {
        $receipts = Auth::user()->receipts()->with('items')->latest()->get();
        return view('pages.receipts_index', compact('receipts'));
    }

    public function create()
    {
        if (!session()->has('recognized_image_path') && !session()->hasOldInput()) {
            $this->discardPendingImage();
        }

        return view('pages.receipt_create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
            'items' => 'nullable|array',
            'items.*.name' => 'required|string|max:255',
            'items.*.price' => 'required|numeric',
            'items.*.warranty_months' => 'required|integer',
        ]);

        $pendingImage = session('pending_receipt_image');
        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('receipts', 'public');
            $this->discardPendingImage();
        } elseif ($pendingImage && Storage::disk('public')->exists($pendingImage)) {
            $imagePath = $pendingImage;
        }

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'manual';

        $receipt = Receipt::create([
            'store_name' => $validated['store_name'],
            'purchase_date' => $validated['purchase_date'],
            'image' => $imagePath,
            'user_id' => $validated['user_id'],
            'status' => $validated['status'],
        ]);

        session()->forget('pending_receipt_image');

        if (!empty($validated['items'])) {
            foreach ($validated['items'] as $itemData) {
                $warrantyEndDate = Carbon::parse($validated['purchase_date'])->addMonths($itemData['warranty_months']);

                Item::create([
                    'receipt_id' => $receipt->id,
                    'name' => $itemData['name'],
                    'price' => $itemData['price'],
                    'warranty_months' => $itemData['warranty_months'],
                    'warranty_end_date' => $warrantyEndDate,
                ]);
            }
        }

        return redirect()->route('receipts.show', $receipt->id)
            ->with('success', 'Чек и товары успешно сохранены.');
    }

    public function show($id)
    {
        $receipt = Receipt::where('user_id', Auth::id())->with('items')->findOrFail($id);
        return view('pages.receipt_show', compact('receipt'));
    }

    public function download($id)
    {
        $receipt = Receipt::where('user_id', Auth::id())->findOrFail($id);

        if (!$receipt->image || !Storage::disk('public')->exists($receipt->image)) {
            abort(404);
        }

        $ext = pathinfo($receipt->image, PATHINFO_EXTENSION) ?: 'jpg';
        $name = 'chek-' . (Str::slug($receipt->store_name) ?: 'receipt')
            . '-' . $receipt->purchase_date->format('Y-m-d') . '.' . $ext;

        return Storage::disk('public')->download($receipt->image, $name);
    }

    public function edit($id)
    {
        $receipt = Receipt::where('user_id', Auth::id())->findOrFail($id);
        return view('pages.receipt_edit', compact('receipt'));
    }

    public function update(Request $request, $id)
    {
        $receipt = Receipt::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
        ]);

        if ($request->hasFile('image')) {
            if ($receipt->image) {
                Storage::disk('public')->delete($receipt->image);
            }
            $validated['image'] = $request->file('image')->store('receipts', 'public');
        }

        $receipt->update([
            'store_name' => $validated['store_name'],
            'purchase_date' => $validated['purchase_date'],
            'image' => $validated['image'] ?? $receipt->image,
        ]);

        return redirect()->route('receipts.show', $receipt->id)->with('success', 'Чек обновлён');
    }

    public function destroy($id)
    {
        $receipt = Receipt::where('user_id', Auth::id())->findOrFail($id);

        if ($receipt->image) {
            Storage::disk('public')->delete($receipt->image);
        }

        $receipt->delete();

        return redirect()->route('receipts.index')->with('success', 'Чек удалён');
    }

    public function recognize(Request $request)
    {
        set_time_limit(90);

        $request->validate([
            'receipt_image' => 'required|image|mimes:jpg,jpeg,png|max:10240',
        ]);

        $apiKey = config('services.mindee.key');
        $modelId = config('services.mindee.model_id');

        if (!$apiKey || !$modelId) {
            return back()->with('error', 'Mindee не настроен.');
        }

        $this->discardPendingImage();

        $savedImagePath = $request->file('receipt_image')->store('receipts', 'public');

        try {
            $imagePath = $request->file('receipt_image')->getRealPath();
            $preparedPath = $this->prepareImage($imagePath);

            $tmpName = 'tmp_receipt_' . uniqid() . '.jpg';
            Storage::disk('public')->put($tmpName, file_get_contents($preparedPath));
            $tmpFullPath = Storage::disk('public')->path($tmpName);
            register_shutdown_function(function () use ($tmpFullPath) {
                @unlink($tmpFullPath);
            });

            if ($preparedPath !== $imagePath) {
                @unlink($preparedPath);
            }

            $imageUrl = url('storage/' . $tmpName);

            [$httpStatus, $rawResponse, $curlError] = $this->sendCurlMultipart($apiKey, $imageUrl, $modelId);

            if ($curlError) {
                @unlink(storage_path('app/public/' . $savedImagePath));
                return back()->with('error', 'Сетевая ошибка: ' . $curlError);
            }

            $enqueueData = json_decode($rawResponse, true);

            if ($httpStatus >= 400 || !isset($enqueueData['job']['polling_url'])) {
                @unlink(storage_path('app/public/' . $savedImagePath));
                return back()->with('error', 'API отклонил запрос. Проверь логи.');
            }

            $pollingUrl = $enqueueData['job']['polling_url'];

            $result = null;
            sleep(2);
            for ($i = 0; $i < 8; $i++) {
                $poll = $this->mindeeRequest($apiKey)->withoutRedirecting()->get($pollingUrl);
                $status = $poll->json('job.status');

                if ($poll->status() === 302 || $status === 'Processed') {
                    $resultUrl = $poll->json('job.result_url') ?: $poll->header('Location');
                    $result = $this->mindeeRequest($apiKey)->get($resultUrl)->json();
                    break;
                }

                if ($status === 'Failed') {
                    @unlink(storage_path('app/public/' . $savedImagePath));
                    return back()->with('error', 'Mindee не смог обработать.');
                }
                sleep(2);
            }

            if (!$result) {
                @unlink(storage_path('app/public/' . $savedImagePath));
                return back()->with('error', 'Таймаут обработки.');
            }

            $fields = data_get($result, 'inference.result.fields') ?? data_get($result, 'inference.fields') ?? [];
            $storeName = $this->firstValue($fields, ['supplier_name', 'merchant_name', 'store_name']);
            
            $date = null;
            $rawDate = $this->firstValue($fields, ['date', 'purchase_date']);
            if ($rawDate) {
                try { $date = Carbon::parse($rawDate)->format('Y-m-d'); } catch (\Exception $e) { $date = null; }
            }

            $items = [];
            foreach (data_get($fields, 'line_items.items', []) as $line) {
                $lineFields = $line['fields'] ?? $line;
                $name = $this->firstValue($lineFields, ['description', 'name']);
                $price = $this->firstValue($lineFields, ['total_price', 'total_amount', 'unit_price']);

                if ($name && is_numeric($price)) {
                    $items[] = ['name' => $name, 'price' => $price];
                }
            }

            session()->put('pending_receipt_image', $savedImagePath);

            return back()->with([
                'success' => 'Успех!',
                'recognized_date' => $date,
                'recognized_store' => $storeName,
                'recognized_items' => $items,
                'recognized_image_path' => $savedImagePath,
            ]);

        } catch (\Exception $e) {
            @unlink(storage_path('app/public/' . $savedImagePath));
            return back()->with('error', 'Ошибка распознавания: ' . $e->getMessage());
        }
    }

    private function discardPendingImage(): void
    {
        $path = session('pending_receipt_image');

        if ($path) {
            Storage::disk('public')->delete($path);
            session()->forget('pending_receipt_image');
        }
    }

    private function prepareImage(string $sourcePath): string
    {
        if (!function_exists('imagecreatefromstring')) {
            return $sourcePath;
        }

        $data = @file_get_contents($sourcePath);
        if (!$data) return $sourcePath;

        $src = @imagecreatefromstring($data);
        if (!$src) return $sourcePath;

        $width = imagesx($src);
        $height = imagesy($src);
        $maxDim = 1600;

        if ($width > $maxDim || $height > $maxDim) {
            $ratio = $width / $height;
            if ($ratio > 1) {
                $newWidth = $maxDim;
                $newHeight = (int) ($maxDim / $ratio);
            } else {
                $newHeight = $maxDim;
                $newWidth = (int) ($maxDim * $ratio);
            }
            $dst = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($src);
            $src = $dst;
        }

        $tmp = tempnam(sys_get_temp_dir(), 'rcpt_') . '.jpg';
        imagejpeg($src, $tmp, 80);
        imagedestroy($src);

        return $tmp;
    }

    private function sendCurlMultipart(string $apiKey, string $imageUrl, string $modelId): array
    {
        $proxyUrl = config('services.mindee.proxy_url');
        $proxySecret = config('services.mindee.proxy_secret');

        $headers = ['Accept: application/json', 'Expect:'];
        if ($proxyUrl) {
            $url = $proxyUrl;
            $headers[] = 'X-Proxy-Secret: ' . $proxySecret;
        } else {
            $url = 'https://api-v2.mindee.net/v2/inferences/enqueue';
            $headers[] = 'Authorization: ' . $apiKey;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'model_id' => $modelId,
            'url' => $imageUrl,
        ]);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 25);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        $rawResponse = curl_exec($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        return [$httpStatus, $rawResponse, $curlError];
    }

    private function mindeeRequest(string $apiKey)
    {
        return Http::timeout(30)
            ->withoutVerifying()
            ->withOptions([
                'version' => 1.1,
                'curl' => [
                    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                ]
            ])
            ->withHeaders(['Authorization' => $apiKey]);
    }

    private function firstValue($fields, array $names)
    {
        foreach ($names as $name) {
            $value = data_get($fields, $name . '.value');
            if ($value !== null && $value !== '') {
                return $value;
            }
        }
        return null;
    }
}