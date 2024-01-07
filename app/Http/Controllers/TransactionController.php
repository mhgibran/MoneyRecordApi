<?php

namespace App\Http\Controllers;

use App\Helper\ResponseFormatter;
use App\Models\Card;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $data = Transaction::query()
                ->when($request->keyword, function($q) use ($request) {
                    $q->where('trx_number','like','%'.$request->keyword.'%')
                    ->orWhere('description','like','%'.$request->keyword.'%');
                })
                ->when($request->type, function($q) use ($request) {
                    $q->where('type',$request->type);
                })
                ->when($request->start && $request->end, function($q) use ($request) {
                    $start = Carbon::parse($request->start)->format('Y-m-d');
                    $end = Carbon::parse($request->end)->format('Y-m-d');
                    $q->whereBetween('trx_date',[$start,$end]);
                })
                ->when($request->min && $request->max, function($q) use ($request) {
                    $q->whereBetween('amount',[$request->min,$request->max]);
                })
                ->when($request->category, function($q) use ($request) {
                    $q->where('transaction_category_id',$request->category);
                })
                ->get();
        
        return ResponseFormatter::success($data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|numeric|max:3',
            'transaction_category_id' => 'required|exists:\App\Models\TransactionCategory,id',
            'card_source_id' => [Rule::requiredIf($request->type == 0), 'exists:\App\Models\Card,id'],
            'card_target_id' => 'required|exists:\App\Models\Card,id',
            'trx_date' => 'required|date|before:' . date('Y-m-d'),
            'description' => 'nullable|string|max:100',
            'amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error($validator->errors()->all());
        }
        
        try {
            DB::beginTransaction();
            // in transaction
            if ($request->type == 1) {
                $targetCard = Card::lockForUpdate()->find($request->card_target_id);
                $targetCard->update([
                    'balance' => $targetCard->balance + $request->amount 
                ]);
            }

            // out transaction
            if ($request->type == 2) {
                $targetCard = Card::lockForUpdate()->find($request->card_target_id);
                
                if ($targetCard->balance < $request->amount) {
                    return ResponseFormatter::error(null, 400, 'Saldo is not enough');                
                }

                $targetCard->update([
                    'balance' => $targetCard->balance - $request->amount 
                ]);
            }

            // transfer transaction
            if ($request->type == 3) {
                $sourceCard = Card::lockForUpdate()->find($request->card_source_id);
                if ($sourceCard->balance < $request->amount) {
                    return ResponseFormatter::error(null, 400, 'Saldo is not enough');                
                }
                $sourceCard->update([
                    'balance' => $sourceCard->balance - $request->amount 
                ]);

                $targetCard = Card::lockForUpdate()->find($request->card_target_id);
                $targetCard->update([
                    'balance' => $targetCard->balance + $request->amount 
                ]);
            }
            
            $data = Transaction::create(array_merge($validator->validated(), [
                'transaction_category_id' => $request->transaction_category_id,
                'card_source_id' => $request->type == 3 ? $request->card_source_id : null,
                'card_target_id' => $request->card_target_id,
                'type' => $request->type,
                'trx_date' => date('Y-m-d', strtotime($request->trx_date)),
                'trx_number' => strtoupper(uniqid()),
                'description' => $request->description,
                'amount' => $request->amount,
            ]));

            DB::commit();
            return ResponseFormatter::success($data, 201, 'Transaction successfully saved');
        } catch (\Exception $errors) {
            DB::rollback();
            return ResponseFormatter::error(null, 500, 'Something when wrong, please try again');
        }
    }

    public function show($id)
    {
        $data = Transaction::find($id);

        if (!$data) {
            return ResponseFormatter::error(null, 401, 'Data Not Found');
        }
        
        return ResponseFormatter::success($data);
    }
}
