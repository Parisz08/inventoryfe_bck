<?php

namespace App\Http\Controllers;

use App\Spb;
use App\SpbItem;
use App\SpbCondition;
use App\StockBarang;
use App\Vendor;
use App\Http\Library\Responses;
use App\Http\Traits\LoggedUser;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SpbController extends Controller
{
    use LoggedUser;

    public function index(Request $request)
    {
        $query = Spb::with('items', 'conditions.vendor')->orderBy('created_at', 'desc');

        $status = $request->input('status');
        if (!empty($status)) {
            $query->where('status', $status);
        }

        $search = $request->input('search');
        if (!empty($search)) {
            $query->where('no_spb', 'like', '%' . $search . '%');
        }

        $data = $query->paginate(10);

        return Responses::sendResponse($data, 'SPB Retrieved Successfully');
    }

    public function show($id)
    {
        $data = Spb::with('items', 'conditions.vendor')->find($id);

        if (!$data) {
            return Responses::sendError([], 'SPB Not Found');
        }

        foreach ($data->items as $item) {
            $stock = StockBarang::where('material_code', $item->material_code)->first();
            $item->actual_stock = $stock ? $stock->stock_barang : null;
            $item->min_stock    = $stock ? $stock->min_stock : null;
        }

        return Responses::sendResponse($data, 'SPB Detail Retrieved Successfully');
    }

    public function store(Request $request)
    {
        $items = $request->input('items', []);

        $validator = app('validator')->make($request->all(), [
            'items'            => 'required|array|min:1',
            'items.*.material_name' => 'required',
            'items.*.qty'      => 'required|integer|min:1',
        ]);
        if ($validator->fails()) {
            return Responses::sendError($validator->errors(), 'Validasi Gagal');
        }

        $userData = $this->get();
        $user     = $userData['user'];

        $noSpb = 'SPPB-' . date('Ymd') . '-' . str_pad(Spb::whereDate('created_at', date('Y-m-d'))->count() + 1, 4, '0', STR_PAD_LEFT);

        $spb = Spb::create([
            'no_spb'       => $noSpb,
            'divisi'       => $request->input('divisi'),
            'keperluan'    => $request->input('keperluan'),
            'request_date' => date('Y-m-d'),
            'status'       => 'Menunggu Approval',
            'created_by'   => $user->full_name,
            'updated_by'   => $user->full_name,
        ]);

        foreach ($items as $item) {
            SpbItem::create([
                'spb_id'        => $spb->id,
                'material_code' => $item['material_code'] ?? null,
                'kategori'      => $item['kategori'] ?? null,
                'material_name' => $item['material_name'],
                'merek'         => $item['merek'] ?? null,
                'specification' => $item['specification'] ?? null,
                'qty'           => $item['qty'],
                'unit'          => $item['unit'] ?? null,
                'note'          => $item['note'] ?? null,
            ]);
        }

        return Responses::sendResponse($spb->load('items'), 'SPB Created Successfully');
    }

    /**
     * Approve/Tolak SPB. Hanya Admin. Bisa dipakai ulang untuk SPB yang statusnya
     * "Menunggu Approval" ATAU "Ditolak" (supaya Admin bisa approve ulang SPB yang tadinya ditolak).
     */
    public function approve(Request $request, $id)
    {
        $validator = app('validator')->make($request->all(), ['approve' => 'required|boolean']);
        if ($validator->fails()) {
            return Responses::sendError($validator->errors(), 'Validasi Gagal');
        }

        $spb = Spb::find($id);
        if (!$spb) {
            return Responses::sendError([], 'SPB Not Found');
        }
        if ($spb->status != 'Menunggu Approval' && $spb->status != 'Ditolak') {
            return Responses::sendError([], 'SPB ini sudah masuk tahap pengadaan, tidak bisa diubah lagi dari sini');
        }

        $userData = $this->get();
        $user     = $userData['user'];

        if ($user->role != 'Admin') {
            return Responses::sendError([], 'Hanya Admin yang bisa melakukan approval SPB');
        }

        $approve = $request->input('approve');

        $spb->approved_by   = $user->full_name;
        $spb->approved_at   = Carbon::now();
        $spb->approval_note = $request->input('approval_note');
        $spb->status        = $approve ? 'Permintaan Pengadaan' : 'Ditolak';
        $spb->updated_by    = $user->full_name;
        $spb->save();

        $message = $approve ? 'SPB Approved, lanjut ke Permintaan Pengadaan' : 'SPB Ditolak';
        return Responses::sendResponse($spb, $message);
    }

    /**
     * Tambah penawaran vendor (komparasi). Hanya Admin.
     */
    public function addCondition(Request $request, $id)
    {
        $userData = $this->get();
        $user     = $userData['user'];

        if ($user->role != 'Admin') {
            return Responses::sendError([], 'Hanya Admin yang bisa menambahkan penawaran vendor');
        }

        $spb = Spb::find($id);
        if (!$spb) {
            return Responses::sendError([], 'SPB Not Found');
        }
        if ($spb->status != 'Permintaan Pengadaan') {
            return Responses::sendError([], 'SPB harus berstatus Permintaan Pengadaan untuk menambahkan kondisi');
        }

        $vendorId = $request->input('vendor_id');
        $vendor   = $vendorId ? Vendor::find($vendorId) : null;

        $round = $spb->conditions()->count() + 1;

        $condition = SpbCondition::create([
            'spb_id'         => $spb->id,
            'vendor_id'      => $vendorId,
            'round'          => $round,
            'supplier'       => $vendor ? $vendor->name : $request->input('supplier'),
            'price'          => $request->input('price'),
            'condition_note' => $request->input('condition_note'),
            'selected'       => false,
            'created_by'     => $user->full_name,
        ]);

        return Responses::sendResponse($condition->load('vendor'), 'Penawaran Vendor Berhasil Ditambahkan');
    }

    /**
     * Checklist vendor pemenang. Hanya Admin. Hanya boleh 1 vendor terpilih per SPB.
     */
    public function selectCondition(Request $request, $id)
    {
        $userData = $this->get();
        $user     = $userData['user'];

        if ($user->role != 'Admin') {
            return Responses::sendError([], 'Hanya Admin yang bisa memilih vendor');
        }

        $condition = SpbCondition::find($id);
        if (!$condition) {
            return Responses::sendError([], 'Penawaran Vendor Not Found');
        }

        $spb = Spb::find($condition->spb_id);
        if (!$spb || $spb->status != 'Permintaan Pengadaan') {
            return Responses::sendError([], 'SPB harus berstatus Permintaan Pengadaan');
        }

        SpbCondition::where('spb_id', $spb->id)->update(['selected' => false]);
        $condition->selected = true;
        $condition->save();

        return Responses::sendResponse($condition->load('vendor'), 'Vendor Berhasil Dipilih');
    }

    /**
     * Finalisasi pilihan vendor & lanjut ke Purchasing. Hanya Admin.
     * Kalau "Belum Ada yang Sesuai" (disposisi=false), balik ke Permintaan Pengadaan untuk nego ulang.
     */
    public function disposisi(Request $request, $id)
    {
        $validator = app('validator')->make($request->all(), ['disposisi' => 'required|boolean']);
        if ($validator->fails()) {
            return Responses::sendError($validator->errors(), 'Validasi Gagal');
        }

        $spb = Spb::find($id);
        if (!$spb) {
            return Responses::sendError([], 'SPB Not Found');
        }
        if ($spb->status != 'Permintaan Pengadaan') {
            return Responses::sendError([], 'SPB harus berstatus Permintaan Pengadaan untuk disposisi');
        }
        if ($spb->conditions()->where('selected', true)->count() == 0) {
            return Responses::sendError([], 'Pilih (checklist) salah satu penawaran vendor terlebih dahulu sebelum disposisi');
        }

        $userData = $this->get();
        $user     = $userData['user'];

        if ($user->role != 'Admin') {
            return Responses::sendError([], 'Hanya Admin yang bisa melakukan disposisi');
        }

        $disposisi = $request->input('disposisi');

        $spb->disposisi_by   = $user->full_name;
        $spb->disposisi_at   = Carbon::now();
        $spb->disposisi_note = $request->input('disposisi_note');
        $spb->status         = $disposisi ? 'Disposisi' : 'Permintaan Pengadaan';
        $spb->updated_by     = $user->full_name;
        $spb->save();

        $message = $disposisi ? 'Vendor Final Terpilih, lanjut ke Purchasing untuk Issued PO' : 'Kembali ke Permintaan Pengadaan';
        return Responses::sendResponse($spb, $message);
    }

    /**
     * Terbitkan PO. Hanya Purchasing.
     */
    public function issuePO(Request $request, $id)
    {
        $validator = app('validator')->make($request->all(), [
            'po_number' => 'required',
            'po_date'   => 'required',
            'po_total'  => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return Responses::sendError($validator->errors(), 'Validasi Gagal');
        }

        $spb = Spb::find($id);
        if (!$spb) {
            return Responses::sendError([], 'SPB Not Found');
        }
        if ($spb->status != 'Disposisi') {
            return Responses::sendError([], 'SPB harus berstatus Disposisi (disetujui) untuk menerbitkan PO');
        }

        $userData = $this->get();
        $user     = $userData['user'];

        if ($user->role != 'Purchasing') {
            return Responses::sendError([], 'Hanya akun Purchasing yang bisa menerbitkan PO');
        }

        $spb->po_number   = $request->input('po_number');
        $spb->po_date     = $request->input('po_date');
        $spb->po_supplier = $request->input('po_supplier');
        $spb->po_total    = $request->input('po_total');
        $spb->status      = 'PO Diterbitkan';
        $spb->updated_by  = $user->full_name;
        $spb->save();

        return Responses::sendResponse($spb, 'Purchase Order Berhasil Diterbitkan');
    }

    /**
     * Receive Material. Hanya Purchasing.
     */
    public function resolusi(Request $request, $id)
    {
        $spb = Spb::find($id);
        if (!$spb) {
            return Responses::sendError([], 'SPB Not Found');
        }
        if ($spb->status != 'PO Diterbitkan') {
            return Responses::sendError([], 'SPB harus berstatus PO Diterbitkan untuk resolusi');
        }

        $userData = $this->get();
        $user     = $userData['user'];

        if ($user->role != 'Purchasing') {
            return Responses::sendError([], 'Hanya akun Purchasing yang bisa mencatat Receive Material');
        }

        $spb->resolusi_note = $request->input('resolusi_note');
        $spb->resolusi_at   = Carbon::now();
        $spb->status        = 'Resolusi';
        $spb->updated_by    = $user->full_name;
        $spb->save();

        return Responses::sendResponse($spb, 'Resolusi Berhasil Dicatat');
    }

    /**
     * Catat Invoice. Hanya Purchasing.
     */
    public function invoice(Request $request, $id)
    {
        $validator = app('validator')->make($request->all(), [
            'invoice_number' => 'required',
            'invoice_date'   => 'required',
            'invoice_amount' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return Responses::sendError($validator->errors(), 'Validasi Gagal');
        }

        $spb = Spb::find($id);
        if (!$spb) {
            return Responses::sendError([], 'SPB Not Found');
        }
        if ($spb->status != 'Resolusi') {
            return Responses::sendError([], 'SPB harus berstatus Resolusi untuk mencatat invoice');
        }

        $userData = $this->get();
        $user     = $userData['user'];

        if ($user->role != 'Purchasing') {
            return Responses::sendError([], 'Hanya akun Purchasing yang bisa mencatat Invoice');
        }

        $spb->invoice_number = $request->input('invoice_number');
        $spb->invoice_date   = $request->input('invoice_date');
        $spb->invoice_amount = $request->input('invoice_amount');
        $spb->status         = 'Invoice';
        $spb->updated_by     = $user->full_name;
        $spb->save();

        return Responses::sendResponse($spb, 'Invoice Berhasil Dicatat');
    }

    /**
     * Catat Payment -> SPB Selesai. Hanya Purchasing.
     */
    public function payment(Request $request, $id)
    {
        $validator = app('validator')->make($request->all(), [
            'payment_date'   => 'required',
            'payment_amount' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return Responses::sendError($validator->errors(), 'Validasi Gagal');
        }

        $spb = Spb::find($id);
        if (!$spb) {
            return Responses::sendError([], 'SPB Not Found');
        }
        if ($spb->status != 'Invoice') {
            return Responses::sendError([], 'SPB harus berstatus Invoice untuk mencatat pembayaran');
        }

        $userData = $this->get();
        $user     = $userData['user'];

        if ($user->role != 'Purchasing') {
            return Responses::sendError([], 'Hanya akun Purchasing yang bisa mencatat Payment');
        }

        $spb->payment_date   = $request->input('payment_date');
        $spb->payment_amount = $request->input('payment_amount');
        $spb->payment_method = $request->input('payment_method');
        $spb->status         = 'Selesai';
        $spb->updated_by     = $user->full_name;
        $spb->save();

        return Responses::sendResponse($spb, 'Pembayaran Berhasil Dicatat, SPB Selesai');
    }

    /**
     * Hapus SPB. Hanya Admin.
     */
    public function destroy($id)
    {
        $userData = $this->get();
        $user     = $userData['user'];

        if ($user->role != 'Admin') {
            return Responses::sendError([], 'Hanya Admin yang bisa menghapus SPB');
        }

        $spb = Spb::find($id);
        if (!$spb) {
            return Responses::sendError([], 'SPB Not Found');
        }

        $spb->delete();

        return Responses::sendResponse([], 'SPB Berhasil Dihapus');
    }
}