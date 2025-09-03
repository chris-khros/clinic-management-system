<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Patient;
use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BillingController extends Controller
{
    public function index()
    {
        $bills = Bill::with(['patient', 'doctor', 'appointment'])->paginate(10);

        $total_revenue = Bill::where('payment_status', 'paid')->sum('total_amount');
        $outstanding_revenue = Bill::whereIn('payment_status', ['unpaid', 'partial'])->sum('total_amount');
        $paid_bills_count = Bill::where('payment_status', 'paid')->count();

        return view('billing.index', compact('bills', 'total_revenue', 'outstanding_revenue', 'paid_bills_count'));
    }

    public function create()
    {
        $patients = Patient::where('is_verified', true)->get();
        $services = Service::where('is_active', true)->get();
        $lastBill = Bill::latest('id')->first();
        $new_bill_id = $lastBill ? $lastBill->id + 1 : 1;
        return view('billing.create', compact('patients', 'services', 'new_bill_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'doctor_id' => 'nullable|exists:doctors,id',
            'services' => 'required|array',
            'services.*.service_id' => 'required|exists:services,id',
            'services.*.quantity' => 'required|integer|min:1',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:cash,card,insurance,online',
            'notes' => 'nullable|string',
            'due_date' => 'required|date',
        ]);

        DB::beginTransaction();

        try {
            // Calculate totals
            $subtotal = 0;
            foreach ($request->services as $service) {
                $serviceModel = Service::find($service['service_id']);
                $subtotal += $serviceModel->price * $service['quantity'];
            }

            $taxAmount = $request->tax_amount ?? 0;
            $discountAmount = $request->discount_amount ?? 0;
            $totalAmount = $subtotal + $taxAmount - $discountAmount;

            // Create bill
            $bill = Bill::create([
                'bill_number' => 'BILL' . strtoupper(Str::random(8)),
                'patient_id' => $request->patient_id,
                'appointment_id' => $request->appointment_id,
                'doctor_id' => $request->doctor_id,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'payment_status' => 'pending',
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
                'due_date' => $request->due_date,
            ]);

            // Create bill items
            foreach ($request->services as $service) {
                $serviceModel = Service::find($service['service_id']);
                $unitPrice = $serviceModel->price;
                $quantity = $service['quantity'];
                $totalPrice = $unitPrice * $quantity;

                BillItem::create([
                    'bill_id' => $bill->id,
                    'service_id' => $service['service_id'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ]);
            }

            DB::commit();

            return redirect()->route('billing.show', $bill)->with('success', 'Bill created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create bill. Please try again.']);
        }
    }

    public function show(Bill $bill)
    {
        $bill->load(['patient', 'doctor', 'appointment', 'billItems.service']);
        return view('billing.show', compact('bill'));
    }

    public function edit(Bill $bill)
    {
        $bill->load(['patient', 'billItems.service']);
        $patients = Patient::where('is_verified', true)->get();
        $services = Service::where('is_active', true)->get();
        return view('billing.edit', compact('bill', 'patients', 'services'));
    }

    public function update(Request $request, Bill $bill)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'services' => 'required|array',
            'services.*.service_id' => 'required|exists:services,id',
            'services.*.quantity' => 'required|integer|min:1',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:cash,card,insurance,online',
            'notes' => 'nullable|string',
            'due_date' => 'required|date',
        ]);

        DB::beginTransaction();

        try {
            // Calculate totals
            $subtotal = 0;
            foreach ($request->services as $service) {
                $serviceModel = Service::find($service['service_id']);
                $subtotal += $serviceModel->price * $service['quantity'];
            }

            $taxAmount = $request->tax_amount ?? 0;
            $discountAmount = $request->discount_amount ?? 0;
            $totalAmount = $subtotal + $taxAmount - $discountAmount;

            // Update bill
            $bill->update([
                'patient_id' => $request->patient_id,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
                'due_date' => $request->due_date,
            ]);

            // Delete existing bill items
            $bill->billItems()->delete();

            // Create new bill items
            foreach ($request->services as $service) {
                $serviceModel = Service::find($service['service_id']);
                $unitPrice = $serviceModel->price;
                $quantity = $service['quantity'];
                $totalPrice = $unitPrice * $quantity;

                BillItem::create([
                    'bill_id' => $bill->id,
                    'service_id' => $service['service_id'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ]);
            }

            DB::commit();

            return redirect()->route('billing.show', $bill)->with('success', 'Bill updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to update bill. Please try again.']);
        }
    }

    public function destroy(Bill $bill)
    {
        $bill->billItems()->delete();
        $bill->delete();
        return redirect()->route('billing.index')->with('success', 'Bill deleted successfully.');
    }

    public function markAsPaid(Bill $bill, Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,card,insurance,online',
        ]);

        $bill->update([
            'payment_status' => 'paid',
            'payment_method' => $request->payment_method,
            'paid_at' => now(),
        ]);

        return redirect()->route('billing.show', $bill)->with('success', 'Payment recorded successfully.');
    }

    public function markAsPartial(Bill $bill, Request $request)
    {
        $request->validate([
            'partial_amount' => 'required|numeric|min:0|max:' . $bill->total_amount,
            'payment_method' => 'required|in:cash,card,insurance,online',
        ]);

        $bill->update([
            'payment_status' => 'partial',
            'payment_method' => $request->payment_method,
            'paid_at' => now(),
        ]);

        return redirect()->route('billing.show', $bill)->with('success', 'Partial payment recorded successfully.');
    }

    public function generateInvoice(Bill $bill)
    {
        $bill->load(['patient', 'doctor', 'billItems.service']);

        // Generate PDF invoice (you can use a package like dompdf)
        // For now, we'll just return a view
        return view('billing.invoice', compact('bill'));
    }

    public function reports()
    {
        $monthlyRevenue = Bill::where('payment_status', 'paid')
            ->whereYear('paid_at', now()->year)
            ->whereMonth('paid_at', now()->month)
            ->sum('total_amount');

        $pendingBills = Bill::where('payment_status', 'pending')->count();

        $recentBills = Bill::with(['patient'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('billing.reports', compact('monthlyRevenue', 'pendingBills', 'recentBills'));
    }
}
