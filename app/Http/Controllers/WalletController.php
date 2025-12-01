<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{

    public function credit(int $userId, float $amount, int $ref_id, int $txnType, string $remark = null, $date = null)
    {
        try {
            $response = $this->creditWallet($userId, $amount, $ref_id, $txnType, $remark, $date);
            if (!$response) {
                return ['status' => false, 'message' => 'Amount credit Failed.'];
            }
            return ['status' => true, 'message' => 'Amount credited successfully.'];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function debit(int $userId, float $amount, int $ref_id, int $txnType, string $remark = null, $date = null)
    {
        try {
            $response = $this->debitWallet($userId, $amount, $ref_id, $txnType, $remark, $date);
            if (!$response) {
                return ['status' => false, 'message' => 'Insufficient balance.'];
            }
            return ['status' => true, 'message' => 'Amount debited successful'];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Credit amount to user's wallet.
     *
     * @param int $userId
     * @param float $amount
     * @return bool
     */
    public function creditWallet(int $userId, float $amount, int $ref_id, int $txnType, string $remark = null, $date = null): bool
    {
        return $this->updateWallet($userId, $amount, $ref_id, $txnType, $remark, $date);
    }

    /**
     * Debit amount from user's wallet.
     *
     * @param int $userId
     * @param float $amount
     * @return bool
     */
    public function debitWallet(int $userId, float $amount, int $ref_id, int $txnType, string $remark = null, $date = null): bool
    {
        return $this->updateWallet($userId, -$amount, $ref_id, $txnType, $remark, $date);
    }

    /**
     * Update user's wallet balance.
     *
     * @param int $userId
     * @param float $amount
     * @return bool
     */
    private function updateWallet(int $userId, float $amount, int $ref_id, int $txnType, string $remark = null, $date = null): bool
    {
        DB::beginTransaction();

        try {

            $user = Customer::where('id', $userId)->first();
            $user->balance += $amount;
            $user->save();

            $wallet = new Wallet;
            $wallet->balance = $user->balance;
            $wallet->type = $amount > 0 ? 'CREDIT' : 'DEBIT';
            $wallet->amount = abs($amount);
            $wallet->ref_id = $ref_id;
            $wallet->txn_type_id = $txnType;
            $wallet->remark = $remark;
            $wallet->date = $date;
            $wallet->user_id = $userId;
            $wallet->save();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
