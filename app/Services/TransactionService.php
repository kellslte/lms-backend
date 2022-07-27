<?php
namespace App\Services;

use App\Models\Transaction;

class TransactionService {

    public function __construct(array $data)
    {
        if(!$data['user']->transaction->exists()){
            if(!$data['transactionOk'] && !$data['user']->transaction->exists()){
                // create transaction object
                $data['user']->transation()->create([
                    'email' => $data['user']->email,
                    'amount' => $data['amount'],
                    'status' => 'unpaid'
                ]);
            }

            $data['user']->transation()->create([
                'email' => $data['user']->email,
                'amount' => $data['amount'],
                'status' => 'paid',
                'transaction_id' => $data['transactionId'],
            ]);
        }

        $data['user']->transation()->update([
            'status'
        ]);
    }
}